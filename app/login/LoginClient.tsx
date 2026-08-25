'use client';

import React from 'react';
import { Palette, Shield, Lock, Sparkles, ArrowRight } from 'lucide-react';
import { GoogleIcon } from '@/components/ui/Icons';
import Link from 'next/link';

export default function LoginClient() {
  const handleGoogleSignIn = () => {
    window.location.href = '/api/auth/signin/google';
  };

  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-20 bg-gradient-to-br from-purple-50 via-white to-pink-50">
      <div className="w-full max-w-md bg-white rounded-3xl p-8 shadow-xl border border-slate-100 text-center">
        <div className="w-14 h-14 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center mx-auto mb-6 shadow-lg">
          <Palette className="w-7 h-7 text-white" />
        </div>

        <h1 className="text-3xl font-black text-slate-900">Sign In to Color Magic</h1>
        <p className="text-slate-500 text-sm mt-2 mb-8">
          Synchronize your saved favorite color palettes and CSS gradients across devices.
        </p>

        <button
          onClick={handleGoogleSignIn}
          className="w-full py-3.5 px-4 rounded-xl bg-white border-2 border-slate-200 hover:border-purple-400 text-slate-800 font-bold text-sm flex items-center justify-center gap-3 transition-all hover:bg-purple-50 shadow-sm mb-4"
        >
          <GoogleIcon className="w-5 h-5" />
          <span>Continue with Google OAuth 2.0</span>
        </button>

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
    </div>
  );
}
