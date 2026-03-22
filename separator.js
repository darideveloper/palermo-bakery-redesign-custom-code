window.addEventListener('load', function () {

  // 1. Define the two image paths
  const img1 = "/wp-content/uploads/2026/03/separator.webp"
  const img2 = "/wp-content/uploads/2026/03/separator-2-small-no-logo.webp"

  // 2. Target your specific Visual Composer sections
  const targetSections = document.querySelectorAll('.sns_slideshow .vc_custom_1651551101010, .vc_custom_1501581736879, .vc_custom_1651551101010')

  console.log('Target sections found:', targetSections.length)

  targetSections.forEach((section, index) => {
    // 3. Alternate logic: if index is even use img1, if odd use img2
    const selectedImg = (index % 2 === 0) ? img1 : img2

    const separatorHTML = `
      <div class="custom-image-separator" data-aos="fade-in">
          <img src="${selectedImg}" 
              style="width: 100%; display: block; height: auto; margin: 70px 0; opacity: 0.3;" 
              alt="Section Separator">
      </div>
    `

    // Place the image AFTER the specific section
    section.insertAdjacentHTML('afterend', separatorHTML)
  })

  // 4. Initialize/Refresh AOS
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 1200,
      once: true
    })
  } else {
    console.warn("AOS library not found.")
  }

})