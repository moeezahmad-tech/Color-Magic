const dataService = require('./dataService');

const UNIPOST_API_BASE = 'https://api.unipost.dev/v1';

// Default Pinterest Boards
const BOARDS = {
  palettes: process.env.PINTEREST_BOARD_PALETTES || '1092193415823453337',
  gradients: process.env.PINTEREST_BOARD_GRADIENTS || '1092193415823453436',
  colors: process.env.PINTEREST_BOARD_COLORS || '1092193415823453337'
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
 * Dispatch post to UniPost
 */
async function postToUniPost({ title, description, link, boardId, imageUrl }) {
  const accountId = getAccountId();
  const payload = {
    caption: description,
    account_ids: [accountId],
    media_urls: [imageUrl],
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
  const firstResult = postData.results?.[0];

  // If UniPost preflight rejected the post immediately
  if (firstResult && firstResult.status === 'failed') {
    throw new Error(firstResult.error_message || 'UniPost failed to publish to Pinterest');
  }

  const pinId = firstResult?.external_id || postData.id || `unipost_${Date.now()}`;
  return {
    success: true,
    pin_id: pinId,
    post_id: postData.id,
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
