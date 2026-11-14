require('dotenv').config();
const express = require('express');
const cors = require('cors');
const path = require('path');
const apiRoutes = require('./routes/api');

const app = express();
const DEFAULT_PORT = parseInt(process.env.PORT, 10) || 3001;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve static assets from public folder
app.use(express.static(path.join(__dirname, 'public')));

// Mount API routes
app.use('/api', apiRoutes);

// Clean Page Routes
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.get('/palettes', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'palettes.html'));
});

app.get('/gradients', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'gradients.html'));
});

app.get('/colors', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'colors.html'));
});

// 404 Fallback
app.use((req, res) => {
  res.status(404).sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Global error handler
app.use((err, req, res, next) => {
  console.error('Server error:', err);
  res.status(500).json({ success: false, error: err.message || 'Internal server error' });
});

// Start Server with automatic port fallback
function startServer(port) {
  const server = app.listen(port, () => {
    console.log(`\n======================================================`);
    console.log(`  Color Magic — Social Media Publishing Dashboard`);
    console.log(`  Running on: http://localhost:${port}`);
    console.log(`  Pinterest: UniPost API v5`);
    console.log(`  Instagram: Meta Graph API v19.0`);
    console.log(`======================================================\n`);
  });

  server.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
      console.log(`Port ${port} is in use, trying port ${port + 1}...`);
      startServer(port + 1);
    } else {
      console.error('Server error:', err);
    }
  });
}

startServer(DEFAULT_PORT);
