document.addEventListener('DOMContentLoaded', function() {
    const resetPassForm = document.getElementById('resetpass-form');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    // Add event listeners for input fields
    newPasswordInput.addEventListener('input', validatePasswords);
    confirmPasswordInput.addEventListener('input', validatePasswords);

    // Add event listener for form submission
    resetPassForm.addEventListener('submit', function(event) {
        if (!validatePasswords()) {
            event.preventDefault(); // Prevent form submission if passwords do not match
        }
    });

    function validatePasswords() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        if (newPassword !== confirmPassword) {
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
});
