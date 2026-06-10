jQuery(document).ready(function ($) {
  // --- 1. SCRIPT GUARD ---
  if (window.cakeGalleryScriptLoaded) return;
  window.cakeGalleryScriptLoaded = true;

  var galleryTimeout;

  var PRETTYPHOTO_OPTIONS = {
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
  };

  /**
   * Prepare a single product card. Idempotent — safe to call repeatedly.
   * Swaps the spinner-GIF src for the full-resolution image, marks the img
   * as native-lazy, and strips the `.lazy` class so the legacy
   * jquery.lazyload scroll-handler ignores it. 300+ animated GIFs decoding
   * in parallel was what was locking up the main thread.
   */
  function prepareCard($card) {
    if ($card.hasClass("gallery-ready")) return;

    var $titleLink = $card.find(".item-title a");
    var actualName = $titleLink.text().trim();
    var $img = $card.find(".product-image img");
    if (actualName) $img.attr("alt", actualName);

    var $link = $card.find(".grid-view .product-image, .product-image").first();
    var originalSrc =
      $img.attr("data-original") ||
      $img.attr("data-src") ||
      $img.attr("data-lazy-src") ||
      "";

    var currentSrc = $img.attr("src") || "";
    var isSpinner = currentSrc.indexOf("prod_loading") !== -1 || currentSrc === "";

    if (originalSrc) {
      var fullResSrc =
        $img.attr("data-lightbox-src") || originalSrc;

      if ($link.length && $link.attr("href") !== fullResSrc) {
        $link.attr("href", fullResSrc);
        $link.attr("data-rel", "prettyPhoto[cake-gallery]");
        $link.attr("title", $img.attr("alt") || "");
      }

      // Native lazy-loading hands off to the browser. Set this BEFORE swapping
      // src so the swap respects lazy semantics.
      $img.attr("loading", "lazy");
      $img.attr("decoding", "async");
      $img.removeAttr("srcset");

      // Replace the animated spinner GIF with the full-resolution image. With
      // loading="lazy", the browser only fetches when near viewport.
      if (isSpinner) {
        $img.attr("src", originalSrc);
      }

      // Disarm jquery.lazyload for this img — its scroll handler is the
      // other half of the iOS freeze.
      $img.removeClass("lazy");
      $img.attr("data-original", originalSrc);
    }

    $card.addClass("gallery-ready");
  }

  function initLightbox($scope) {
    if (!$.fn.prettyPhoto) return;

    var selector = "a[data-rel^='prettyPhoto']:not(.pp-bound)";
    var $targets = $scope ? $scope.find(selector).addBack(selector) : $(selector);

    if ($targets.length) {
      $targets.prettyPhoto(PRETTYPHOTO_OPTIONS);
      $targets.addClass("pp-bound");
    }
  }
  window.palermoInitLightbox = initLightbox;

  /**
   * Process cards in chunks so iOS Safari can paint and respond to touch
   * between batches. CHUNK_SIZE is tuned for the cake-gallery (~308 cards).
   */
  var CHUNK_SIZE = 30;

  function processCardsChunked($cards, done) {
    var i = 0;
    function step() {
      var end = Math.min(i + CHUNK_SIZE, $cards.length);
      for (; i < end; i++) prepareCard($cards.eq(i));
      // Bind lightbox to the newly prepared elements in this chunk.
      initLightbox($cards.slice(i - CHUNK_SIZE, i));

      if (i < $cards.length) {
        // Yield to the browser. rAF is ~16ms on iOS — keeps scroll alive.
        if (window.requestAnimationFrame) requestAnimationFrame(step);
        else setTimeout(step, 16);
      } else if (done) {
        done();
      }
    }
    step();
  }

  function processCards($scope) {
    var $cards;
    if ($scope && $scope.length) {
      $cards = $scope.find(".block-product-inner").addBack(".block-product-inner");
    } else {
      $cards = $(".block-product-inner:not(.gallery-ready)");
    }
    if (!$cards.length) return;

    // Append-batches from infinite scroll are small (~20) — do synchronously.
    if ($cards.length <= CHUNK_SIZE) {
      $cards.each(function () { prepareCard($(this)); });
      initLightbox($cards);
      return;
    }

    // Initial page load: process above-the-fold synchronously so the user
    // sees real images immediately, then yield for the rest.
    var firstBatch = $cards.slice(0, CHUNK_SIZE);
    var rest = $cards.slice(CHUNK_SIZE);
    firstBatch.each(function () { prepareCard($(this)); });
    initLightbox(firstBatch);
    processCardsChunked(rest);
  }

  function refreshGalleryDebounced($scope) {
    clearTimeout(galleryTimeout);
    galleryTimeout = setTimeout(function () {
      processCards($scope);
    }, 250);
  }

  // --- EXECUTION ---
  processCards();

  // --- 4. LIGHTBOX CLOSE REDIRECTION ---
  // Intercept clicks on the prettyPhoto close button and redirect them to the
  // overlay. This ensures a unified closing sequence and bypasses library-level
  // inconsistencies. We use the capture phase to ensure we catch the event before
  // the library's internal listeners.
  document.addEventListener(
    "click",
    function (e) {
      if (e.target && e.target.closest(".pp_close")) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var overlay = document.querySelector(".pp_overlay");
        if (overlay) overlay.click();
      }
    },
    true, // useCapture
  );

  // --- 5. LIGHTBOX SWIPE NAVIGATION ---
  // Captures horizontal swipe gestures on mobile to trigger Next/Prev buttons.
  // We use the capture phase to bypass library-level stopPropagation and a
  // 50px threshold to distinguish swipes from taps.
  var touchstartX = 0;
  var touchstartY = 0;

  document.addEventListener(
    "touchstart",
    function (e) {
      // Ignore if touch started on interactive UI (Fav/Share buttons)
      if (e.target.closest("#lightbox-btn-container")) return;

      var container = e.target.closest(".pp_pic_holder");
      if (container) {
        touchstartX = e.changedTouches[0].screenX;
        touchstartY = e.changedTouches[0].screenY;
      }
    },
    { capture: true, passive: true },
  );

  document.addEventListener(
    "touchend",
    function (e) {
      if (e.target.closest("#lightbox-btn-container")) return;

      var container = e.target.closest(".pp_pic_holder");
      if (container) {
        var touchendX = e.changedTouches[0].screenX;
        var touchendY = e.changedTouches[0].screenY;

        var dX = touchendX - touchstartX;
        var dY = touchendY - touchstartY;

        // Intent detection: must be primarily horizontal and exceed 50px
        if (Math.abs(dX) > Math.abs(dY) && Math.abs(dX) > 50) {
          if (dX < 0) {
            // Swipe Left -> Next
            var $next = $(".pp_next");
            if ($next.length && $next.css("display") !== "none") $next.click();
          } else {
            // Swipe Right -> Prev
            var $prev = $(".pp_previous");
            if ($prev.length && $prev.css("display") !== "none") $prev.click();
          }
        }
      }
    },
    { capture: true, passive: true },
  );

  // YITH / theme infinite-scroll hooks. When YITH emits the event it passes
  // the appended container as the second arg, so process just that subtree.
  $(document).on(
    "yith_infs_added_elem append.infiniteScroll post-load",
    function (event, payload) {
      var $scope = null;
      if (payload && payload.jquery) $scope = payload;
      else if (payload instanceof HTMLElement) $scope = $(payload);
      refreshGalleryDebounced($scope);
    },
  );
});
