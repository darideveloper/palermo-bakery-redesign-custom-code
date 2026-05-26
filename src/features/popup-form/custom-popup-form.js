document.addEventListener('DOMContentLoaded', () => {
    // --- SCRIPT GUARD ---
    if (window.customPopupFormLoaded) return;
    window.customPopupFormLoaded = true;

    // 1. Inject the Popup Shell (Wrapper and Button) into the body
    const popupShellHTML = `
    <div id="custom-popup-wrapper">
        <!-- Floating Cupcake Trigger Button -->
        <button id="form-trigger-btn" aria-label="Ask Me">
            <div class="cupcake-container">
                <div class="toothpick-flag">Ask Me</div>
                <!-- PNG Cupcake Icon -->
                <img class="cupcake-img" src="https://ccdev2026.wpenginepowered.com/wp-content/uploads/2026/05/cupcake-help-icon-120.png" alt="Ask Me">
            </div>
        </button>

        <!-- Popup Form Container -->
        <div id="popup-form-container" class="popup-hidden">
            <div class="popup-header">
                <button id="close-popup-btn" aria-label="Close form">&times;</button>
            </div>
            <div class="popup-content">
                <!-- Form will be moved here -->
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', popupShellHTML);

    // 2. Locate the specific CF7 form and move it
    // Strictly target the specific form ID to avoid affecting other forms on the page
    const cf7Form = document.querySelector('#wpcf7-f1874-o1 .wpcf7-form');
    const popupContent = document.querySelector('#popup-form-container .popup-content');
    const formContainer = document.getElementById('popup-form-container');

    if (cf7Form && popupContent) {
        popupContent.appendChild(cf7Form);
        // Show the wrapper only after the form is moved (flashing prevention)
        document.getElementById('custom-popup-wrapper').style.display = 'block';
    }

    // 3. Initialize the logic after relocation
    const triggerBtn = document.getElementById('form-trigger-btn');
    const closeBtn = document.getElementById('close-popup-btn');

    // Toggle visibility
    triggerBtn.addEventListener('click', () => {
        formContainer.classList.toggle('popup-hidden');
    });

    closeBtn.addEventListener('click', () => {
        formContainer.classList.add('popup-hidden');
    });

    // 4. Listen for Contact Form 7 native events (strictly for this form)
    document.addEventListener('wpcf7mailsent', (event) => {
        // Only trigger auto-close if the submitted form matches our specific ID
        if (event.detail.contactFormId === '1874') {
            setTimeout(() => {
                formContainer.classList.add('popup-hidden');
            }, 3000);
        }
    }, false);
});

