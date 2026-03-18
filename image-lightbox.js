jQuery(document).ready(function ($) {

  /**
   * CORE FUNCTION: PREPARE AND BIND GALLERY
   */
  function initCakeGallery() {
    console.log("--- Gallery Refresh: Processing Items ---")

    var $container = $('#sns_woo_list')
    var $productCards = $('.block-product-inner')

    // 0. FIX ALT ATTRIBUTES (Sync title with image alt)
    $productCards.each(function () {
      var $card = $(this)
      var actualName = $card.find('.item-title a').text().trim()
      if (actualName) {
        $card.find('.product-image img').attr('alt', actualName)
      }
    })

    // 1. PREPARE LINKS FOR PRETTYPHOTO
    var $galleryLinks = $container.find('.grid-view .product-image')

    $galleryLinks.each(function () {
      var $link = $(this)
      var $img = $link.find('img')
      var highResImage = $img.attr('data-original') || $img.attr('src')

      if (highResImage) {
        $link.attr('href', highResImage)
        $link.attr('data-rel', 'prettyPhoto[cake-gallery]')
        $link.attr('title', $img.attr('alt') || "")
      }
    })

    // 2. INITIALIZE / RE-BIND PRETTYPHOTO
    if ($.fn.prettyPhoto) {
      // Unbind previous clicks to prevent double-triggering
      $("a[data-rel^='prettyPhoto']").unbind('click.prettyphoto')

      $("a[data-rel^='prettyPhoto']").prettyPhoto({
        hook: 'data-rel',
        social_tools: false,
        theme: 'pp_default',
        horizontal_padding: 20,
        opacity: 0.8,
        deeplinking: false,
        allow_resize: true,
        default_width: 900,
        default_height: 600,

        // FIX: prettyPhoto hides the slider if items > 30 by default. 
        // We set this higher to accommodate your 147+ items.
        overlay_gallery: true,
        overlay_gallery_max: 300,

        changepicturecallback: function () {
          // Extra safety: force height adjustment on every image change
          var viewportHeight = $(window).height()
          $(".pp_content_container").css("max-height", (viewportHeight - 120) + "px")
        }
      })
      console.log("prettyPhoto bound to " + $galleryLinks.length + " items.")
    }
  }

  // --- EXECUTION ---

  // Run on initial page load
  initCakeGallery()

  // Listen for common Infinite Scroll events
  $(document).on('yith_infs_added_elem append.infiniteScroll post-load', function () {
    initCakeGallery()
  })

  // Watch the container for any DOM changes (Filters/AJAX Pagination)
  var target = document.querySelector('#sns_woo_list')
  if (target) {
    var observer = new MutationObserver(function (mutations) {
      // Small timeout to ensure elements are fully rendered
      setTimeout(function () {
        initCakeGallery()
      }, 100)
    })
    observer.observe(target, { childList: true, subtree: true })
  }

})