/**
 * JeevanRakt - Blood Donor Management System
 * Client-side JavaScript
 * Developer: Aryan Verma (Student ID: 241306064)
 */

document.addEventListener("DOMContentLoaded", function () {
    // 1. Automatic alert dismiss timer (5 seconds)
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 6000);
    });

    // 2. Phone number numeric validation check on forms
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name="phone"]');
    phoneInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });
    });

    // 3. Confirmation modal or prompt for deletion actions
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
