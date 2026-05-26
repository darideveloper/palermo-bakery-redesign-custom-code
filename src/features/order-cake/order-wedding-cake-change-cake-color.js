document.addEventListener('DOMContentLoaded', () => {
  // 1. Detect if we are in the correct page
  const targetForm = document.querySelector('form[action^="/order-wedding-cake/"]')
  if (!targetForm) return

  // 2. Find all images in the page that match a specific CSS selector
  const getCakeImages = () => document.querySelectorAll('.owl-stage-outer img, .product-image img, .product-image-lightbox-image')

  // 3. Find checkboxes
  const whiteCheckbox = document.querySelector('input[type="checkbox"][value="White"]')
  const ivoryCheckbox = document.querySelector('input[type="checkbox"][value="Ivory"]')

  if (!whiteCheckbox || !ivoryCheckbox) {
    console.warn('White or Ivory color checkboxes not found.')
    return
  }

  // Helper to get replacement color based on target
  const replaceColor = (str, newColor) => {
    if (!str) return str

    // Always use the full image size (no variants) by removing dimension suffixes like -600x600
    str = str.replace(/-\d+x\d+(?=\.(jpg|jpeg|png|webp|gif))/gi, '')

    // Exception: keep always the white version for 11ExquisiteWhiteWeddingCake
    if (str.includes('11ExquisiteWhiteWeddingCake')) {
      return str
    }

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
    // Ivory version: 3PindotsIvoryWeddingCake (.jpg)
    // White version: WPindots or WPindotsWhite (.png)
    if (str.includes('3PindotsIvoryWeddingCake') || str.includes('WPindots')) {
      if (newColor === 'White') {
        return str.replace(/3PindotsIvoryWeddingCake/g, 'WPindots')
          .replace(/\.jpg/g, '.png')
      } else if (newColor === 'Ivory') {
        // Replace WPindotsWhite first, then WPindots, to avoid partial matches
        return str.replace(/WPindotsWhite/g, '3PindotsIvoryWeddingCake')
          .replace(/WPindots/g, '3PindotsIvoryWeddingCake')
          .replace(/\.png/g, '.jpg')
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

    const oldColor = newColor === 'White' ? 'Ivory' : 'White'
    const oldColorRegex = new RegExp(oldColor, 'g')
    if (!str.includes(oldColor)) return str
    return str.replace(oldColorRegex, newColor)
  }

  // Helper to change image colors
  const updateImagesColor = (oldColor, newColor) => {
    const cakeImages = getCakeImages()
    console.log({ cakeImages })
    cakeImages.forEach(img => {
      // Exception: keep always the white version for 11ExquisiteWhiteWeddingCake
      if (img.src && img.src.includes('11ExquisiteWhiteWeddingCake')) {
        return
      }

      const isModalImage = img.classList.contains('product-image-lightbox-image')
      const loader = isModalImage ? getLoader() : null

      if (img.src) {
        const oldSrc = img.src
        const newSrc = replaceColor(oldSrc, newColor)
        
        if (oldSrc !== newSrc) {
          if (isModalImage && loader) {
            console.log('Showing loader for modal image color change...')
            loader.classList.add('is-loading')
            const hideLoader = () => {
              console.log('Modal image loaded after color change.')
              loader.classList.remove('is-loading')
            }
            img.addEventListener('load', hideLoader, { once: true })
            img.addEventListener('error', hideLoader, { once: true })
          }
          img.src = newSrc
        }
      }

      if (img.srcset) {
        img.srcset = replaceColor(img.srcset, newColor)
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

  // --- MODAL SYNC LOGIC ---

  // Inject styles for the loader
  const style = document.createElement('style')
  style.textContent = `
    .product-image-lightbox-main { position: relative; }
    .modal-cake-loader {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 40px;
      height: 40px;
      border: 4px solid rgba(0, 0, 0, 0.1);
      border-top: 4px solid #d4af37; /* Gold color matching theme */
      border-radius: 50%;
      animation: modal-spin 0.8s linear infinite;
      z-index: 10001;
      display: none;
      pointer-events: none;
    }
    .modal-cake-loader.is-loading { display: block; }
    @keyframes modal-spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
  `
  document.head.appendChild(style)

  const getLoader = () => {
    let loader = document.querySelector('.modal-cake-loader')
    if (!loader) {
      const container = document.querySelector('.product-image-lightbox-main')
      if (container) {
        loader = document.createElement('div')
        loader.className = 'modal-cake-loader'
        container.appendChild(loader)
      }
    }
    return loader
  }

  const detectColorFromSrc = (src) => {
    if (!src) return '';
    
    // Check exceptions first
    if (src.includes('11ExquisiteWhiteWeddingCake')) {
      return 'White';
    }
    if (src.includes('5WhiteStuccoIvoryWeddingCake')) {
      return 'White';
    }
    if (src.includes('5RusticStuccoIvoryWeddingCake')) {
      return src.includes('-300x300') ? 'White' : 'Ivory';
    }
    if (src.includes('WPindots')) {
      return 'White';
    }
    if (src.includes('3PindotsIvoryWeddingCake')) {
      return 'Ivory';
    }
    if (src.includes('Screen-Shot-2022-07-11-at-10.58.32-PM')) {
      return 'White';
    }
    if (src.includes('Screen-Shot-2022-07-11-at-10.58.40-PM')) {
      return 'Ivory';
    }

    if (src.includes('-300x300')) {
      return 'White';
    }
    if (src.includes('White')) {
      return 'White';
    }
    if (src.includes('Ivory')) {
      return 'Ivory';
    }
    
    return '';
  };

  const handleModalImageSrcChange = (img) => {
    const whiteCheckbox = document.querySelector('input[type="checkbox"][value="White"]')
    const ivoryCheckbox = document.querySelector('input[type="checkbox"][value="Ivory"]')

    const oldSrc = img.src || '';
    if (!oldSrc || oldSrc.includes('prod_loading') || oldSrc.includes('loading')) {
      return;
    }

    // 1. Detect if the src represents a specific color variant
    const detectedColor = detectColorFromSrc(oldSrc);
    if (detectedColor) {
      if (detectedColor === 'Ivory' && ivoryCheckbox && !ivoryCheckbox.checked) {
        console.log('MUTATION_OBSERVER_SYNC: Detected Ivory variant in src, checking Ivory checkbox');
        ivoryCheckbox.click();
        return; // The click event will trigger a new color sync
      } else if (detectedColor === 'White' && whiteCheckbox && !whiteCheckbox.checked) {
        console.log('MUTATION_OBSERVER_SYNC: Detected White variant in src, checking White checkbox');
        whiteCheckbox.click();
        return; // The click event will trigger a new color sync
      }
    }

    // 2. Fall back to master color setting if no color is detected
    let targetColor = ''
    if (ivoryCheckbox && ivoryCheckbox.checked) {
      targetColor = 'Ivory'
    } else if (whiteCheckbox && whiteCheckbox.checked) {
      targetColor = 'White'
    }

    if (!targetColor) return;

    const newSrc = replaceColor(oldSrc, targetColor);
    if (oldSrc !== newSrc) {
      console.log('MUTATION_OBSERVER_SYNC: Syncing modal image SRC from:', oldSrc, 'to:', newSrc);
      const loader = getLoader();
      if (loader) loader.classList.add('is-loading');

      const hideLoader = () => {
        if (loader) loader.classList.remove('is-loading');
      };

      // Handle cache hit: if image is complete, hide loader immediately
      if (img.complete) {
        hideLoader();
      }

      img.addEventListener('load', hideLoader, { once: true });
      img.addEventListener('error', hideLoader, { once: true });

      img.src = newSrc;
    }
  };

  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
        const target = mutation.target;
        if (target.classList && target.classList.contains('product-image-lightbox-image')) {
          handleModalImageSrcChange(target);
        }
      } else if (mutation.type === 'childList') {
        const img = document.querySelector('.product-image-lightbox-image');
        if (img) {
          handleModalImageSrcChange(img);
        }
      }
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
    attributes: true,
    attributeFilter: ['src']
  });

  // --- END MODAL SYNC LOGIC ---

  // 4 & 5. Attach event listeners
  handleColorChange(whiteCheckbox, ivoryCheckbox, 'Ivory', 'White')
  handleColorChange(ivoryCheckbox, whiteCheckbox, 'White', 'Ivory')

  // Native listener for cake radio selection change to sync color checkboxes
  const cakeRadios = document.querySelectorAll('input[type="radio"][name="cake"]')
  cakeRadios.forEach(radio => {
    radio.addEventListener('change', () => {
      if (radio.checked) {
        const val = radio.value || ''
        console.log('Cake radio changed natively:', val)
        const whiteCheckbox = document.querySelector('input[type="checkbox"][value="White"]')
        const ivoryCheckbox = document.querySelector('input[type="checkbox"][value="Ivory"]')
        if (val.toLowerCase().includes('ivory')) {
          if (ivoryCheckbox && !ivoryCheckbox.checked) {
            console.log('Syncing main page checkbox to Ivory natively')
            ivoryCheckbox.click()
          }
        } else if (val.toLowerCase().includes('white')) {
          if (whiteCheckbox && !whiteCheckbox.checked) {
            console.log('Syncing main page checkbox to White natively')
            whiteCheckbox.click()
          }
        }
      }
    })
  })

  // jQuery prop interceptor to sync programmatically checked radios (such as from next/prev buttons in modal)
  if (window.jQuery) {
    const originalProp = window.jQuery.fn.prop;
    window.jQuery.fn.prop = function(name, value) {
      const result = originalProp.apply(this, arguments);
      if (name === 'checked' && value === true && this.is('input[type="radio"][name="cake"]')) {
        const val = this.val() || '';
        console.log('JQUERY_PROP_SYNC: Cake radio checked programmatically via jQuery:', val);
        
        const whiteCheckbox = document.querySelector('input[type="checkbox"][value="White"]')
        const ivoryCheckbox = document.querySelector('input[type="checkbox"][value="Ivory"]')

        if (val.toLowerCase().includes('ivory')) {
          if (ivoryCheckbox && !ivoryCheckbox.checked) {
            console.log('Syncing main page checkbox to Ivory via jQuery intercept')
            ivoryCheckbox.click();
          }
        } else if (val.toLowerCase().includes('white')) {
          if (whiteCheckbox && !whiteCheckbox.checked) {
            console.log('Syncing main page checkbox to White via jQuery intercept')
            whiteCheckbox.click();
          }
        }
      }
      return result;
    };
  }

  // 6. Set White as default after a delay to ensure other scripts (like CF7) are done
  const setWhiteDefault = () => {
    if (whiteCheckbox && !whiteCheckbox.checked && !ivoryCheckbox.checked) {
      console.log('Setting White as default (robust)...')
      whiteCheckbox.click()
    }
  }

  // Run with different delays to ensure it sticks
  setTimeout(setWhiteDefault, 500)
  setTimeout(setWhiteDefault, 1000)
  setTimeout(setWhiteDefault, 2000)

  window.addEventListener('load', () => {
    setTimeout(setWhiteDefault, 500)
  })
})
