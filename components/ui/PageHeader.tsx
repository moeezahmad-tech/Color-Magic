import React from 'react';

interface PageHeaderProps {
  title: React.ReactNode;
  description?: React.ReactNode;
  icon?: React.ReactNode;
  action?: React.ReactNode;
}

export function PageHeader({ title, description, icon, action }: PageHeaderProps) {
  return (
    <div className="bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-7 sm:pt-12 sm:pb-8">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div className="flex-1">
            <div className="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-2 sm:mb-3">
              {icon && (
                <div className="text-purple-600 flex items-center justify-center shrink-0 hidden sm:flex">
                  {/* Scale up icon for desktop to match huge text */}
                  {React.isValidElement(icon) ? React.cloneElement(icon as any, { className: 'w-8 h-8 sm:w-10 sm:h-10' }) : icon}
                </div>
              )}
              <h1 className="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight flex items-center gap-2 sm:gap-0">
                {/* Show smaller icon inline on mobile */}
                {icon && (
                  <div className="text-purple-600 flex sm:hidden shrink-0">
                    {React.isValidElement(icon) ? React.cloneElement(icon as any, { className: 'w-8 h-8' }) : icon}
                  </div>
                )}
                <span>{title}</span>
              </h1>
            </div>
            {description && (
              <div className="text-base sm:text-lg text-slate-600 max-w-3xl">
                {description}
              </div>
            )}
          </div>
          
          {action && (
            <div className="shrink-0">
              {action}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
