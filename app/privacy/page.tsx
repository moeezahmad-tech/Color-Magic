import type { Metadata } from 'next';
import React from 'react';
import Link from 'next/link';
import { ShieldCheck, Lock, Eye, Database, Globe, UserCheck, Mail, ArrowLeft, Cookie, Sparkles } from 'lucide-react';

export const metadata: Metadata = {
  title: 'Privacy Policy',
  description: 'Learn how Color Magic collects, uses, and safeguards your data when you use our color tools and Google authentication.',
  alternates: { canonical: '/privacy' },
  openGraph: {
    title: 'Privacy Policy | Color Magic',
    description: 'Privacy Policy and data protection details for Color Magic users.',
    url: '/privacy',
  },
};

export default function PrivacyPolicyPage() {
  const lastUpdated = 'February 26, 2025';

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
      {/* Header */}
      <div className="text-center space-y-4">
        <Link
          href="/"
          className="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-purple-600 transition-colors mb-2"
        >
          <ArrowLeft className="w-3.5 h-3.5" /> Back to Home
        </Link>
        <div className="flex justify-center">
          <span className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-purple-50 text-purple-600 rounded-full border border-purple-100 text-xs font-bold shadow-2xs">
            <ShieldCheck className="w-4 h-4 text-purple-600" /> Trust &amp; Privacy
          </span>
        </div>
        <h1 className="text-4xl sm:text-5xl font-black tracking-tight text-slate-900">
          Privacy{' '}
          <span className="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
            Policy
          </span>
        </h1>
        <p className="text-slate-500 text-sm max-w-xl mx-auto">
          We value your privacy and are committed to transparency. This policy outlines what data we collect, why we collect it, and how your information is protected.
        </p>
        <p className="text-xs font-mono text-slate-400">Last updated: {lastUpdated}</p>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-xs flex flex-col items-center text-center">
          <div className="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
            <Lock className="w-6 h-6" />
          </div>
          <h3 className="text-base font-black text-slate-900">No Selling of Data</h3>
          <p className="text-xs text-slate-500 mt-1">We will never sell or rent your personal information to third parties.</p>
        </div>

        <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-xs flex flex-col items-center text-center">
          <div className="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
            <Database className="w-6 h-6" />
          </div>
          <h3 className="text-base font-black text-slate-900">Client-First Storage</h3>
          <p className="text-xs text-slate-500 mt-1">Palettes and favorites are saved locally on your device by default.</p>
        </div>

        <div className="bg-white border border-slate-100 rounded-3xl p-6 shadow-xs flex flex-col items-center text-center">
          <div className="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center mb-3">
            <UserCheck className="w-6 h-6" />
          </div>
          <h3 className="text-base font-black text-slate-900">Minimal OAuth Scope</h3>
          <p className="text-xs text-slate-500 mt-1">Google login only requests your basic profile and email to identify your session.</p>
        </div>
      </div>

      {/* Detailed Content */}
      <div className="space-y-8">
        {/* Section 1: Overview */}
        <section className="bg-white border border-slate-100 rounded-3xl p-8 shadow-xs space-y-4">
          <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <Eye className="w-6 h-6 text-purple-600" /> 1. Information We Collect
          </h2>
          <p className="text-slate-600 text-sm leading-relaxed">
            Color Magic operates as a free, open-source color tool suite provided by <strong>TechKreative</strong>. We keep data collection to the absolute minimum needed to deliver a reliable user experience:
          </p>
          <ul className="space-y-2 text-sm text-slate-600 list-disc list-inside pl-2">
            <li>
              <strong>Google Account Information (Optional):</strong> When you choose to sign in with Google OAuth, we receive your name, email address, and profile picture avatar. We do not access your contacts, Google Drive, or any other personal Google data.
            </li>
            <li>
              <strong>Saved Preferences:</strong> Your saved favorite palettes, gradients, and custom colors are stored locally in your browser via <code className="text-xs font-mono bg-slate-100 px-1.5 py-0.5 rounded text-pink-600">localStorage</code>.
            </li>
            <li>
              <strong>Image Color Extraction:</strong> When you use our <em>Palette from Image</em> tool, all image processing occurs entirely within your browser&apos;s HTML5 Canvas. Your uploaded images are <strong>never</strong> transmitted to our servers or stored remotely.
            </li>
            <li>
              <strong>Anonymous Usage Analytics:</strong> We use Google Analytics 4 (GA4) with IP anonymization to track aggregate web vitals, page views, and feature popularity to continuously improve tool performance.
            </li>
          </ul>
        </section>

        {/* Section 2: How We Use Information */}
        <section className="bg-white border border-slate-100 rounded-3xl p-8 shadow-xs space-y-4">
          <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <Sparkles className="w-6 h-6 text-pink-500" /> 2. How We Use Your Information
          </h2>
          <p className="text-slate-600 text-sm leading-relaxed">
            The limited information collected is used exclusively for the following purposes:
          </p>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-slate-600">
            <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <strong className="text-slate-900 block mb-1">Session Management</strong>
              Authenticating your profile and personalizing your user dashboard.
            </div>
            <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <strong className="text-slate-900 block mb-1">Favorites &amp; Sync</strong>
              Maintaining your saved color collections and export preferences.
            </div>
            <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <strong className="text-slate-900 block mb-1">Platform Stability</strong>
              Monitoring site errors, uptime, and security integrity.
            </div>
            <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <strong className="text-slate-900 block mb-1">Open-Source Enhancements</strong>
              Optimizing color generation algorithms based on general usage patterns.
            </div>
          </div>
        </section>

        {/* Section 3: Google OAuth & Third Parties */}
        <section className="bg-white border border-slate-100 rounded-3xl p-8 shadow-xs space-y-4">
          <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <Globe className="w-6 h-6 text-blue-500" /> 3. Third-Party Services &amp; Google User Data
          </h2>
          <p className="text-slate-600 text-sm leading-relaxed">
            Color Magic complies with the <strong>Google API Services User Data Policy</strong>, including the Limited Use requirements.
          </p>
          <div className="p-4 bg-purple-50/70 border border-purple-100 rounded-2xl text-xs text-purple-900 leading-relaxed">
            <strong>Google API Limited Use Disclosure:</strong> Color Magic&apos;s use and transfer to any other app of information received from Google APIs will adhere to{' '}
            <a
              href="https://developers.google.com/terms/api-services-user-data-policy"
              target="_blank"
              rel="noopener noreferrer"
              className="underline font-bold text-purple-700 hover:text-purple-900"
            >
              Google API Services User Data Policy
            </a>
            , including the Limited Use requirements.
          </div>
          <p className="text-slate-600 text-sm leading-relaxed">
            We do not share, transfer, or disclose Google user data to third parties, advertising platforms, or data brokers.
          </p>
        </section>

        {/* Section 4: Cookies & Local Storage */}
        <section className="bg-white border border-slate-100 rounded-3xl p-8 shadow-xs space-y-4">
          <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <Cookie className="w-6 h-6 text-amber-500" /> 4. Cookies &amp; Local Storage
          </h2>
          <p className="text-slate-600 text-sm leading-relaxed">
            We use cookies strictly for session authentication (NextAuth JWT tokens) and basic site analytics. You can adjust cookie preferences or clear local storage at any time via your browser settings or from your Profile dashboard.
          </p>
        </section>

        {/* Section 5: Data Retention & User Rights */}
        <section className="bg-white border border-slate-100 rounded-3xl p-8 shadow-xs space-y-4">
          <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <ShieldCheck className="w-6 h-6 text-emerald-500" /> 5. Your Rights &amp; Data Deletion
          </h2>
          <p className="text-slate-600 text-sm leading-relaxed">
            You maintain full control over your data. You may:
          </p>
          <ul className="space-y-2 text-sm text-slate-600 list-disc list-inside pl-2">
            <li>Sign out at any time to invalidate active session tokens.</li>
            <li>Clear your saved favorites with one click in the <Link href="/profile" className="text-pink-500 font-bold hover:underline">Profile</Link> page.</li>
            <li>Revoke Color Magic&apos;s access anytime through your <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener noreferrer" className="text-purple-600 font-bold hover:underline">Google Security Settings</a>.</li>
            <li>Request permanent deletion of any associated account records by contacting our support team.</li>
          </ul>
        </section>

        {/* Section 6: Contact */}
        <section className="bg-gradient-to-r from-purple-500 to-pink-500 rounded-3xl p-8 text-white space-y-3">
          <h2 className="text-2xl font-extrabold flex items-center gap-2">
            <Mail className="w-6 h-6 text-white" /> 6. Contact &amp; Inquiries
          </h2>
          <p className="text-white/90 text-sm leading-relaxed">
            If you have questions, concerns, or requests regarding this Privacy Policy or data protection, please reach out to us:
          </p>
          <div className="text-sm font-semibold pt-2">
            <p><strong>Organization:</strong> TechKreative</p>
            <p><strong>Website:</strong> <a href="https://techkreative.com" target="_blank" rel="noopener noreferrer" className="underline hover:text-pink-100">https://techkreative.com</a></p>
            <p><strong>Open Source Repo:</strong> <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer" className="underline hover:text-pink-100">GitHub Repository</a></p>
          </div>
        </section>
      </div>
    </div>
  );
}
