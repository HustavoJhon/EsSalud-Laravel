import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Suprimir errores de extensiones del navegador (iframes cross-origin)
window.addEventListener('error', (e) => {
    if (e.message?.includes('Permission denied') || e.message?.includes('nodeType')) {
        e.preventDefault();
        e.stopPropagation();
    }
});
window.addEventListener('unhandledrejection', (e) => {
    if (e.reason?.message?.includes('Permission denied') || e.reason?.message?.includes('nodeType')) {
        e.preventDefault();
    }
});
