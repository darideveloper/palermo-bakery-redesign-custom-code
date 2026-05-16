document.addEventListener('DOMContentLoaded', () => {
  // 1. Detect if we are in the correct page
  const targetForm = document.querySelector('form[action^="/order-wedding-cake/"]')
  if (!targetForm) return

  // 2. Find all images in the page that match a specific CSS selector
  const cakeImages = document.querySelectorAll('.owl-stage-outer img, .product-image img')

  // 3. Find checkboxes
  const whiteCheckbox = document.querySelector('input[type="checkbox"][value="White"]')
  const ivoryCheckbox = document.querySelector('input[type="checkbox"][value="Ivory"]')

  if (!whiteCheckbox || !ivoryCheckbox) {
    console.warn('White or Ivory color checkboxes not found.')
    return
  }

  // Helper to change image colors
  const updateImagesColor = (oldColor, newColor) => {
    const oldColorRegex = new RegExp(oldColor, 'g')

    console.log({ cakeImages })
    cakeImages.forEach(img => {
      console.log({ img })

      // Exception: keep always the white version for 11ExquisiteWhiteWeddingCake
      if (img.src && img.src.includes('11ExquisiteWhiteWeddingCake')) {
        return
      }

      const replaceColor = (str) => {
        if (!str || !str.includes(oldColor)) return str
        
        // Temporarily map 5WhiteStucco to evade replacement, swap colors, then restore
        return str
          .replace(/5WhiteStucco/g, '__5WS__')
          .replace(oldColorRegex, newColor)
          .replace(/__5WS__/g, '5WhiteStucco')
      }

      if (img.src && img.src.includes(oldColor)) {
        console.log(`is ${oldColor.toLowerCase()} in src`)
        img.src = replaceColor(img.src)
      }

      if (img.srcset && img.srcset.includes(oldColor)) {
        console.log(`is ${oldColor.toLowerCase()} in srcset`)
        img.srcset = replaceColor(img.srcset)
      }
    })
  }

  // Helper to handle checkbox change
  const handleColorChange = (clickedCheckbox, otherCheckbox, oldColor, newColor) => {
    clickedCheckbox.addEventListener('change', () => {
      console.log(`${newColor} checkbox clicked`)
      if (clickedCheckbox.checked) {
        console.log('is checked')
        // Make them exclusive like radio buttons
        otherCheckbox.checked = false
        
        // Update images
        updateImagesColor(oldColor, newColor)
      }
    })
  }

  // 4 & 5. Attach event listeners
  handleColorChange(whiteCheckbox, ivoryCheckbox, 'Ivory', 'White')
  handleColorChange(ivoryCheckbox, whiteCheckbox, 'White', 'Ivory')
})