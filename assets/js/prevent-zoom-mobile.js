// assets/js/prevent-zoom-mobile.js
document.addEventListener('DOMContentLoaded', () => {
  // uniquement sur devices tactiles
  if (!window.matchMedia('(pointer: coarse)').matches) return;

  const targets = document.querySelectorAll('.no-zoom-target');
  targets.forEach((el) => {
    ['gesturestart', 'gesturechange', 'gestureend'].forEach((type) => {
      el.addEventListener(type, (e) => e.preventDefault(), { passive: false });
    });

    el.addEventListener('touchstart', (e) => {
      if (e.touches && e.touches.length > 1) e.preventDefault();
    }, { passive: false });

    el.addEventListener('touchmove', (e) => {
      if (e.touches && e.touches.length > 1) e.preventDefault();
    }, { passive: false });

    let lastTouchEnd = 0;
    el.addEventListener('touchend', (e) => {
      const now = Date.now();
      if (now - lastTouchEnd <= 300) e.preventDefault();
      lastTouchEnd = now;
    }, { passive: false });
  });
});
