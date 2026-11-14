const dataService = require('./dataService');

const UNIPOST_API_BASE = 'https://api.unipost.dev/v1';

// Default Pinterest Boards
const BOARDS = {
  palettes: process.env.PINTEREST_BOARD_PALETTES || '1092193415823453337',
  gradients: process.env.PINTEREST_BOARD_GRADIENTS || '1092193415823453436',
  colors: process.env.PINTEREST_BOARD_COLORS || '1092193415823453337',
  main: process.env.PINTEREST_BOARD_MAIN || '1092193415823453337'
};

function getHeaders() {
  const key = process.env.UNIPOST_KEY;
  if (!key) {
    throw new Error('UNIPOST_KEY is missing in Dashboard_App/.env');
  }
  return {
    'Authorization': `Bearer ${key}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  };
}

function getAccountId() {
  return process.env.UNIPOST_PINTEREST_ACCOUNT_ID || 'e8a6759b-e0d3-4118-8c59-e8e5bf990171';
}

/**
 * Upload poster bytes directly to UniPost R2 media storage
 * Bypasses crawler blocks and chunked encoding issues
 */
async function uploadMedia(imageUrl) {
  try {
    const imgRes = await fetch(imageUrl);
    if (!imgRes.ok) return imageUrl;
    const buffer = await imgRes.arrayBuffer();

    const initRes = await fetch(`${UNIPOST_API_BASE}/media`, {
      method: 'POST',
      headers: getHeaders(),
      body: JSON.stringify({
        filename: 'poster.jpg',
        content_type: 'image/jpeg'
      })
    });
    if (!initRes.ok) return imageUrl;
    const initJson = await initRes.json();
    const { id, upload_url } = initJson.data || {};
    if (!upload_url) return imageUrl;

    const putRes = await fetch(upload_url, {
      method: 'PUT',
      headers: {
        'Content-Type': 'image/jpeg',
        'Content-Length': buffer.byteLength.toString()
      },
      body: buffer
    });
    if (!putRes.ok) return imageUrl;

    const getRes = await fetch(`${UNIPOST_API_BASE}/media/${id}`, {
      headers: getHeaders()
    });
    const getJson = await getRes.json();
    return getJson.data?.download_url || imageUrl;
  } catch (err) {
    console.warn('Direct media upload fallback to URL:', err.message);
    return imageUrl;
  }
}

/**
 * Dispatch post to UniPost
 */
async function postToUniPost({ title, description, link, boardId, imageUrl }) {
  const accountId = getAccountId();
  const effectiveImageUrl = await uploadMedia(imageUrl);

  const payload = {
    caption: description,
    account_ids: [accountId],
    media_urls: [effectiveImageUrl],
    platform_options: {
      pinterest: {
        title,
        board_id: boardId,
        link
      }
    }
  };

  const res = await fetch(`${UNIPOST_API_BASE}/posts`, {
    method: 'POST',
    headers: getHeaders(),
    body: JSON.stringify(payload)
  });

  const data = await res.json().catch(() => ({}));

  if (!res.ok) {
    const errMsg = data.error?.message || data.message || `UniPost API HTTP ${res.status}`;
    throw new Error(errMsg);
  }

  const postData = data.data || data;
  const postId = postData.id;

  // Poll UniPost to wait for Pinterest worker completion (up to ~10s)
  let attempts = 0;
  const maxAttempts = 7;
  let finalStatus = postData.status || 'queued';
  let firstResult = postData.results?.[0];

  while (attempts < maxAttempts) {
    if (finalStatus === 'failed') {
      const err = firstResult?.error_message || 'Pinterest rejected the media or request';
      const reason = firstResult?.platform_error_code || firstResult?.provider_error?.reason || '';
      throw new Error(`${err}${reason ? ` [${reason}]` : ''}`);
    }

    if (finalStatus === 'published' || finalStatus === 'completed') {
      const pinId = firstResult?.external_id || postId;
      return {
        success: true,
        pin_id: pinId,
        post_id: postId,
        data: postData
      };
    }

    // Wait 1.5 seconds between status checks
    await new Promise(r => setTimeout(r, 1500));
    attempts++;

    try {
      const pollRes = await fetch(`${UNIPOST_API_BASE}/posts/${postId}`, {
        headers: getHeaders()
      });
      const pollJson = await pollRes.json();
      const pollData = pollJson.data || pollJson;
      finalStatus = pollData.status;
      firstResult = pollData.results?.[0];
    } catch (e) {
      console.warn(`Polling error on attempt ${attempts}:`, e.message);
    }
  }

  // If still failed or ended with failure
  if (finalStatus === 'failed') {
    const err = firstResult?.error_message || 'Pinterest rejected the request';
    const reason = firstResult?.platform_error_code || firstResult?.provider_error?.reason || '';
    throw new Error(`${err}${reason ? ` [${reason}]` : ''}`);
  }

  // If status is published
  const pinId = firstResult?.external_id || postId;
  return {
    success: true,
    pin_id: pinId,
    post_id: postId,
    data: postData
  };
}

/**
 * Post Palette to Pinterest
 */
async function postPalette(id) {
  const palettes = await dataService.getPalettes();
  const palette = palettes.find(p => String(p.id) === String(id));
  if (!palette) {
    throw new Error(`Palette '${id}' not found`);
  }

  const name = palette.name || 'Curated Color Scheme';
  const style = palette.style || 'Design';
  const colors = palette.colors || [];
  const colorList = colors.join(', ');
  const slug = (palette.slug || name.toLowerCase().replace(/[^a-z0-9]+/g, '-')).replace(/^-|-$/g, '');

  const title = `${name} Color Palette — Color Magic`;
  const description = `${name}: A curated ${style} color palette with hex codes ${colorList}. Generate palettes, check contrast, and explore hex conversions at Color Magic.`;
  const link = `https://colormagic.techkreative.com/palette/${slug}`;
  const posterUrl = `https://colormagic.techkreative.com/posters/palettes/${slug}.jpg`;

  const result = await postToUniPost({
    title,
    description,
    link,
    boardId: BOARDS.palettes,
    imageUrl: posterUrl
  });

  // Update local dataset
  await dataService.updatePalette(id, {
    uploaded: true,
    pinterest_pin_id: result.pin_id,
    pinterest_posted_at: new Date().toISOString(),
    pinterest_board_id: BOARDS.palettes
  });

  return {
    success: true,
    id,
    title,
    pin_id: result.pin_id,
    link
  };
}

/**
 * Post Gradient to Pinterest
 */
async function postGradient(id) {
  const gradients = await dataService.getGradients();
  const gradient = gradients.find(g => String(g.id) === String(id));
  if (!gradient) {
    throw new Error(`Gradient '${id}' not found`);
  }

  const name = gradient.name || 'CSS Gradient';
  const style = gradient.style || 'Modern';
  const type = (gradient.type || 'LINEAR').toUpperCase();
  const css = gradient.css || '';
  const colors = gradient.colors || [];
  const colorList = colors.join(', ');

  const title = `${name} ${type} Gradient — Color Magic`;
  const description = `CSS code: ${css}. Colors: ${colorList}. Curated ${style} gradient for modern web & UI design from Color Magic.`;
  const link = `https://colormagic.techkreative.com/gradient/${gradient.id}`;
  const posterUrl = `https://colormagic.techkreative.com/posters/gradients/${gradient.id}.jpg`;

  const result = await postToUniPost({
    title,
    description,
    link,
    boardId: BOARDS.gradients,
    imageUrl: posterUrl
  });

  // Update local dataset
  await dataService.updateGradient(id, {
    uploaded: true,
    pinterest_pin_id: result.pin_id,
    pinterest_posted_at: new Date().toISOString(),
    pinterest_board_id: BOARDS.gradients
  });

  return {
    success: true,
    id,
    title,
    pin_id: result.pin_id,
    link
  };
}

/**
 * Post Color to Pinterest
 */
async function postColor(hexOrSlug) {
  const colorsRaw = await dataService.getColors();
  const clean = String(hexOrSlug).toLowerCase().replace(/^#/, '');

  let matched = null;
  for (const [key, item] of Object.entries(colorsRaw)) {
    const h = String(item.hex || key).toLowerCase().replace(/^#/, '');
    const s = String(item.slug || '').toLowerCase();
    if (h === clean || s === clean) {
      matched = item;
      break;
    }
  }

  if (!matched) {
    throw new Error(`Color '${hexOrSlug}' not found`);
  }

  const hex = '#' + String(matched.hex || clean).toUpperCase().replace(/^#/, '');
  const name = matched.name || hex;
  const slug = matched.slug || clean;

  const title = `${name} (${hex}) Color Meaning & Codes — Color Magic`;
  const description = `${name} (${hex}): Explore hex, RGB, CMYK and HSL color values, visual harmonies, and contrast ratios at Color Magic.`;
  const link = `https://colormagic.techkreative.com/color/${slug}`;
  const posterUrl = `https://colormagic.techkreative.com/posters/colors/${clean}.jpg`;

  const result = await postToUniPost({
    title,
    description,
    link,
    boardId: BOARDS.colors,
    imageUrl: posterUrl
  });

  // Update local dataset
  await dataService.updateColor(clean, {
    uploaded: true,
    pinterest_pin_id: result.pin_id,
    pinterest_posted_at: new Date().toISOString(),
    pinterest_board_id: BOARDS.colors
  });

  return {
    success: true,
    id: clean,
    title,
    pin_id: result.pin_id,
    link
  };
}

/**
 * Check connection status and accounts from UniPost
 */
async function getStatus() {
  const key = process.env.UNIPOST_KEY;
  if (!key) {
    return {
      connected: false,
      error: 'UNIPOST_KEY not configured in .env'
    };
  }

  try {
    const res = await fetch(`${UNIPOST_API_BASE}/accounts`, {
      headers: getHeaders()
    });
    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      return {
        connected: false,
        error: data.message || `UniPost API error: HTTP ${res.status}`
      };
    }

    const accounts = Array.isArray(data.data) ? data.data : [];
    const targetAccountId = getAccountId();
    const pinterestAccount = accounts.find(a => a.id === targetAccountId || a.platform === 'pinterest');

    return {
      connected: !!pinterestAccount,
      account_id: pinterestAccount?.id || targetAccountId,
      username: pinterestAccount?.name || pinterestAccount?.username || 'Pinterest Account',
      platform: 'pinterest',
      status: pinterestAccount?.status || 'active',
      all_accounts: accounts
    };
  } catch (err) {
    return {
      connected: false,
      error: err.message
    };
  }
}

module.exports = {
  postPalette,
  postGradient,
  postColor,
  getStatus,
  BOARDS
};

