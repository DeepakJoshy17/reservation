document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const loginForm = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn'); // Select the submit button

    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', validatePassword);
    loginForm.addEventListener('submit', handleSubmit);

    // Email validation
    function validateEmail() {
        const value = emailInput.value.trim();
        if (!/\S+@\S+\.\S+/.test(value)) {
            showError(emailInput, 'Please enter a valid email address.');
        } else if (!value.includes('.')) {
            showError(emailInput, 'Email must contain a dot (.) after the "@" symbol.');
        } else {
            showSuccess(emailInput);
        }
        enableDisableSubmitButton(); // Enable or disable submit button based on form validity
    }

    // Password validation
    function validatePassword() {
        const value = passwordInput.value;
        if (value === '') {
            showError(passwordInput, 'Please enter a password.');
        } else {
            showSuccess(passwordInput);
        }
        enableDisableSubmitButton(); // Enable or disable submit button based on form validity
    }

    // Form submission handler
    function handleSubmit(event) {
        event.preventDefault(); // Prevent default form submission

        // Validate email and password before submitting
        validateEmail();
        validatePassword();

        // Check if both email and password are valid
        const isValidEmail = emailInput.classList.contains('is-valid');
        const isValidPassword = passwordInput.classList.contains('is-valid');

        if (isValidEmail && isValidPassword) {
            // If both email and password are valid, submit the form
            this.submit();
        }
    }

    // Show error message
    function showError(inputElement, message) {
        const feedbackElement = inputElement.nextElementSibling;
        inputElement.classList.remove('is-valid');
        inputElement.classList.add('is-invalid');
        feedbackElement.textContent = message;
    }

    // Show success state
    function showSuccess(inputElement) {
        const feedbackElement = inputElement.nextElementSibling;
        inputElement.classList.remove('is-invalid');
        inputElement.classList.add('is-valid');
        feedbackElement.textContent = '';
    }

    // Enable or disable the submit button based on form validity
    function enableDisableSubmitButton() {
        const isValidEmail = emailInput.classList.contains('is-valid');
        const isValidPassword = passwordInput.classList.contains('is-valid');

        if (isValidEmail && isValidPassword) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', 'disabled');
        }
    }
});
