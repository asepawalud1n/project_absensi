<style>
    /* Custom Login Form - Minimalis & Proporsional */
    .fi-simple-main {
        max-width: 420px !important;
    }

    @media (min-width: 768px) {
        .fi-simple-main {
            max-width: 400px !important;
        }
    }

    /* Spacing yang Nyaman */
    .fi-simple-page {
        padding: 1.5rem !important;
    }

    .fi-simple-main .fi-section {
        padding: 1.75rem !important;
        border-radius: 0.75rem !important;
    }

    /* Input Fields yang Nyaman */
    .fi-simple-main .fi-input-wrp {
        padding: 0.625rem 0.875rem !important;
        min-height: 2.75rem !important;
        border-radius: 0.5rem !important;
    }

    .fi-simple-main .fi-input {
        font-size: 0.9375rem !important;
    }

    /* Form Fields dengan Spacing Baik */
    .fi-simple-main .fi-fo-field-wrp {
        margin-bottom: 1rem !important;
    }

    .fi-simple-main .fi-fo-field-wrp-label {
        margin-bottom: 0.5rem !important;
    }

    .fi-simple-main .fi-fo-field-wrp-label .fi-fo-field-wrp-label-text {
        font-size: 0.875rem !important;
        font-weight: 500 !important;
    }

    /* Button yang Proporsional */
    .fi-simple-main .fi-btn {
        padding: 0.625rem 1.25rem !important;
        font-size: 0.9375rem !important;
        min-height: 2.75rem !important;
        font-weight: 600 !important;
        border-radius: 0.5rem !important;
    }

    /* Heading yang Jelas */
    .fi-simple-main .fi-header-heading {
        font-size: 1.5rem !important;
        margin-bottom: 1.25rem !important;
        font-weight: 700 !important;
        text-align: center !important;
        color: #1f2937 !important;
    }

    .dark .fi-simple-main .fi-header-heading {
        color: #f9fafb !important;
    }

    /* Checkbox yang Nyaman */
    .fi-simple-main .fi-checkbox-wrp {
        padding: 0.5rem !important;
    }

    .fi-simple-main .fi-fo-checkbox {
        font-size: 0.875rem !important;
    }

    /* Form actions spacing */
    .fi-simple-main .fi-form-actions {
        margin-top: 1.5rem !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ganti heading login
        const heading = document.querySelector('.fi-simple-main .fi-header-heading');
        if (heading) {
            heading.textContent = 'Masuk ke Akun Anda';
        }
    });
</script>
