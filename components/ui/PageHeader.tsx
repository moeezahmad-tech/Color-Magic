import React from 'react';

interface PageHeaderProps {
  title: React.ReactNode;
  description?: React.ReactNode;
  icon?: React.ReactNode;
}

export function PageHeader({ title, description, icon }: PageHeaderProps) {
  return (
    <div className="relative bg-white border-b border-slate-100 overflow-hidden">
      {/* Background decoration */}
      <div 
        aria-hidden 
        className="pointer-events-none absolute top-0 right-0 w-[400px] h-[400px] rounded-full opacity-[0.04]"
        style={{ background: 'radial-gradient(circle, #ec4899, transparent 70%)', transform: 'translate(40%, -40%)' }}
      />
      <div 
        aria-hidden 
        className="pointer-events-none absolute bottom-0 left-0 w-[300px] h-[300px] rounded-full opacity-[0.04]"
        style={{ background: 'radial-gradient(circle, #8b5cf6, transparent 70%)', transform: 'translate(-40%, 40%)' }}
      />

      <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div className="max-w-3xl">
          {icon && (
            <div className="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6">
              {icon}
            </div>
          )}
          <h1 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
            {title}
          </h1>
          {description && (
            <p className="text-lg text-slate-500 leading-relaxed max-w-2xl">
              {description}
            </p>
          )}
        </div>
      </div>
    </div>
  );
}
