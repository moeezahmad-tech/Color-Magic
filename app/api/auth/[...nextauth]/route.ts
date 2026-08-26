import NextAuth from 'next-auth';
import { NextRequest } from 'next/server';
import { authOptions } from '@/lib/auth';

const handler = NextAuth(authOptions);

async function authHandler(
  req: NextRequest,
  context: { params: Promise<{ nextauth: string[] }> }
) {
  const resolvedParams = await context.params;
  return handler(req as any, { params: resolvedParams } as any);
}

export { authHandler as GET, authHandler as POST };
