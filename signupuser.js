document.addEventListener('DOMContentLoaded', function() {
    const signUpForm = document.getElementById('signup-form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneNumberInput = document.getElementById('phoneNumber');
    const addressInput = document.getElementById('address');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const submitBtn = document.getElementById('submitBtn');

    // Add event listeners for input fields
    nameInput.addEventListener('input', validateForm);
    emailInput.addEventListener('input', validateForm);
    phoneNumberInput.addEventListener('input', validateForm);
    addressInput.addEventListener('input', validateForm);
    passwordInput.addEventListener('input', validateForm);
    confirmPasswordInput.addEventListener('input', validateForm);

    // Add event listener for form submission
    signUpForm.addEventListener('submit', function(event) {
        if (!isFormValid()) {
            event.preventDefault(); // Prevent form submission if the form is not valid
            // Optionally, you can display a message to the user indicating that there are validation errors.
        }
    });

    // Validation functions...
    function validateForm() {
        const isValid = (
            validateName() &&
            validateEmail() &&
            validatePhoneNumber() &&
            validateAddress() &&
            validatePassword() &&
            validateConfirmPassword()
        );

        // Enable or disable the submit button based on form validity
        if (isValid) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', 'disabled');
        }
    }

    function validateName() {
        const value = nameInput.value.trim();
        if (value === '') {
            showError(nameInput, 'Please enter your full name.');
            return false;
        } else {
            showSuccess(nameInput);
            return true;
        }
    }

    function validateEmail() {
        const value = emailInput.value.trim();
        if (!/\S+@\S+\.\S+/.test(value)) {
            showError(emailInput, 'Please enter a valid email address.');
            return false;
        } else {
            showSuccess(emailInput);
            return true;
        }
    }

    function validatePhoneNumber() {
        const value = phoneNumberInput.value.trim();
        if (!/^\d{10}$/.test(value)) {
            showError(phoneNumberInput, 'Please enter a valid 10-digit phone number.');
            return false;
        } else {
            showSuccess(phoneNumberInput);
            return true;
        }
    }

    function validateAddress() {
        const value = addressInput.value.trim();
        if (value === '') {
            showError(addressInput, 'Please enter your address.');
            return false;
        } else {
            showSuccess(addressInput);
            return true;
        }
    }

    function validatePassword() {
        const value = passwordInput.value;
        if (value === '') {
            showError(passwordInput, 'Please enter a password.');
            return false;
        } else {
            showSuccess(passwordInput);
            return true;
        }
    }

    function validateConfirmPassword() {
        const passwordValue = passwordInput.value;
        const confirmPasswordValue = confirmPasswordInput.value;
        if (confirmPasswordValue !== passwordValue) {
            showError(confirmPasswordInput, 'Passwords do not match.');
            return false;
        } else {
            showSuccess(confirmPasswordInput);
            return true;
        }
    }

    function showError(inputElement, message) {
        const feedbackElement = inputElement.nextElementSibling;
        inputElement.classList.remove('is-valid');
        inputElement.classList.add('is-invalid');
        feedbackElement.textContent = message;
    }

    function showSuccess(inputElement) {
        const feedbackElement = inputElement.nextElementSibling;
        inputElement.classList.remove('is-invalid');
        inputElement.classList.add('is-valid');
        feedbackElement.textContent = '';
    }

    function isFormValid() {
        // Check if all input fields are valid
        const isValid = (
            validateName() &&
            validateEmail() &&
            validatePhoneNumber() &&
            validateAddress() &&
            validatePassword() &&
            validateConfirmPassword()
        );

        return isValid;
    }
});
