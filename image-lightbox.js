jQuery(document).ready(function ($) {
  // --- 1. SCRIPT GUARD ---
  if (window.cakeGalleryScriptLoaded) return;
  window.cakeGalleryScriptLoaded = true;

  var galleryTimeout;

  /**
   * CORE FUNCTION: PREPARE AND BIND GALLERY
   */
  function initCakeGallery() {
    console.log("--- Gallery Refresh: Processing Items ---");

    var $container = $("#sns_woo_list");
    // Target only elements NOT yet processed
    var $productCards = $(".block-product-inner:not(.gallery-ready)");

    if ($productCards.length === 0) {
       console.log("No new items to process.");
       return;
    }

    // 0. FIX ALT ATTRIBUTES (Sync title with image alt)
    $productCards.each(function () {
      var $card = $(this);
      var $titleLink = $card.find(".item-title a");
      var actualName = $titleLink.text().trim();
      
      if (actualName) {
        $card.find(".product-image img").attr("alt", actualName);
      }
      $card.addClass("gallery-ready");
    });

    // 1. PREPARE LINKS AND OPTIMIZE PREVIEW IMAGES
    var $galleryLinks = $container.find(".grid-view .product-image");

    $galleryLinks.each(function () {
      var $link = $(this);
      var $img = $link.find("img");
      
      // Attempt to find the high-res source
      var originalSrc = $img.attr("data-original") || 
                        $img.attr("data-src") || 
                        $img.attr("data-lazy-src") || 
                        $img.attr("src");

      if (!originalSrc) return;

      // SELF-HEALING: If the image is "ready" but the URL is NOT optimized, something reverted it.
      var isReady = $link.hasClass("link-ready");
      var isOptimized = originalSrc.includes("-300x300");

      if (isReady && isOptimized) {
         // Everything is fine, skip
         return;
      }

      if (isReady && !isOptimized) {
         console.warn("[Gallery] Warning: Item was reverted to high-res! Re-fixing...", originalSrc);
      } else {
         console.log("[Gallery] Processing item:", originalSrc);
      }

      // A. Set high-resolution image for the lightbox (href)
      // This should only happen if not already set or if we need to sync
      if ($link.attr("href") !== originalSrc) {
        $link.attr("href", originalSrc);
        $link.attr("data-rel", "prettyPhoto[cake-gallery]");
        $link.attr("title", $img.attr("alt") || "");
      }

      // B. Transform the grid preview to 300x300 thumbnail
      if (!isOptimized) {
        var thumbnailSrc = originalSrc
          .replace(/(.*)(\.(?:jpg|jpeg|png|webp))$/i, "$1-300x300$2");
        
        console.log("[Gallery] Applying Optimized URL:", thumbnailSrc);

        // Remove the lazy class FIRST so the theme's jquery.lazyload.js
        // won't react to the subsequent src/attribute changes
        $img.removeClass("lazy");
        
        // AGGRESSIVE OVERWRITE: Target all common lazy load attributes
        $img.attr("src", thumbnailSrc);
        $img.attr("data-original", thumbnailSrc);
        $img.attr("data-src", thumbnailSrc);
        $img.attr("data-lazy-src", thumbnailSrc);
        
        // Remove srcset to force the browser to use our 300x300 version
        $img.removeAttr("srcset");
        $img.attr("data-srcset", "");
      }

      $link.addClass("link-ready");
    });

    // 2. INITIALIZE / RE-BIND PRETTYPHOTO
    if ($.fn.prettyPhoto) {
      // Re-initialize prettyPhoto for all links to ensure the new ones are included in the collection
      $("a[data-rel^='prettyPhoto']").unbind("click.prettyphoto");

      $("a[data-rel^='prettyPhoto']").prettyPhoto({
        hook: "data-rel",
        social_tools: false,
        theme: "pp_default",
        horizontal_padding: 20,
        opacity: 0.8,
        deeplinking: false,
        allow_resize: true,
        default_width: 900,
        default_height: 600,

        // FIX 1: Turn off the thumbnail gallery at the bottom
        overlay_gallery: false,

        changepicturecallback: function () {
          // Extra safety: force height adjustment on every image change
          var viewportHeight = $(window).height();
          $(".pp_content_container").css(
            "max-height",
            viewportHeight - 120 + "px",
          );
        },
      });
      console.log("prettyPhoto bound to " + $galleryLinks.length + " items.");
    }
  }

  /**
   * DEBOUNCED REFRESH
   */
  function refreshGalleryDebounced() {
    clearTimeout(galleryTimeout);
    galleryTimeout = setTimeout(function() {
      initCakeGallery();
    }, 250); // Increased delay for better stability on mobile
  }

  // --- EXECUTION ---

  // Run on initial page load
  initCakeGallery();

  // Listen for common Infinite Scroll events
  $(document).on(
    "yith_infs_added_elem append.infiniteScroll post-load",
    function () {
      refreshGalleryDebounced();
    },
  );

  // Watch the container for any DOM changes (Filters/AJAX Pagination)
  var target = document.querySelector("#sns_woo_list");
  if (target) {
    var observer = new MutationObserver(function (mutations) {
      // Check if any nodes were actually added to avoid useless triggers
      var nodesAdded = mutations.some(function(m) {
        return m.addedNodes.length > 0;
      });

      if (nodesAdded) {
        refreshGalleryDebounced();
      }
    });

    observer.observe(target, { childList: true, subtree: true });
  }
});
