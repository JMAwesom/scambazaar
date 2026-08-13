/**
 * Bazaar Contact Form Handler
 * Sends the contact form using fetch and shows a response message.
 */
(function () {
    'use strict';

    const form = document.getElementById('contactForm');
    const alertBox = document.getElementById('contactAlert');

    if (!form || !alertBox) {
        return;
    }

    function showAlert(type, message) {
        alertBox.hidden = false;
        alertBox.className = 'contact-alert ' + type;
        alertBox.textContent = message;

        alertBox.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');

        if (!submitButton) {
            return;
        }

        const originalButtonText = submitButton.textContent;

        submitButton.disabled = true;
        submitButton.textContent = 'Sending...';

        try {
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                form.reset();
                showAlert('success', data.message || 'Your query has been received.');
            } else {
                showAlert('error', data.message || 'There was a problem submitting your query.');
            }

        } catch (error) {
            showAlert('error', 'Could not send the query. Please try again.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    });

})();
