import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import LoginApp from './components/LoginApp';

function mountApp() {
    const rootElement = document.getElementById('app');
    if (!rootElement) return false;

    createRoot(rootElement).render(
        <React.StrictMode>
            <LoginApp />
        </React.StrictMode>,
    );

    return true;
}

if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', () => mountApp());
} else {
    mountApp();
}

