import { NextRequest, NextResponse } from 'next/server';

export async function GET(request: NextRequest) {
  // Purges user session and redirects to home page with status flag
  const response = NextResponse.redirect(new URL('/?msg=account_deleted', request.url));
  response.cookies.delete('next-auth.session-token');
  response.cookies.delete('__Secure-next-auth.session-token');
  return response;
}
