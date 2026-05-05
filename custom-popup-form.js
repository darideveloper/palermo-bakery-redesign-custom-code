document.addEventListener('DOMContentLoaded', () => {
    // 1. Inject the HTML into the body
    const formHTML = `
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
                <p class="popup-intro">Do you want to order a cake or have any questions for one of our cake consultants? Fill out this form and they will contact within 24-48 hours.</p>
                <form id="my-proprietary-form" action="/contact-form/" method="post">
                    <!-- Api inputs (Hidden) -->
                    <input type="hidden" name="api_key" value="dID804XfI3tGiZEfp6mvahNsmBf1pR">
                    <input type="hidden" name="user" value="palermo">
                    <input type="hidden" name="subject" value="New contact from custom popup form">
                    <input type="hidden" name="redirect" value="https://www.darideveloper.com">

                    <!-- Contact inputs -->
                    <div class="form-field">
                        <label>Name
                            <input type="text" name="name" placeholder="Name" required>
                        </label>
                    </div>
                    <div class="form-field">
                        <label>Email
                            <input type="email" name="email" placeholder="Email" required>
                        </label>
                    </div>
                    <div class="form-field">
                        <label>Phone
                            <input type="tel" name="phone" placeholder="Phone">
                        </label>
                    </div>
                    <div class="form-field">
                        <label>Message
                            <textarea name="message" placeholder="Message" required></textarea>
                        </label>
                    </div>
                    <button type="submit" class="wpcf7-submit">Submit</button>
                </form>
                <!-- Div to show success/error messages -->
                <div id="popup-form-response" class="popup-hidden"></div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', formHTML);

    // 2. Initialize the logic after injection
    const triggerBtn = document.getElementById('form-trigger-btn');
    const closeBtn = document.getElementById('close-popup-btn');
    const formContainer = document.getElementById('popup-form-container');
    const form = document.getElementById('my-proprietary-form');
    const responseDiv = document.getElementById('popup-form-response');

    // Toggle visibility
    triggerBtn.addEventListener('click', () => {
        formContainer.classList.toggle('popup-hidden');
    });

    closeBtn.addEventListener('click', () => {
        formContainer.classList.add('popup-hidden');
    });

    // Handle form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Basic UI updates
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        responseDiv.classList.add('popup-hidden');
        responseDiv.className = ''; // Reset classes

        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                responseDiv.textContent = 'Thank you! We will contact you soon.';
                responseDiv.classList.add('success');
                responseDiv.classList.remove('popup-hidden');
                form.reset();
                
                // Auto close after 3 seconds
                setTimeout(() => {
                    formContainer.classList.add('popup-hidden');
                    responseDiv.classList.add('popup-hidden');
                }, 3000);
            } else {
                throw new Error('Failed to send message');
            }
        } catch (error) {
            responseDiv.textContent = 'Sorry, there was an error sending your message. Please try again.';
            responseDiv.classList.add('error');
            responseDiv.classList.remove('popup-hidden');
            console.error('Form submission error:', error);
        } finally {
            submitBtn.textContent = originalBtnText;
            submitBtn.disabled = false;
        }
    });
});