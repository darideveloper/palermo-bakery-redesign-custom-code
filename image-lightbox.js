jQuery(document).ready(function ($) {
  // --- 1. SCRIPT GUARD ---
  if (window.cakeGalleryScriptLoaded) return;
  window.cakeGalleryScriptLoaded = true;

  // --- 0. THUMBNAIL SENTINEL GUARD ---
  // PHP appends "?t=300" so any rogue URL-rewriter whose regex anchors to
  // .jpg$/.png$/etc. is a no-op on our thumbnails. This MutationObserver
  // is a fallback in case anything else swaps src back to a full-size URL.
  var THUMB_SUFFIX = "-300x300";
  var SENTINEL = "?t=300";
  function enforceThumb(img) {
    var src = img.getAttribute("src") || "";
    if (src.indexOf(SENTINEL) !== -1) return; // already protected
    var m = src.match(/^([^?]+?)(?:-\d+x\d+)?\.(jpg|jpeg|png|webp)(?:\?.*)?$/i);
    if (!m) return;
    if (src.indexOf("/uploads/") === -1) return;
    img.setAttribute("src", m[1] + THUMB_SUFFIX + "." + m[2] + SENTINEL);
  }
  function enforceAllThumbs() {
    var nodes = document.querySelectorAll("#sns_woo_list .product-image img");
    for (var i = 0; i < nodes.length; i++) enforceThumb(nodes[i]);
  }

  // Persistent guard: watch every gallery img for src mutations and revert
  // immediately. The check inside enforceThumb prevents recursion (it skips
  // imgs whose src already contains the sentinel).
  var srcGuard = null;
  if (window.MutationObserver) {
    srcGuard = new MutationObserver(function (records) {
      for (var i = 0; i < records.length; i++) {
        var t = records[i].target;
        if (t && t.tagName === "IMG") enforceThumb(t);
      }
    });
  }
  function attachSrcGuard(scope) {
    if (!srcGuard) return;
    var root = scope && scope.length ? scope[0] : document;
    var nodes = root.querySelectorAll
      ? root.querySelectorAll("#sns_woo_list .product-image img, .product-image img")
      : [];
    for (var i = 0; i < nodes.length; i++) {
      srcGuard.observe(nodes[i], { attributes: true, attributeFilter: ["src"] });
    }
  }

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
   * iOS Safari freeze fix: swap the spinner-GIF src for the real thumbnail,
   * mark the img as native-lazy, and strip the `.lazy` class so the legacy
   * jquery.lazyload scroll-handler ignores it. 300+ animated GIFs decoding
   * in parallel is what was locking up the main thread.
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
      var isOptimized = originalSrc.indexOf("-300x300") !== -1;
      var thumbnailSrc = isOptimized
        ? originalSrc
        : originalSrc.replace(/(.*)(\.(?:jpg|jpeg|png|webp))$/i, "$1-300x300$2");

      var fullResSrc =
        $img.attr("data-lightbox-src") ||
        originalSrc.replace(/-300x300(?=\.\w+$)/i, "");

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

      // Replace the animated spinner GIF with the real thumbnail. With
      // loading="lazy", the browser only fetches when near viewport.
      if (isSpinner) {
        $img.attr("src", thumbnailSrc);
      }

      // Disarm jquery.lazyload for this img — its scroll handler is the
      // other half of the iOS freeze.
      $img.removeClass("lazy");
      $img.attr("data-original", thumbnailSrc);
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
      enforceAllThumbs();
      attachSrcGuard($scope);
    }, 250);
  }

  // --- EXECUTION ---
  processCards();

  // Belt-and-braces: PHP already added the sentinel query string. These
  // calls only matter for imgs PHP missed (none in the gallery, but
  // defensive). The MutationObserver catches any future src swap.
  enforceAllThumbs();
  attachSrcGuard();
  setTimeout(function () { enforceAllThumbs(); attachSrcGuard(); }, 500);
  setTimeout(function () { enforceAllThumbs(); attachSrcGuard(); }, 1500);

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
