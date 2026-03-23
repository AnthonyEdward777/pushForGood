(function () {
    function getMessage(field, state) {
        if (state.valueMissing && field.dataset.errorRequired) {
            return field.dataset.errorRequired;
        }

        if (state.typeMismatch && field.type === 'email' && field.dataset.errorEmail) {
            return field.dataset.errorEmail;
        }

        if (state.tooShort && field.dataset.errorMinlength) {
            return field.dataset.errorMinlength;
        }

        if (state.tooLong && field.dataset.errorMaxlength) {
            return field.dataset.errorMaxlength;
        }

        if (state.patternMismatch && field.dataset.errorPattern) {
            return field.dataset.errorPattern;
        }

        return '';
    }

    function clearFieldState(field) {
        field.setCustomValidity('');
        field.classList.remove('input-invalid');
    }

    function handleInvalid(event) {
        const field = event.target;
        const message = getMessage(field, field.validity);

        if (message) {
            field.setCustomValidity(message);
        }

        field.classList.add('input-invalid');
    }

    function wireField(field) {
        field.addEventListener('invalid', handleInvalid);
        field.addEventListener('input', function () {
            clearFieldState(field);
        });
        field.addEventListener('change', function () {
            clearFieldState(field);
        });
    }

    function init() {
        const forms = document.querySelectorAll('form[data-enhanced-validation="true"]');

        forms.forEach(function (form) {
            const fields = form.querySelectorAll('input, select, textarea');
            fields.forEach(wireField);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
