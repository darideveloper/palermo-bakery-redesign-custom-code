jQuery(document).ready(function ($) {
  // --- SCRIPT GUARD ---
  if (window.customCategoryLoaderLoaded) return;
  window.customCategoryLoaderLoaded = true;

  // 1. Build the HTML for the loader and add it to the bottom of the page
  var loaderHTML = '<div id="custom-category-loader"><div class="custom-spinner"></div></div>'
  
  if ($('#custom-category-loader').length === 0) {
    $('body').append(loaderHTML)
  }

  // Move auth buttons out of hidden toolbar and place below the visible filter widget
  if ($('.gallery-auth-buttons').length > 0 && $('#woocommerce_product_categories-3').length > 0) {
    $('.gallery-auth-buttons').insertAfter('#woocommerce_product_categories-3');
  }

  // Inject Favorite Cakes pill
  $('#woocommerce_product_categories-3 ul.product-categories').prepend('<li><a href="/favorite-cakes" class="fav-pill-link">♥ Favorite Cakes</a></li>');

  // 2. Listen for clicks on the category pill buttons
  $('#woocommerce_product_categories-3 ul.product-categories').on('click', 'li a', function (e) {

    // Safety check: if the user holds CTRL/CMD to open in a new tab, don't show the spinner
    if (e.ctrlKey || e.metaKey || $(this).attr('target') === '_blank') {
      return
    }

    // Turn on the loading screen!
    $('#custom-category-loader').addClass('is-loading')

    // Safety timeout: if the next page doesn't load within 10 seconds, hide the spinner
    setTimeout(function() {
      $('#custom-category-loader').removeClass('is-loading')
    }, 10000)
  })

  // 3. Safety fallback: If the user hits the browser's "Back" button, hide the spinner
  $(window).on('pageshow', function (event) {
    if (event.originalEvent.persisted) {
      $('#custom-category-loader').removeClass('is-loading')
    }
  })
})