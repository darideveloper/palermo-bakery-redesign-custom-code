document.addEventListener('DOMContentLoaded', () => {
  // 1. Detect if we are in the correct page
  const targetForm = document.querySelector('form[action^="/order-wedding-cake/"]')
  if (!targetForm) return

  // 2. Find checkboxes
  const whiteCheckbox = document.querySelector('input[type="checkbox"][value="White"]')
  const ivoryCheckbox = document.querySelector('input[type="checkbox"][value="Ivory"]')

  if (!whiteCheckbox || !ivoryCheckbox) return

  let isSyncing = false;

  // Style injection for gold spinner loader
  const style = document.createElement('style')
  style.textContent = `
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
      const container = document.querySelector('.product-image-lightbox-main') || document.querySelector('.pp_content') || document.body
      if (container) {
        loader = document.createElement('div')
        loader.className = 'modal-cake-loader'
        container.appendChild(loader)
      }
    }
    return loader
  }

  // Helper to get replacement color based on target
  const replaceColor = (str, newColor, isUrl = false) => {
    if (!str) return str
    // Strip dimension suffixes from URLs (e.g. -300x300)
    if (isUrl || str.includes('/uploads/') || /\.(jpg|jpeg|png|webp|gif)/i.test(str)) {
      str = str.replace(/-\d+x\d+(?=\.(jpg|jpeg|png|webp|gif))/gi, '')
    }
    // Protect "Exquisite" variant
    if (str.includes('Exquisite')) return str

    // Legacy exceptions
    if (str.includes('5RusticStuccoIvoryWeddingCake') || str.includes('5WhiteStuccoIvoryWeddingCake')) {
      return newColor === 'White' ? str.replace(/5RusticStuccoIvoryWeddingCake/g, '5WhiteStuccoIvoryWeddingCake') : str.replace(/5WhiteStuccoIvoryWeddingCake/g, '5RusticStuccoIvoryWeddingCake')
    }
    if (str.includes('3PindotsIvoryWeddingCake') || str.includes('WPindots')) {
      if (newColor === 'White') return str.replace(/3PindotsIvoryWeddingCake/g, 'WPindots').replace(/\.jpg/g, '.png')
      return str.replace(/WPindotsWhite/g, '3PindotsIvoryWeddingCake').replace(/WPindots/g, '3PindotsIvoryWeddingCake').replace(/\.png/g, '.jpg')
    }
    if (str.includes('Screen-Shot-2022-07-11-at-10.58.40-PM') || str.includes('Screen-Shot-2022-07-11-at-10.58.32-PM')) {
      return newColor === 'White' ? str.replace(/Screen-Shot-2022-07-11-at-10.58.40-PM/g, 'Screen-Shot-2022-07-11-at-10.58.32-PM') : str.replace(/Screen-Shot-2022-07-11-at-10.58.32-PM/g, 'Screen-Shot-2022-07-11-at-10.58.40-PM')
    }

    const oldColor = newColor === 'White' ? 'Ivory' : 'White'
    return str.replace(new RegExp(oldColor, 'g'), newColor)
  }

  // Master function to sync everything to a target color
  const syncGlobalColor = (newColor) => {
    if (isSyncing) return
    isSyncing = true

    // 1. Update Grid Cakes
    const cakes = document.querySelectorAll('li.product-image-item')
    cakes.forEach(cake => {
      const targetRadio = cake.querySelector(`input[type="radio"][value*="${newColor}"]`)
      if (targetRadio) {
        // Toggle the visual active-variant class on parent LIs natively
        const targetLi = targetRadio.closest('.product-image-variant-item')
        if (targetLi) {
          const siblings = targetLi.parentNode.querySelectorAll('.product-image-variant-item')
          siblings.forEach(sib => {
            if (sib === targetLi) {
              sib.classList.add('active-variant')
            } else {
              sib.classList.remove('active-variant')
            }
          })
        }

        // Check if this specific cake was selected before we update the radio
        const wasChecked = !!cake.querySelector('input[type="radio"]:checked')
        if (wasChecked && !targetRadio.checked) {
          targetRadio.click()
        }
      } else {
        // Fallback for single-variant or legacy cakes
        const img = cake.querySelector('img:not(.cloned)')
        if (img) {
          if (img.src) img.src = replaceColor(img.src, newColor, true)
          if (img.srcset) img.srcset = replaceColor(img.srcset, newColor, true)
          if (img.alt) img.alt = replaceColor(img.alt, newColor, false)
        }
        const span = cake.querySelector('label span')
        if (span && span.textContent) {
          span.textContent = replaceColor(span.textContent, newColor, false)
        }
      }
    })

    // 2. Update Modal if open
    const modalImg = document.querySelector('.product-image-lightbox-image')
    const modalCaption = document.querySelector('.product-image-lightbox-caption')
    if (modalImg && modalImg.src) modalImg.src = replaceColor(modalImg.src, newColor, true)
    if (modalCaption && modalCaption.textContent) {
      modalCaption.textContent = replaceColor(modalCaption.textContent, newColor, false)
    }

    // Crucial: Use a small asynchronous delay to release the lock.
    // This prevents recursive loops from synchronous DOM events.
    setTimeout(() => {
      isSyncing = false
    }, 200)
  }

  // Handle global checkbox toggles
  const handleToggle = (clicked, other, color) => {
    clicked.addEventListener('change', () => {
      if (clicked.checked) {
        other.checked = false
        syncGlobalColor(color)
      }
    })
  }
  handleToggle(whiteCheckbox, ivoryCheckbox, 'White')
  handleToggle(ivoryCheckbox, whiteCheckbox, 'Ivory')

  // Sync checkboxes when a specific variant is selected (e.g. from modal navigation)
  document.addEventListener('change', (e) => {
    if (isSyncing) return
    const radio = e.target
    if (radio.tagName === 'INPUT' && radio.type === 'radio' && radio.name === 'cake') {
      const val = radio.value || ''
      if (val.toLowerCase().includes('ivory')) {
        if (ivoryCheckbox && !ivoryCheckbox.checked) {
          ivoryCheckbox.checked = true
          whiteCheckbox.checked = false
          syncGlobalColor('Ivory')
        }
      } else if (val.toLowerCase().includes('white')) {
        if (whiteCheckbox && !whiteCheckbox.checked) {
          whiteCheckbox.checked = true
          ivoryCheckbox.checked = false
          syncGlobalColor('White')
        }
      }
    }
  })

  // Modal Sync Logic
  const detectColorFromSrc = (src) => {
    if (!src) return ''
    if (src.includes('Exquisite')) return 'White'
    if (src.includes('5WhiteStuccoIvoryWeddingCake')) return 'White'
    if (src.includes('5RusticStuccoIvoryWeddingCake')) return src.includes('-300x300') ? 'White' : 'Ivory'
    if (src.includes('WPindots')) return 'White'
    if (src.includes('3PindotsIvoryWeddingCake')) return 'Ivory'
    if (src.includes('Screen-Shot-2022-07-11-at-10.58.32-PM')) return 'White'
    if (src.includes('Screen-Shot-2022-07-11-at-10.58.40-PM')) return 'Ivory'
    if (src.includes('-300x300')) return 'White'
    return src.toLowerCase().includes('ivory') ? 'Ivory' : (src.toLowerCase().includes('white') ? 'White' : '')
  }

  const observer = new MutationObserver((mutations) => {
    if (isSyncing) return
    
    let triggerSync = false
    let colorToSync = ''

    for (const m of mutations) {
      if ((m.type === 'attributes' && m.attributeName === 'src' && m.target.classList.contains('product-image-lightbox-image')) || 
          (m.type === 'childList' && document.querySelector('.product-image-lightbox-image'))) {
        
        const img = document.querySelector('.product-image-lightbox-image')
        if (img && img.src) {
          // Robust loader handling whenever the lightbox image src changes
          const oldSrc = img.getAttribute('data-prev-src') || ''
          if (oldSrc !== img.src && !img.src.includes('prod_loading') && !img.src.includes('loading')) {
            img.setAttribute('data-prev-src', img.src)
            
            const loader = getLoader()
            if (loader) loader.classList.add('is-loading')
            
            const hideLoader = () => {
              if (loader) loader.classList.remove('is-loading')
            }
            
            if (img.complete) {
              hideLoader()
            }
            img.addEventListener('load', hideLoader, { once: true })
            img.addEventListener('error', hideLoader, { once: true })
          }

          const color = detectColorFromSrc(img.src)
          if (color === 'Ivory' && !ivoryCheckbox.checked) {
            ivoryCheckbox.checked = true
            whiteCheckbox.checked = false
            colorToSync = 'Ivory'
            triggerSync = true
          } else if (color === 'White' && !whiteCheckbox.checked) {
            whiteCheckbox.checked = true
            ivoryCheckbox.checked = false
            colorToSync = 'White'
            triggerSync = true
          } else {
            // Force modal caption sync even if color matches
            const caption = document.querySelector('.product-image-lightbox-caption')
            const currentGlobal = ivoryCheckbox.checked ? 'Ivory' : 'White'
            if (caption && caption.textContent) {
              const newText = replaceColor(caption.textContent, currentGlobal, false)
              if (caption.textContent !== newText) {
                caption.textContent = newText
              }
            }
          }
        }
        if (triggerSync) break
      }
    }

    if (triggerSync && colorToSync) {
      syncGlobalColor(colorToSync)
    }
  })

  observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['src'] })

  // Initial default selection
  const setWhiteDefault = () => {
    if (whiteCheckbox && !whiteCheckbox.checked && !ivoryCheckbox.checked) {
      whiteCheckbox.checked = true
      syncGlobalColor('White')
    }
  }
  setTimeout(setWhiteDefault, 1000)
})
