const dataService = require('./dataService');

const GRAPH_API_BASE = 'https://graph.facebook.com/v19.0';

function getCredentials() {
  return {
    accountId: process.env.INSTAGRAM_ACCOUNT_ID || '',
    accessToken: process.env.INSTAGRAM_ACCESS_TOKEN || ''
  };
}

async function request(endpoint, method = 'GET', params = {}) {
  const { accessToken } = getCredentials();
  const url = endpoint.startsWith('http')
    ? endpoint
    : `${GRAPH_API_BASE}/${endpoint.replace(/^\//, '')}`;

  const queryParams = new URLSearchParams();
  if (method === 'GET') {
    if (accessToken) queryParams.set('access_token', accessToken);
    for (const [k, v] of Object.entries(params)) {
      queryParams.set(k, v);
    }
    const fullUrl = `${url}?${queryParams.toString()}`;
    const res = await fetch(fullUrl);
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      throw new Error(data.error?.message || `Meta API HTTP ${res.status}`);
    }
    return data;
  } else {
    const bodyParams = new URLSearchParams();
    if (accessToken) bodyParams.set('access_token', accessToken);
    for (const [k, v] of Object.entries(params)) {
      bodyParams.set(k, v);
    }
    const res = await fetch(url, {
      method: 'POST',
      body: bodyParams
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      throw new Error(data.error?.message || `Meta API HTTP ${res.status}`);
    }
    return data;
  }
}

/**
 * Publish Image Container to Instagram
 */
async function publishContainer(imageUrl, caption) {
  const { accountId, accessToken } = getCredentials();

  if (!accountId || !accessToken) {
    throw new Error('Instagram is not configured. Please add INSTAGRAM_ACCOUNT_ID and INSTAGRAM_ACCESS_TOKEN to Dashboard_App/.env');
  }

  // 1. Create Media Container
  const createRes = await request(`${accountId}/media`, 'POST', {
    image_url: imageUrl,
    caption
  });

  if (!createRes?.id) {
    throw new Error('Failed to create Instagram media container');
  }

  const containerId = createRes.id;

  // 2. Wait 2 seconds for Instagram processing
  await new Promise(r => setTimeout(r, 2000));

  // 3. Publish container
  const publishRes = await request(`${accountId}/media_publish`, 'POST', {
    creation_id: containerId
  });

  const mediaId = publishRes?.id;

  return {
    success: true,
    media_id: mediaId,
    container_id: containerId
  };
}

/**
 * Post Palette to Instagram
 */
async function postPalette(id) {
  const palettes = await dataService.getPalettes();
  const palette = palettes.find(p => String(p.id) === String(id));
  if (!palette) {
    throw new Error(`Palette '${id}' not found`);
  }

  const name = palette.name || 'Curated Color Scheme';
  const colors = palette.colors || [];
  const colorList = colors.join('  •  ');
  const slug = (palette.slug || name.toLowerCase().replace(/[^a-z0-9]+/g, '-')).replace(/^-|-$/g, '');

  const caption = `✨ ${name} Palette ✨\n\nColors:\n${colorList}\n\nExplore this color palette & hex codes on Color Magic:\nhttps://colormagic.techkreative.com/palette/${slug}\n\n#colormagic #colorpalette #designinspiration #graphicdesign #colorscheme #uidesign #webdesign`;
  const posterUrl = `https://colormagic.techkreative.com/posters/palettes/${slug}.jpg`;

  const result = await publishContainer(posterUrl, caption);

  // Update dataset
  await dataService.updatePalette(id, {
    instagram_posted: true,
    instagram_post_id: result.media_id,
    instagram_posted_at: new Date().toISOString()
  });

  return {
    success: true,
    id,
    title: name,
    media_id: result.media_id
  };
}

/**
 * Post Gradient to Instagram
 */
async function postGradient(id) {
  const gradients = await dataService.getGradients();
  const gradient = gradients.find(g => String(g.id) === String(id));
  if (!gradient) {
    throw new Error(`Gradient '${id}' not found`);
  }

  const name = gradient.name || 'CSS Gradient';
  const colors = gradient.colors || [];
  const colorList = colors.join('  •  ');
  const css = gradient.css || '';

  const caption = `🌈 ${name} Gradient 🌈\n\nCSS: ${css}\nColors: ${colorList}\n\nGrab full CSS codes & presets at Color Magic:\nhttps://colormagic.techkreative.com/gradient/${gradient.id}\n\n#colormagic #cssgradient #webdevelopment #frontend #uiux #modernweb`;
  const posterUrl = `https://colormagic.techkreative.com/posters/gradients/${gradient.id}.jpg`;

  const result = await publishContainer(posterUrl, caption);

  // Update dataset
  await dataService.updateGradient(id, {
    instagram_posted: true,
    instagram_post_id: result.media_id,
    instagram_posted_at: new Date().toISOString()
  });

  return {
    success: true,
    id,
    title: name,
    media_id: result.media_id
  };
}

/**
 * Post Color to Instagram
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

  const caption = `🎨 ${name} (${hex})\n\nDiscover color psychology, harmonies, and contrast ratios for ${name} on Color Magic:\nhttps://colormagic.techkreative.com/color/${slug}\n\n#colormagic #colorOfTheDay #hexcode #designinspo #colorcontrast`;
  const posterUrl = `https://colormagic.techkreative.com/posters/colors/${clean}.jpg`;

  const result = await publishContainer(posterUrl, caption);

  // Update dataset
  await dataService.updateColor(clean, {
    instagram_posted: true,
    instagram_post_id: result.media_id,
    instagram_posted_at: new Date().toISOString()
  });

  return {
    success: true,
    id: clean,
    title: name,
    media_id: result.media_id
  };
}

/**
 * Check connection status
 */
async function getStatus() {
  const { accountId, accessToken } = getCredentials();
  if (!accountId || !accessToken) {
    return {
      connected: false,
      error: 'INSTAGRAM_ACCOUNT_ID or INSTAGRAM_ACCESS_TOKEN is missing in .env'
    };
  }

  try {
    const data = await request(`${accountId}?fields=id,username,name,profile_picture_url`);
    return {
      connected: true,
      account_id: data.id,
      username: data.username || 'instagram_account',
      name: data.name || '',
      profile_picture_url: data.profile_picture_url || ''
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
  getStatus
};
