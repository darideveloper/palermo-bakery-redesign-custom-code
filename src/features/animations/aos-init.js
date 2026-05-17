/**
 * AOS Initialization Script
 * Final Version: Optimized for high-performance gallery usage.
 * - Prevents multiple initializations
 * - Disables on mobile to prevent Safari crashes
 * - Targets specific elements instead of all images
 */

(function ($) {
  if (window.myAosScriptLoaded) return;
  window.myAosScriptLoaded = true;

  const initAnimations = () => {
    // 1. Apply AOS attributes to visual elements (Specifically product cards)
    const cards = document.querySelectorAll('.block-product-inner:not(.aos-applied)');
    cards.forEach(card => {
      card.setAttribute('data-aos', 'zoom-in');
      card.classList.add('aos-applied');
    });

    // 2. Apply AOS attributes to typography (Specifically gallery titles/headings)
    const titles = document.querySelectorAll('.page-title, h1, h2:not(.aos-applied), h3:not(.aos-applied)');
    titles.forEach(el => {
      // 'fade-up' is significantly safer for mobile than 'fade-right'
      const effect = window.innerWidth < 768 ? 'fade-up' : 'fade-right';
      el.setAttribute('data-aos', effect);
      el.classList.add('aos-applied');
    });

    // 3. Initialize AOS
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        once: true, // Only animate once for better performance
        offset: 50, 
        // DISABLE ON MOBILE: This is the #1 cause of "A problem repeatedly occurred" on iOS
        disable: window.innerWidth < 1024 
      });

      // Refresh AOS as images load to update trigger positions
      // We only do this once after a short delay to save resources
      setTimeout(() => {
        if (typeof AOS !== 'undefined') {
          AOS.refresh();
        }
      }, 1000);
    }
  };

  // Run on load and also on DOMContentLoaded for faster feel
  document.addEventListener('DOMContentLoaded', initAnimations);
  window.addEventListener('load', initAnimations);

  // Listen for Infinite Scroll events to apply AOS to new items
  if (typeof $ !== 'undefined') {
    $(document).on('yith_infs_added_elem append.infiniteScroll post-load', function() {
      initAnimations();
    });
  }
})(jQuery);
