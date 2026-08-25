import React from 'react';
import Link from 'next/link';
import { ArrowRight } from 'lucide-react';

interface ScrollableSectionProps {
  title: string;
  viewMoreHref?: string;
  viewMoreText?: string;
  children: React.ReactNode;
}

export function ScrollableSection({ title, viewMoreHref, viewMoreText = 'View More', children }: ScrollableSectionProps) {
  return (
    <div className="space-y-4">
      <h2 className="text-2xl font-extrabold text-slate-900">{title}</h2>
      <div className="flex overflow-x-auto pb-4 gap-6 snap-x hide-scrollbar">
        {children}
        
        {viewMoreHref && (
          <Link
            href={viewMoreHref}
            className="shrink-0 w-32 md:w-48 bg-slate-50 border border-slate-100 rounded-3xl flex flex-col items-center justify-center gap-3 text-slate-500 hover:text-purple-600 hover:bg-purple-50 hover:border-purple-200 transition-all snap-start"
          >
            <div className="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
              <ArrowRight className="w-5 h-5" />
            </div>
            <span className="text-sm font-bold">{viewMoreText}</span>
          </Link>
        )}
      </div>
    </div>
  );
}
