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

    // 1. PREPARE LINKS FOR PRETTYPHOTO
    var $galleryLinks = $container.find(".grid-view .product-image:not(.link-ready)");

    $galleryLinks.each(function () {
      var $link = $(this);
      var $img = $link.find("img");
      var highResImage = $img.attr("data-original") || $img.attr("src");

      if (highResImage) {
        $link.attr("href", highResImage);
        $link.attr("data-rel", "prettyPhoto[cake-gallery]");
        $link.attr("title", $img.attr("alt") || "");
        $link.addClass("link-ready");
      }
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
