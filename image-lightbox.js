jQuery(document).ready(function ($) {
  /**
   * 1. PREPARE THE GALLERY
   * We target only '.grid-view' to prevent the lightbox from 
   * double-counting images present in the hidden 'list-view'.
   */
  $('#sns_woo_list .grid-view .product-image').each(function () {
    var $link = $(this)
    var $img = $link.find('img')

    // Grab the high-res image URL from data-original (lazy load) or src
    var highResImage = $img.attr('data-original') || $img.attr('src')

    if (highResImage) {
      // Overwrite the href to point to the image instead of the product page
      $link.attr('href', highResImage)

      // Group images into a single gallery named 'cake-gallery'
      // This enables the slider and next/prev arrows
      $link.attr('data-rel', 'prettyPhoto[cake-gallery]')
    }
  })

  /**
   * 2. INITIALIZE PRETTYPHOTO
   * This turns the standard links into the interactive modal.
   */
  if ($.fn.prettyPhoto) {
    $("a[data-rel^='prettyPhoto']").prettyPhoto({
      hook: 'data-rel',            // Look for the data-rel attribute
      social_tools: false,         // Clean UI: Remove Twitter/Facebook buttons
      theme: 'pp_default',         // The standard theme with invisible hover arrows
      horizontal_padding: 20,      // Padding on the sides of the image
      opacity: 0.8,                // Darkness of the background overlay
      deeplinking: false,          // Prevents URL from changing when a modal opens
      overlay_gallery: true,       // Shows the small thumbnail gallery at the bottom
      allow_resize: true,          // Resize the photos if they are larger than the viewport
      default_width: 900,
      default_height: 600
    })
  }
})