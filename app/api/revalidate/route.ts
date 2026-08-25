import { NextRequest, NextResponse } from 'next/server';
import { revalidateTag } from 'next/cache';

/**
 * Strict TypeScript interface for the incoming revalidation webhook payload
 */
export interface RevalidateWebhookPayload {
  /** The cache tag to invalidate */
  tag: string;
  /** Secret authorization token to prevent unauthorized cache purges */
  secret: string;
}

/**
 * Route Handler for On-Demand Incremental Static Regeneration (ISR)
 * Endpoint: POST /api/revalidate
 *
 * Caching Lifecycle:
 * 1. An external event occurs (e.g., CMS webhook, database mutation, admin update).
 * 2. The client or webhook sends a POST request with the specific `tag` and `secret`.
 * 3. `revalidateTag(tag)` immediately invalidates all cached fetch requests tagged with that tag in Next.js's Data Cache.
 * 4. The next user request hitting the route will trigger a fresh background fetch and update the cache.
 */
export async function POST(request: NextRequest) {
  try {
    const body = (await request.json()) as Partial<RevalidateWebhookPayload>;
    const { tag, secret } = body;

    // 1. Authorization: Verify the secret token against server environment variables
    const expectedSecret = process.env.REVALIDATION_SECRET_TOKEN;
    
    if (!expectedSecret) {
      console.warn('[ISR Revalidate] REVALIDATION_SECRET_TOKEN environment variable is not defined.');
    }

    if (!secret || (expectedSecret && secret !== expectedSecret)) {
      return NextResponse.json(
        { 
          revalidated: false, 
          message: 'Unauthorized: Invalid or missing revalidation secret token' 
        },
        { status: 401 }
      );
    }

    // 2. Validation: Ensure a valid cache tag string is provided
    if (!tag || typeof tag !== 'string' || tag.trim().length === 0) {
      return NextResponse.json(
        { 
          revalidated: false, 
          message: 'Bad Request: Missing or invalid "tag" in request body' 
        },
        { status: 400 }
      );
    }

    const cleanTag = tag.trim();

    // 3. Invalidation: Purge the cache entries associated with the tag
    // In Next.js 16+, revalidateTag accepts a cacheLife profile or { expire } config
    revalidateTag(cleanTag, { expire: 0 });

    return NextResponse.json({
      revalidated: true,
      tag: cleanTag,
      timestamp: new Date().toISOString(),
      message: `Cache tag "${cleanTag}" purged successfully. Next visit will re-fetch fresh data.`,
    });
  } catch (error) {
    console.error('[ISR Revalidate Error]:', error);
    return NextResponse.json(
      {
        revalidated: false,
        message: 'Internal Server Error occurred while purging cache tag',
        error: error instanceof Error ? error.message : 'Unknown error',
      },
      { status: 500 }
    );
  }
}
