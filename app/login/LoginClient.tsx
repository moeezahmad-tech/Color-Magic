'use client';

import React, { useState, Suspense } from 'react';
import { Palette, Shield, Lock, Sparkles, ArrowRight, AlertCircle } from 'lucide-react';
import { GoogleIcon } from '@/components/ui/Icons';
import { signIn } from 'next-auth/react';
import { useSearchParams } from 'next/navigation';
import Link from 'next/link';

function LoginForm() {
  const searchParams = useSearchParams();
  const errorParam = searchParams.get('error');
  const callbackUrl = searchParams.get('callbackUrl') || '/profile';
  const [isLoading, setIsLoading] = useState(false);

  const handleGoogleSignIn = async () => {
    try {
      setIsLoading(true);
      await signIn('google', { callbackUrl });
    } catch (err) {
      console.error('Sign in error:', err);
      setIsLoading(false);
    }
  };

  const getErrorMessage = (err: string | null) => {
    if (!err) return null;
    switch (err.toLowerCase()) {
      case 'google':
      case 'oauthsignin':
      case 'oauthcallback':
        return 'Could not sign in with Google. Please check that Authorized Redirect URIs are configured in Google Cloud Console.';
      case 'configuration':
        return 'NextAuth configuration error. Please ensure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET are set.';
      case 'accessdenied':
        return 'Access denied. You cancelled the sign-in request.';
      default:
        return 'An error occurred while signing in. Please try again.';
    }
  };

  const errorMessage = getErrorMessage(errorParam);

  return (
    <div className="w-full max-w-md bg-white rounded-3xl p-8 shadow-xl border border-slate-100 text-center">
      <div className="w-14 h-14 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center mx-auto mb-6 shadow-lg">
        <Palette className="w-7 h-7 text-white" />
      </div>

      <h1 className="text-3xl font-black text-slate-900">Sign In to Color Magic</h1>
      <p className="text-slate-500 text-sm mt-2 mb-6">
        Synchronize your saved favorite color palettes and CSS gradients across devices.
      </p>

      {errorMessage && (
        <div className="mb-6 p-3.5 bg-red-50 border border-red-200 rounded-2xl text-left flex items-start gap-3 text-red-700 text-xs">
          <AlertCircle className="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
          <div className="flex-1">
            <strong className="font-bold block">Authentication Notice</strong>
            <span>{errorMessage}</span>
          </div>
        </div>
      )}

      <button
        onClick={handleGoogleSignIn}
        disabled={isLoading}
        className="w-full py-3.5 px-4 rounded-xl bg-white border-2 border-slate-200 hover:border-purple-400 text-slate-800 font-bold text-sm flex items-center justify-center gap-3 transition-all hover:bg-purple-50 shadow-sm mb-3 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
      >
        <GoogleIcon className="w-5 h-5" />
        <span>{isLoading ? 'Connecting to Google...' : 'Continue with Google'}</span>
      </button>

      <p className="text-[11px] text-slate-400 leading-relaxed px-2">
        By signing in, you agree to our{' '}
        <Link href="/terms" className="text-purple-600 hover:underline font-semibold">
          Terms of Service
        </Link>{' '}
        and{' '}
        <Link href="/privacy" className="text-purple-600 hover:underline font-semibold">
          Privacy Policy
        </Link>
        .
      </p>

      <div className="flex items-center gap-3 my-4">
        <div className="flex-1 h-px bg-slate-100" />
        <span className="text-xs text-slate-400 font-semibold">or</span>
        <div className="flex-1 h-px bg-slate-100" />
      </div>

      <Link
        href="/"
        className="text-xs font-semibold text-slate-400 hover:text-slate-700 transition-colors flex items-center justify-center gap-1"
      >
        <span>Continue as Anonymous Guest</span>
        <ArrowRight className="w-3.5 h-3.5" />
      </Link>

      <div className="mt-8 grid grid-cols-3 gap-3 text-center">
        {[
          { icon: <Shield className="w-4 h-4 text-emerald-500" />, label: 'Secure' },
          { icon: <Lock className="w-4 h-4 text-blue-500" />, label: 'Private' },
          { icon: <Sparkles className="w-4 h-4 text-purple-500" />, label: 'Free' },
        ].map(({ icon, label }) => (
          <div key={label} className="bg-slate-50 rounded-xl py-2 flex flex-col items-center gap-1">
            {icon}
            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{label}</span>
          </div>
        ))}
      </div>
    </div>
  );
}

export default function LoginClient() {
  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-20 bg-gradient-to-br from-purple-50 via-white to-pink-50">
      <Suspense fallback={<div className="text-slate-400 text-sm">Loading sign in...</div>}>
        <LoginForm />
      </Suspense>
    </div>
  );
}

