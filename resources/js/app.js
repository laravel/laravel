import './bootstrap';
import Alpine from 'alpinejs';
import * as THREE from 'three';
import { init3DNetBackground } from './3d-net';
import { initThemeSwitcher } from './theme-switcher';

window.Alpine = Alpine;
window.THREE = THREE;
window.initThemeSwitcher = initThemeSwitcher;

// Initialize 3D background when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    init3DNetBackground();
});

Alpine.start();
