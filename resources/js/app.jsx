// Dead file — not compiled by Vite. Remove if unused.
import './bootstrap';
import '../css/app.css';

import React from 'react';
import { createRoot } from 'react-dom/client';
import AuthAtmosphere from './src/components/auth/AuthAtmosphere';

const authAtmosphereContainer = document.getElementById('auth-atmosphere-root');
if (authAtmosphereContainer) {
    const root = createRoot(authAtmosphereContainer);
    root.render(<AuthAtmosphere />);
}

// Standalone (Blade) pages 3D background
import('./standalone-3d').then(({ initStandalone3D }) => {
    const canvas = document.getElementById('three-canvas');
    if (canvas) {
        initStandalone3D('three-canvas');
    }
});
