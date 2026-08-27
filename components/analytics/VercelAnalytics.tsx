'use client';

import { Analytics } from '@vercel/analytics/react';

export function VercelAnalytics() {
  return (
    <Analytics
      beforeSend={(event) => {
        // Ensure this is running in the client browser
        if (typeof navigator !== 'undefined') {
          // 1. Block automated headless browsers (Selenium, Puppeteer, Playwright)
          if (navigator.webdriver) {
            return null; // Drops the event, saving your Vercel quota
          }

          // 2. Block known bot, crawler, and AI user agents
          const botRegex = /bot|crawler|spider|crawling|headless|chrome-lighthouse/i;
          if (botRegex.test(navigator.userAgent)) {
            return null;
          }
        }

        // If it passes the checks, it's a real human. Send the event!
        return event;
      }}
    />
  );
}
