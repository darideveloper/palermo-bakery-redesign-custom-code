jQuery(document).ready(function ($) {
  // 1. Build the HTML for the loader and add it to the bottom of the page
  var loaderHTML = '<div id="custom-category-loader"><div class="custom-spinner"></div></div>'
  $('body').append(loaderHTML)

  // 2. Listen for clicks on the category pill buttons
  $('#woocommerce_product_categories-3 ul.product-categories li a').on('click', function (e) {

    // Safety check: if the user holds CTRL/CMD to open in a new tab, don't show the spinner
    if (e.ctrlKey || e.metaKey || $(this).attr('target') === '_blank') {
      return
    }

    // Turn on the loading screen!
    $('#custom-category-loader').addClass('is-loading')
  })

  // 3. Safety fallback: If the user hits the browser's "Back" button, hide the spinner
  $(window).on('pageshow', function (event) {
    if (event.originalEvent.persisted) {
      $('#custom-category-loader').removeClass('is-loading')
    }
  })
})