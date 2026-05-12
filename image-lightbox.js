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

      // Attempt to find the image source (from most to least preferred)
      var originalSrc =
        $img.attr("data-original") ||
        $img.attr("data-src") ||
        $img.attr("data-lazy-src") ||
        $img.attr("src");

      if (!originalSrc) return;

      var isReady = $link.hasClass("link-ready");
      var isOptimized = originalSrc.includes("-300x300");

      if (isReady && isOptimized) {
        return;
      }

      if (isReady && !isOptimized) {
        console.warn(
          "[Gallery] Warning: Item was reverted to high-res! Re-fixing...",
          originalSrc,
        );
      } else {
        console.log("[Gallery] Processing item:", originalSrc);
      }

      // Determine the full-resolution URL for the lightbox:
      // 1. Prefer data-lightbox-src (set by the PHP filter)
      // 2. Fall back to stripping -300x300 from data-original
      // 3. Otherwise use originalSrc as-is
      var fullResSrc =
        $img.attr("data-lightbox-src") ||
        originalSrc.replace(/-300x300(?=\.\w+$)/i, "");

      // A. Set the lightbox href to the full-resolution image
      if ($link.attr("href") !== fullResSrc) {
        $link.attr("href", fullResSrc);
        $link.attr("data-rel", "prettyPhoto[cake-gallery]");
        $link.attr("title", $img.attr("alt") || "");
      }

      // B. Ensure the grid preview uses 300x300 thumbnail
      if (!isOptimized) {
        var thumbnailSrc = originalSrc.replace(
          /(.*)(\.(?:jpg|jpeg|png|webp))$/i,
          "$1-300x300$2",
        );

        console.log("[Gallery] Applying Optimized URL:", thumbnailSrc);

        // Only update data-original — let lazyload copy it to src
        // when the image scrolls into view. Do NOT remove class="lazy"
        // and do NOT set src directly, as that would defeat the theme's
        // lazy loading and cause iOS Safari to crash from memory pressure.
        $img.attr("data-original", thumbnailSrc);
        $img.attr("data-src", thumbnailSrc);
        $img.attr("data-lazy-src", thumbnailSrc);
        $img.attr("data-gallery-processed", "true");

        $img.removeAttr("srcset");
        $img.attr("data-srcset", "");
      }

      $link.addClass("link-ready");
    });

    // 2. INITIALIZE / RE-BIND PRETTYPHOTO
    if ($.fn.prettyPhoto) {
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
        overlay_gallery: false,

        changepicturecallback: function () {
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
    galleryTimeout = setTimeout(function () {
      initCakeGallery();
    }, 250);
  }

  // --- EXECUTION ---

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
      var nodesAdded = mutations.some(function (m) {
        return m.addedNodes.length > 0;
      });

      if (nodesAdded) {
        refreshGalleryDebounced();
      }
    });

    observer.observe(target, { childList: true, subtree: true });
  }
});
