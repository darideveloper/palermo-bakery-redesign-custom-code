document.addEventListener('DOMContentLoaded', () => {
    // 1. Inject the Popup Shell (Wrapper and Button) into the body
    const popupShellHTML = `
    <div id="custom-popup-wrapper">
        <!-- Floating Cupcake Trigger Button -->
        <button id="form-trigger-btn" aria-label="Ask Me">
            <div class="cupcake-container">
                <div class="toothpick-flag">Ask Me</div>
                <!-- Simple SVG Cupcake -->
                <svg viewBox="0 0 100 100" class="cupcake-svg" xmlns="http://www.w3.org/2000/svg">
                    <!-- Cherry -->
                    <circle cx="50" cy="15" r="8" fill="#d93025" />
                    <!-- Frosting -->
                    <path d="M 20 50 Q 20 20, 50 25 Q 80 20, 80 50 Q 85 55, 80 60 L 20 60 Q 15 55, 20 50 Z" fill="#ffb6c1" />
                    <!-- Base -->
                    <path d="M 25 60 L 30 95 L 70 95 L 75 60 Z" fill="#d2b48c" />
                    <line x1="35" y1="60" x2="38" y2="95" stroke="#a0522d" stroke-width="2" />
                    <line x1="45" y1="60" x2="46" y2="95" stroke="#a0522d" stroke-width="2" />
                    <line x1="55" y1="60" x2="54" y2="95" stroke="#a0522d" stroke-width="2" />
                    <line x1="65" y1="60" x2="62" y2="95" stroke="#a0522d" stroke-width="2" />
                </svg>
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

