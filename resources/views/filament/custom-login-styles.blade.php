<style>
    /* Custom Login Form - Ultra Minimalis & Compact */
    .fi-simple-main {
        max-width: 320px !important;
    }

    @media (min-width: 768px) {
        .fi-simple-main {
            max-width: 300px !important;
        }
    }

    /* Ultra Compact Spacing */
    .fi-simple-page {
        padding: 0.75rem !important;
    }

    .fi-simple-main .fi-section {
        padding: 0.875rem !important;
    }

    /* Super Small Input Fields */
    .fi-simple-main .fi-input-wrp {
        padding: 0.375rem 0.5rem !important;
        min-height: auto !important;
    }

    .fi-simple-main .fi-input {
        font-size: 0.813rem !important;
        padding: 0 !important;
    }

    /* Ultra Compact Form Fields */
    .fi-simple-main .fi-fo-field-wrp {
        margin-bottom: 0.5rem !important;
    }

    .fi-simple-main .fi-fo-field-wrp-label {
        margin-bottom: 0.25rem !important;
    }

    .fi-simple-main .fi-fo-field-wrp-label .fi-fo-field-wrp-label-text {
        font-size: 0.75rem !important;
    }

    /* Compact Button */
    .fi-simple-main .fi-btn {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.813rem !important;
        min-height: auto !important;
    }

    /* Compact Heading */
    .fi-simple-main .fi-header-heading {
        font-size: 1rem !important;
        margin-bottom: 0.5rem !important;
        font-weight: 600 !important;
    }

    /* Remove extra spacing */
    .fi-simple-main .fi-form-component-container {
        margin: 0 !important;
    }

    /* Compact checkbox/remember me */
    .fi-simple-main .fi-checkbox-wrp {
        padding: 0.25rem !important;
    }

    .fi-simple-main .fi-fo-checkbox {
        font-size: 0.75rem !important;
    }

    /* Compact form actions */
    .fi-simple-main .fi-form-actions {
        margin-top: 0.75rem !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ganti heading login
        const heading = document.querySelector('.fi-simple-main .fi-header-heading');
        if (heading) {
            heading.textContent = 'Masuk ke akun Anda';
        }
    });
</script>
