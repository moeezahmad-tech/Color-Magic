import React from 'react';

/**
 * Strict TypeScript interfaces for data structure & API payload
 */
export interface ColorData {
  id: string;
  name: string;
  style: string;
  colors: string[];
  tags: string[];
  likes?: number;
}

export interface ApiResponse<T> {
  data: T;
  status: number;
  message?: string;
}

/**
 * Data fetcher function configured with Next.js App Router ISR & Cache Tags
 * 
 * Caching Lifecycle:
 * 1. Initial Request (Build/Runtime): Next.js makes the API request and writes the result to the Data Cache.
 * 2. Within 60 Seconds: Any visitor gets the instantaneously cached response (HIT).
 * 3. After 60 Seconds (Time-Based ISR): The next visitor is served the stale cached response (STALE)
 *    while Next.js triggers a background re-fetch in the background to update the cache (REVALIDATE).
 * 4. On-Demand Invalidation: When /api/revalidate is called with tag 'my-color-data', the cache is
 *    immediately purged without waiting for the 60s timer to expire.
 */
async function fetchCuratedColors(): Promise<ColorData[] | null> {
  const API_URL = 'https://api.colormagic.techkreative.com/palettes';

  try {
    // AbortSignal.timeout sets a 5-second timeout window to prevent hanging requests
    const res = await fetch(API_URL, {
      next: {
        revalidate: 60, // Time-based ISR: Cache entry lives for 60 seconds
        tags: ['my-color-data', 'palettes'], // Cache tag for on-demand revalidation via revalidateTag()
      },
      headers: {
        'Content-Type': 'application/json',
      },
      signal: AbortSignal.timeout(5000),
    });

    if (!res.ok) {
      console.error(`[ISR Fetch Failed] API returned status ${res.status}`);
      return null;
    }

    const json: ApiResponse<ColorData[]> = await res.json();
    return json.data || [];
  } catch (error) {
    // Graceful error handling for network timeouts or DNS resolution issues
    console.error('[ISR Fetch Error] Gracefully caught fetch failure:', error);
    return null;
  }
}

/**
 * Next.js App Router Server Component with ISR Support
 */
export default async function CuratedColorsServer() {
  const colorItems = await fetchCuratedColors();

  // Graceful fallback UI when API fails or times out
  if (!colorItems || colorItems.length === 0) {
    return (
      <div className="w-full max-w-4xl mx-auto my-6 p-6 rounded-2xl bg-amber-50/70 border border-amber-200 text-amber-900">
        <div className="flex items-center gap-3">
          <div className="w-2.5 h-2.5 rounded-full bg-amber-500" />
          <h3 className="text-base font-bold">Data Feed Temporarily Offline</h3>
        </div>
        <p className="text-xs text-amber-700 mt-2 leading-relaxed">
          We encountered an issue reaching the upstream color service. A fallback cached state will reload automatically when the service responds.
        </p>
      </div>
    );
  }

  return (
    <section className="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      {/* Header & Status Indicator */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
          <h2 className="text-2xl font-black text-slate-900 tracking-tight">
            Curated Color Palettes (ISR)
          </h2>
          <p className="text-xs text-slate-500 mt-1">
            Auto-revalidates every 60s or immediately via tag <code className="font-mono text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded text-[11px]">my-color-data</code>
          </p>
        </div>

        <div className="flex items-center gap-2">
          <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            ISR 60s Active
          </span>
        </div>
      </div>

      {/* Rendered Data Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {colorItems.slice(0, 6).map((item) => (
          <div
            key={item.id}
            className="group p-5 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition-all space-y-3"
          >
            <div className="flex items-center justify-between">
              <span className="font-bold text-sm text-slate-800">{item.name}</span>
              <span className="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                {item.style}
              </span>
            </div>

            {/* Swatches */}
            <div className="flex h-12 rounded-xl overflow-hidden shadow-2xs border border-slate-100">
              {item.colors.map((color, index) => (
                <div
                  key={`${item.id}-${index}-${color}`}
                  className="flex-1 transition-transform group-hover:scale-105"
                  style={{ backgroundColor: color }}
                  title={color}
                />
              ))}
            </div>

            <div className="flex flex-wrap gap-1.5 pt-1">
              {item.tags.map((tag) => (
                <span
                  key={tag}
                  className="text-[10px] font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full"
                >
                  #{tag}
                </span>
              ))}
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}
