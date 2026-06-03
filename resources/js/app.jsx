import './bootstrap';
import '../css/app.css';

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

if (!authAtmosphereContainer) {
    // Standalone (Blade) pages 3D background
    import('./standalone-3d').then(({ initStandalone3D }) => {
        initStandalone3D('three-canvas');
    });
}
