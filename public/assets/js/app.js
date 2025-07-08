document.addEventListener("DOMContentLoaded", () => {
  const checkLabels = document.querySelectorAll(".check-label");

  if (checkLabels.length != 0) {
    checkLabels.forEach((label) => {
      label.addEventListener("click", function (e) {
        if (e.target.tagName.toLowerCase() === "input") return;

        const checkbox = label.querySelector('input[type="checkbox"]');
        const checkboxItem = label.querySelector(".checkbox-item");
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
          checkboxItem.classList.add("active");
        } else {
          checkboxItem.classList.remove("active");
        }
        e.preventDefault();
      });

      // При изменении состояния чекбокса (например, с клавиатуры)
      const checkbox = label.querySelector('input[type="checkbox"]');
      const checkboxItem = label.querySelector(".checkbox-item");
      checkbox.addEventListener("change", function () {
        if (checkbox.checked) {
          checkboxItem.classList.add("active");
        } else {
          checkboxItem.classList.remove("active");
        }
      });
    });
  }

  // валидация формы
  const form = document.querySelector(".verification-form__form form");
  if (form) {
    form.addEventListener("submit", function (e) {
      let valid = true;

      form.querySelectorAll(".js-error").forEach((el) => el.remove());
      form
        .querySelectorAll(".error")
        .forEach((el) => el.classList.remove("error"));

      const requiredFields = [
        { name: "user-name" },
        { name: "user-street" },
        { name: "user-city" },
        { name: "user-state" },
        { name: "user-zip" },
        { name: "user-country" },
      ];
      requiredFields.forEach((field) => {
        const input = form.querySelector(`[name="${field.name}"]`);
        if (input && !input.value.trim()) {
          input.classList.add("error");
          valid = false;
        }
      });

      const dob = form.querySelector('input[type="date"]');
      if (dob && !dob.value) {
        dob.classList.add("error");
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        form.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  }

  const phoneInput = document.querySelector("#phone");
  if (phoneInput && window.intlTelInput) {
    const iti = window.intlTelInput(phoneInput, {
      initialCountry: "us",
      preferredCountries: ["us", "ru", "ua", "kz"],
      utilsScript:
        "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js",
    });

    function updatePlaceholder() {
      const countryData = iti.getSelectedCountryData();
      const dialCode = countryData.dialCode ? `+${countryData.dialCode}` : "";
      phoneInput.placeholder = dialCode;
    }

    updatePlaceholder();

    phoneInput.addEventListener("countrychange", updatePlaceholder);
  }

  const faqItem = document.querySelectorAll(".faq__item button");

  if (faqItem.length != 0) {
    faqItem.forEach((item) => {
      item.addEventListener("click", (e) => {
        let parentItem = e.target.closest(".faq__item");

        if (parentItem.classList.contains("active")) {
            CloseFaq(parentItem);
        } else {
            OpenFaq(parentItem);
        }
      });
    });


    function CloseFaq(item = null){
        faqItem.forEach(item=>{
            item.closest('.faq__item').classList.remove('active');
        })
    }
    function OpenFaq(item){
        CloseFaq();
        item.classList.add('active');
    }
  }


  
});
