document.addEventListener('DOMContentLoaded', () => {

    const checkLabels = document.querySelectorAll('.check-label');

    if (checkLabels.length != 0) {
        checkLabels.forEach(label => {
            label.addEventListener('click', function (e) {
                if (e.target.tagName.toLowerCase() === 'input') return;

                const checkbox = label.querySelector('input[type="checkbox"]');
                const checkboxItem = label.querySelector('.checkbox-item');
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    checkboxItem.classList.add('active');
                } else {
                    checkboxItem.classList.remove('active');
                }
                e.preventDefault();
            });

            // При изменении состояния чекбокса (например, с клавиатуры)
            const checkbox = label.querySelector('input[type="checkbox"]');
            const checkboxItem = label.querySelector('.checkbox-item');
            checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    checkboxItem.classList.add('active');
                } else {
                    checkboxItem.classList.remove('active');
                }
            });
        })
    }

    // валидация формы 
    const form = document.querySelector('.verification-form__form form');
    if (form) {
        form.addEventListener('submit', function (e) {
            let valid = true;

            form.querySelectorAll('.js-error').forEach(el => el.remove());
            form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

            [...form.querySelectorAll('input')].forEach(el => {
              if (el.hasAttribute('data-required')) {
                if (!el.value.trim()) {
                  valid = false;
                  el.classList.add('error');
                }
              }
            });

            if (!valid) {
                e.preventDefault();
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    const phoneInput = document.querySelector("#phone");
    if (phoneInput && window.intlTelInput) {
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "us",
            preferredCountries: ["us", "ru", "ua", "kz"],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js"
        });

        function updatePlaceholder() {
            const countryData = iti.getSelectedCountryData();
            const dialCode = countryData.dialCode ? `+${countryData.dialCode}` : "";
            phoneInput.placeholder = dialCode;
        }

        updatePlaceholder();

        phoneInput.addEventListener("countrychange", updatePlaceholder);
    }
});