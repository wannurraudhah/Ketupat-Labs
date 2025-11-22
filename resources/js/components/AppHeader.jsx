import React from 'react';

export default function AppHeader({ title, subtitle, actions, showBack, onBack }) {
    return (
        <div className="bg-white border-b border-slate-200 px-6 py-4">
            <div className="max-w-7xl mx-auto flex items-center justify-between">
                <div className="flex items-center gap-4">
                    {/* Logo */}
                    <img
                        src="/images/logo.png"
                        alt="Logo"
                        className="h-10 w-auto"
                        onError={(e) => {
                            // Hide logo if image doesn't exist
                            e.target.style.display = 'none';
                        }}
                    />
                    <div>
                        {showBack && (
                            <button
                                onClick={onBack}
                                className="text-slate-600 hover:text-slate-900 mb-2 text-sm block"
                            >
                                ← Back
                            </button>
                        )}
                        <h1 className="text-3xl font-semibold text-slate-900">{title}</h1>
                        {subtitle && <p className="text-sm text-slate-500 mt-1">{subtitle}</p>}
                    </div>
                </div>
                {actions && <div className="flex items-center gap-3">{actions}</div>}
            </div>
        </div>
    );
}

