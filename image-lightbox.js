jQuery(document).ready(function ($) {

  /**
   * 0. PRE-PROCESSOR: FIX ALT ATTRIBUTES
   * Loops through each product block to sync the technical 'alt' 
   * (e.g., WC-0005) with the actual product name.
   */
  $('.block-product-inner').each(function () {
    var $card = $(this);
    // Find the text inside the h3 link (e.g., "Shades of Pink Wedding Cake")
    var actualName = $card.find('.item-title a').text().trim();
    
    // Update the alt attribute of the image within this specific card
    if (actualName) {
      $card.find('.product-image img').attr('alt', actualName);
    }
  });

  /**
   * 1. PREPARE THE GALLERY
   * This remains mostly the same, but now $img.attr('alt') 
   * will return the "Shades of Pink..." text instead of the ID.
   */
  $('#sns_woo_list .grid-view .product-image').each(function () {
    var $link = $(this);
    var $img = $link.find('img');

    var highResImage = $img.attr('data-original') || $img.attr('src');

    if (highResImage) {
      $link.attr('href', highResImage);
      $link.attr('data-rel', 'prettyPhoto[cake-gallery]');
      
      // Explicitly set the title attribute using the newly updated alt text
      // to ensure prettyPhoto displays it correctly.
      $link.attr('title', $img.attr('alt'));
    }
  });

  /**
   * 2. INITIALIZE PRETTYPHOTO
   */
  if ($.fn.prettyPhoto) {
    $("a[data-rel^='prettyPhoto']").prettyPhoto({
      hook: 'data-rel',
      social_tools: false,
      theme: 'pp_default',
      horizontal_padding: 20,
      opacity: 0.8,
      deeplinking: false,
      overlay_gallery: true,
      allow_resize: true,
      default_width: 900,
      default_height: 600
    });
  }
});
