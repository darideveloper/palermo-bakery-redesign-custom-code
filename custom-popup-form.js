document.addEventListener('DOMContentLoaded', () => {
    const triggerBtn = document.getElementById('form-trigger-btn');
    const closeBtn = document.getElementById('close-popup-btn');
    const formContainer = document.getElementById('popup-form-container');
    const form = document.getElementById('my-proprietary-form');
    const responseDiv = document.getElementById('popup-form-response');

    // Toggle visibility
    triggerBtn.addEventListener('click', () => {
        formContainer.classList.toggle('hidden');
    });

    closeBtn.addEventListener('click', () => {
        formContainer.classList.add('hidden');
    });

    // Handle form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Basic UI updates
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        responseDiv.classList.add('hidden');
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
                responseDiv.classList.remove('hidden');
                form.reset();
                
                // Auto close after 3 seconds
                setTimeout(() => {
                    formContainer.classList.add('hidden');
                    responseDiv.classList.add('hidden');
                }, 3000);
            } else {
                throw new Error('Failed to send message');
            }
        } catch (error) {
            responseDiv.textContent = 'Sorry, there was an error sending your message. Please try again.';
            responseDiv.classList.add('error');
            responseDiv.classList.remove('hidden');
            console.error('Form submission error:', error);
        } finally {
            submitBtn.textContent = originalBtnText;
            submitBtn.disabled = false;
        }
    });
});