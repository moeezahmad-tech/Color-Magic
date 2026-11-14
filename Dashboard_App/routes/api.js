const express = require('express');
const router = express.Router();
const dataService = require('../services/dataService');
const pinterestService = require('../services/pinterest');
const instagramService = require('../services/instagram');

// ── GET OVERVIEW STATS ──────────────────────────────────────────────────────
router.get('/stats', async (req, res) => {
  try {
    const stats = await dataService.getStats();
    res.json({ success: true, stats });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// ── GET SETTINGS & CONNECTION STATUS ────────────────────────────────────────
router.get('/settings', async (req, res) => {
  try {
    const [pinterest, instagram] = await Promise.all([
      pinterestService.getStatus(),
      instagramService.getStatus()
    ]);
    res.json({
      success: true,
      connections: {
        pinterest,
        instagram
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// ── GET PALETTES ────────────────────────────────────────────────────────────
router.get('/palettes', async (req, res) => {
  try {
    const { page = 1, limit = 24, search = '', style = '', filter = 'all' } = req.query;
    const pageNum = Math.max(1, parseInt(page, 10));
    const limitNum = Math.min(100, Math.max(1, parseInt(limit, 10)));

    let list = await dataService.getPalettes();

    // Search query
    if (search) {
      const q = search.toLowerCase().trim();
      list = list.filter(p =>
        (p.name && p.name.toLowerCase().includes(q)) ||
        (p.colors && p.colors.some(c => c.toLowerCase().includes(q))) ||
        (p.style && p.style.toLowerCase().includes(q))
      );
    }

    // Style filter
    if (style) {
      const s = style.toLowerCase();
      list = list.filter(p => p.style && p.style.toLowerCase() === s);
    }

    // Upload status filter
    if (filter === 'unposted') {
      list = list.filter(p => !p.uploaded && !p.pinterest_pin_id);
    } else if (filter === 'posted') {
      list = list.filter(p => p.uploaded || p.pinterest_pin_id);
    } else if (filter === 'ig_unposted') {
      list = list.filter(p => !p.instagram_posted && !p.instagram_post_id);
    } else if (filter === 'ig_posted') {
      list = list.filter(p => p.instagram_posted || p.instagram_post_id);
    }

    const total = list.length;
    const totalPages = Math.ceil(total / limitNum) || 1;
    const start = (pageNum - 1) * limitNum;
    const items = list.slice(start, start + limitNum);

    res.json({
      success: true,
      data: items,
      pagination: {
        page: pageNum,
        limit: limitNum,
        total,
        totalPages
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// ── GET GRADIENTS ───────────────────────────────────────────────────────────
router.get('/gradients', async (req, res) => {
  try {
    const { page = 1, limit = 24, search = '', type = '', style = '', filter = 'all' } = req.query;
    const pageNum = Math.max(1, parseInt(page, 10));
    const limitNum = Math.min(100, Math.max(1, parseInt(limit, 10)));

    let list = await dataService.getGradients();

    if (search) {
      const q = search.toLowerCase().trim();
      list = list.filter(g =>
        (g.name && g.name.toLowerCase().includes(q)) ||
        (g.css && g.css.toLowerCase().includes(q)) ||
        (g.colors && g.colors.some(c => c.toLowerCase().includes(q))) ||
        (g.style && g.style.toLowerCase().includes(q))
      );
    }

    if (type) {
      const t = type.toLowerCase();
      list = list.filter(g => g.type && g.type.toLowerCase() === t);
    }

    if (style) {
      const s = style.toLowerCase();
      list = list.filter(g => g.style && g.style.toLowerCase() === s);
    }

    if (filter === 'unposted') {
      list = list.filter(g => !g.uploaded && !g.pinterest_pin_id);
    } else if (filter === 'posted') {
      list = list.filter(g => g.uploaded || g.pinterest_pin_id);
    } else if (filter === 'ig_unposted') {
      list = list.filter(g => !g.instagram_posted && !g.instagram_post_id);
    } else if (filter === 'ig_posted') {
      list = list.filter(g => g.instagram_posted || g.instagram_post_id);
    }

    const total = list.length;
    const totalPages = Math.ceil(total / limitNum) || 1;
    const start = (pageNum - 1) * limitNum;
    const items = list.slice(start, start + limitNum);

    res.json({
      success: true,
      data: items,
      pagination: {
        page: pageNum,
        limit: limitNum,
        total,
        totalPages
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// ── GET COLORS ──────────────────────────────────────────────────────────────
router.get('/colors', async (req, res) => {
  try {
    const { page = 1, limit = 24, search = '', filter = 'all' } = req.query;
    const pageNum = Math.max(1, parseInt(page, 10));
    const limitNum = Math.min(100, Math.max(1, parseInt(limit, 10)));

    const colorsRaw = await dataService.getColors();
    let list = Object.entries(colorsRaw).map(([key, val]) => ({
      key,
      hex: val.hex || key,
      name: val.name || ('#' + val.hex),
      slug: val.slug || val.hex,
      uploaded: !!(val.uploaded || val.pinterest_pin_id),
      pinterest_pin_id: val.pinterest_pin_id || null,
      pinterest_posted_at: val.pinterest_posted_at || null,
      instagram_posted: !!(val.instagram_posted || val.instagram_post_id),
      instagram_post_id: val.instagram_post_id || null
    }));

    if (search) {
      const q = search.toLowerCase().trim();
      list = list.filter(c =>
        c.name.toLowerCase().includes(q) ||
        c.hex.toLowerCase().includes(q) ||
        c.slug.toLowerCase().includes(q)
      );
    }

    if (filter === 'unposted') {
      list = list.filter(c => !c.uploaded);
    } else if (filter === 'posted') {
      list = list.filter(c => c.uploaded);
    } else if (filter === 'ig_unposted') {
      list = list.filter(c => !c.instagram_posted);
    } else if (filter === 'ig_posted') {
      list = list.filter(c => c.instagram_posted);
    }

    const total = list.length;
    const totalPages = Math.ceil(total / limitNum) || 1;
    const start = (pageNum - 1) * limitNum;
    const items = list.slice(start, start + limitNum);

    res.json({
      success: true,
      data: items,
      pagination: {
        page: pageNum,
        limit: limitNum,
        total,
        totalPages
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// ── POST TO PINTEREST (VIA UNIPOST) ─────────────────────────────────────────
router.post('/post/pinterest', async (req, res) => {
  try {
    const { type, id } = req.body;
    if (!type || !id) {
      return res.status(400).json({ success: false, error: 'type and id are required' });
    }

    let result;
    if (type === 'palette') {
      result = await pinterestService.postPalette(id);
    } else if (type === 'gradient') {
      result = await pinterestService.postGradient(id);
    } else if (type === 'color') {
      result = await pinterestService.postColor(id);
    } else {
      return res.status(400).json({ success: false, error: `Invalid type '${type}'` });
    }

    res.json(result);
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// ── POST TO INSTAGRAM (VIA GRAPH API) ───────────────────────────────────────
router.post('/post/instagram', async (req, res) => {
  try {
    const { type, id } = req.body;
    if (!type || !id) {
      return res.status(400).json({ success: false, error: 'type and id are required' });
    }

    let result;
    if (type === 'palette') {
      result = await instagramService.postPalette(id);
    } else if (type === 'gradient') {
      result = await instagramService.postGradient(id);
    } else if (type === 'color') {
      result = await instagramService.postColor(id);
    } else {
      return res.status(400).json({ success: false, error: `Invalid type '${type}'` });
    }

    res.json(result);
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

// ── BATCH AUTO-POST TO PINTEREST ────────────────────────────────────────────
router.post('/batch/pinterest', async (req, res) => {
  try {
    const { type = 'palette' } = req.body;
    let nextItem = null;

    if (type === 'palette') {
      const list = await dataService.getPalettes();
      nextItem = list.find(p => !p.uploaded && !p.pinterest_pin_id);
      if (!nextItem) {
        return res.json({ success: true, finished: true, message: 'All palettes have been published!' });
      }
      const result = await pinterestService.postPalette(nextItem.id);
      return res.json({ success: true, item: nextItem, result });
    } else if (type === 'gradient') {
      const list = await dataService.getGradients();
      nextItem = list.find(g => !g.uploaded && !g.pinterest_pin_id);
      if (!nextItem) {
        return res.json({ success: true, finished: true, message: 'All gradients have been published!' });
      }
      const result = await pinterestService.postGradient(nextItem.id);
      return res.json({ success: true, item: nextItem, result });
    }

    res.status(400).json({ success: false, error: `Batch posting for '${type}' is not supported` });
  } catch (err) {
    res.status(500).json({ success: false, error: err.message });
  }
});

module.exports = router;
