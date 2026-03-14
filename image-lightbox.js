jQuery(document).ready(function ($) {
  // 1. Target all the product image links in the grid
  $('#sns_woo_list .product-image').each(function () {
    var $link = $(this)
    var $img = $link.find('img')

    // 2. Grab the high-res image URL
    var highResImage = $img.attr('data-original') || $img.attr('src')

    if (highResImage) {
      // 3. Change the link to point directly to the image
      $link.attr('href', highResImage)

      // 4. Add the attribute that triggers WooCommerce's prettyPhoto lightbox
      $link.attr('data-rel', 'prettyPhoto[cake-gallery]')
    }
  })

  // 5. Initialize the lightbox for these modified links
  if ($.fn.prettyPhoto) {
    $("a[data-rel^='prettyPhoto']").prettyPhoto({
      hook: 'data-rel',
      social_tools: false,
      theme: 'pp_default',
      horizontal_padding: 20,
      opacity: 0.8,
      deeplinking: false
    })
  }
})