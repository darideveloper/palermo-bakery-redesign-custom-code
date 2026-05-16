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
        if (!str) return str

        // Special exception for the Rustic/White Stucco cake which has "Ivory" in both filenames:
        // Ivory version: 5RusticStuccoIvoryWeddingCake
        // White version: 5WhiteStuccoIvoryWeddingCake
        if (str.includes('5RusticStuccoIvoryWeddingCake') || str.includes('5WhiteStuccoIvoryWeddingCake')) {
          if (newColor === 'White') {
            return str.replace(/5RusticStuccoIvoryWeddingCake/g, '5WhiteStuccoIvoryWeddingCake')
          } else if (newColor === 'Ivory') {
            return str.replace(/5WhiteStuccoIvoryWeddingCake/g, '5RusticStuccoIvoryWeddingCake')
          }
        }

        // Special exception for the Pindots cake:
        // Ivory version: 3PindotsIvoryWeddingCake
        // White version: WPindots or WPindotsWhite
        if (str.includes('3PindotsIvoryWeddingCake') || str.includes('WPindots')) {
          if (newColor === 'White') {
            return str.replace(/3PindotsIvoryWeddingCake/g, 'WPindots')
          } else if (newColor === 'Ivory') {
            // Replace WPindotsWhite first, then WPindots, to avoid partial matches
            return str.replace(/WPindotsWhite/g, '3PindotsIvoryWeddingCake')
                      .replace(/WPindots/g, '3PindotsIvoryWeddingCake')
          }
        }

        // Special exception for the Screen-Shot cake:
        // Ivory version: Screen-Shot-2022-07-11-at-10.58.40-PM
        // White version: Screen-Shot-2022-07-11-at-10.58.32-PM
        if (str.includes('Screen-Shot-2022-07-11-at-10.58.40-PM') || str.includes('Screen-Shot-2022-07-11-at-10.58.32-PM')) {
          if (newColor === 'White') {
            return str.replace(/Screen-Shot-2022-07-11-at-10.58.40-PM/g, 'Screen-Shot-2022-07-11-at-10.58.32-PM')
          } else if (newColor === 'Ivory') {
            return str.replace(/Screen-Shot-2022-07-11-at-10.58.32-PM/g, 'Screen-Shot-2022-07-11-at-10.58.40-PM')
          }
        }

        if (!str.includes(oldColor)) return str
        return str.replace(oldColorRegex, newColor)
      }

      if (img.src) {
        img.src = replaceColor(img.src)
      }

      if (img.srcset) {
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