const fs = require('fs').promises;
const path = require('path');

const DATA_DIR = path.resolve(__dirname, '../../data');
const PALETTES_FILE = path.join(DATA_DIR, 'palettes.json');
const GRADIENTS_FILE = path.join(DATA_DIR, 'gradients.json');
const COLORS_FILE = path.join(DATA_DIR, 'color-names.json');

async function readJson(filePath, fallback = []) {
  try {
    const raw = await fs.readFile(filePath, 'utf-8');
    return JSON.parse(raw);
  } catch (err) {
    console.error(`Error reading ${filePath}:`, err.message);
    return fallback;
  }
}

async function writeJson(filePath, data) {
  await fs.writeFile(filePath, JSON.stringify(data, null, 2), 'utf-8');
}

// ── PALETTES ────────────────────────────────────────────────────────────────
async function getPalettes() {
  return await readJson(PALETTES_FILE, []);
}

async function updatePalette(id, updates) {
  const palettes = await getPalettes();
  const idx = palettes.findIndex(p => String(p.id) === String(id));
  if (idx === -1) return null;

  palettes[idx] = { ...palettes[idx], ...updates };
  await writeJson(PALETTES_FILE, palettes);
  return palettes[idx];
}

// ── GRADIENTS ───────────────────────────────────────────────────────────────
async function getGradients() {
  return await readJson(GRADIENTS_FILE, []);
}

async function updateGradient(id, updates) {
  const gradients = await getGradients();
  const idx = gradients.findIndex(g => String(g.id) === String(id));
  if (idx === -1) return null;

  gradients[idx] = { ...gradients[idx], ...updates };
  await writeJson(GRADIENTS_FILE, gradients);
  return gradients[idx];
}

// ── COLORS ──────────────────────────────────────────────────────────────────
async function getColors() {
  return await readJson(COLORS_FILE, {});
}

async function updateColor(hexOrSlug, updates) {
  const colors = await getColors();
  const clean = String(hexOrSlug).toLowerCase().replace(/^#/, '');

  let matchedKey = null;
  for (const [key, item] of Object.entries(colors)) {
    const h = String(item.hex || key).toLowerCase().replace(/^#/, '');
    const s = String(item.slug || '').toLowerCase();
    if (h === clean || s === clean) {
      matchedKey = key;
      break;
    }
  }

  if (!matchedKey) return null;

  colors[matchedKey] = { ...colors[matchedKey], ...updates };
  await writeJson(COLORS_FILE, colors);
  return colors[matchedKey];
}

// ── OVERVIEW STATS ──────────────────────────────────────────────────────────
async function getStats() {
  const [palettes, gradients, colorsRaw] = await Promise.all([
    getPalettes(),
    getGradients(),
    getColors()
  ]);

  const colorItems = Object.values(colorsRaw);

  const palettesUploaded = palettes.filter(p => p.uploaded || p.pinterest_pin_id).length;
  const gradientsUploaded = gradients.filter(g => g.uploaded || g.pinterest_pin_id).length;
  const colorsUploaded = colorItems.filter(c => c.uploaded || c.pinterest_pin_id).length;

  const palettesIgUploaded = palettes.filter(p => p.instagram_posted || p.instagram_post_id).length;
  const gradientsIgUploaded = gradients.filter(g => g.instagram_posted || g.instagram_post_id).length;
  const colorsIgUploaded = colorItems.filter(c => c.instagram_posted || c.instagram_post_id).length;

  return {
    palettes: {
      total: palettes.length,
      uploaded: palettesUploaded,
      remaining: palettes.length - palettesUploaded,
      instagramUploaded: palettesIgUploaded
    },
    gradients: {
      total: gradients.length,
      uploaded: gradientsUploaded,
      remaining: gradients.length - gradientsUploaded,
      instagramUploaded: gradientsIgUploaded
    },
    colors: {
      total: colorItems.length,
      uploaded: colorsUploaded,
      remaining: colorItems.length - colorsUploaded,
      instagramUploaded: colorsIgUploaded
    },
    totals: {
      total: palettes.length + gradients.length + colorItems.length,
      pinterestUploaded: palettesUploaded + gradientsUploaded + colorsUploaded,
      instagramUploaded: palettesIgUploaded + gradientsIgUploaded + colorsIgUploaded
    }
  };
}

module.exports = {
  getPalettes,
  updatePalette,
  getGradients,
  updateGradient,
  getColors,
  updateColor,
  getStats
};

