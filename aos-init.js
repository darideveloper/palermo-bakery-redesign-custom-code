/**
 * AOS Initialization Script
 * Final Version: Fixed mobile "disappearing" content by removing global 'a' tag targeting
 * and implementing progressive refresh for lazy-loaded content.
 */

(function () {
  if (window.myAosScriptLoaded) return;
  window.myAosScriptLoaded = true;

  const initAnimations = () => {
    // 1. Apply AOS attributes to visual elements
    const images = document.querySelectorAll('.wp-block-image, img, .social_rounded');
    images.forEach(img => {
      if (!img.hasAttribute('data-aos')) {
        img.setAttribute('data-aos', 'zoom-in');
      }
    });

    // 2. Apply AOS attributes to typography (excluding global 'a' tags to prevent mobile glitches)
    const elementsToFade = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
    elementsToFade.forEach(el => {
      if (!el.hasAttribute('data-aos')) {
        // 'fade-up' is significantly safer for mobile than 'fade-right'
        const effect = window.innerWidth < 768 ? 'fade-up' : 'fade-right';
        el.setAttribute('data-aos', effect);
      }
    });

    // 3. Initialize AOS
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        once: false,
        offset: 50, // Smaller offset for better mobile triggers
        disable: false
      });

      // The "Magic Fix": Refresh AOS multiple times as images/grids load to update trigger positions
      [100, 500, 1500, 3000].forEach(delay => {
        setTimeout(() => {
          if (typeof AOS !== 'undefined') {
            AOS.refresh();
          }
        }, delay);
      });
    }
  };

  // Run on load
  if (document.readyState === 'complete') {
    initAnimations();
  } else {
    window.addEventListener('load', initAnimations);
  }
})();