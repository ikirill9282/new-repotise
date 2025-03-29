const swiper = new Swiper(".mySwiper", {
    slidesPerView: 5,
    spaceBetween: 20,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        320: {
            slidesPerView: 1.4,
            spaceBetween: 10,
        },
        400: {
            slidesPerView: 1.6,
            spaceBetween: 10,
        },
        500: {
            slidesPerView: 2.2,
            spaceBetween: 10,
        },
        600: {
            slidesPerView: 2.4,
            spaceBetween: 10,
        },
        700: {
            slidesPerView: 3.2,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 3.2,
            spaceBetween: 15,
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 20,
        },
        1200: {
            slidesPerView: 5,
            spaceBetween: 20,
        },
    },
});

const swiper1 = new Swiper(".mySwiper1", {
    slidesPerView: 4,
    spaceBetween: 20,
    navigation: {
        nextEl: ".mySwiper1 .swiper-button-next",
        prevEl: ".mySwiper1 .swiper-button-prev",
    },
    breakpoints: {
        320: {
            slidesPerView: 1.1,
            spaceBetween: 10,
        },
        400: {
            slidesPerView: 1.3,
            spaceBetween: 10,
        },
        500: {
            slidesPerView: 1.6,
            spaceBetween: 10,
        },
        600: {
            slidesPerView: 1.9,
            spaceBetween: 10,
        },
        700: {
            slidesPerView: 2.2,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 2.2,
            spaceBetween: 15,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
        1200: {
            slidesPerView: 4,
            spaceBetween: 20,
        },
    },
});



function toggleText() {
    let hiddenText = document.querySelector(".hidden-text");
    let dots = document.querySelector(".dots");
    let button = document.querySelector(".show-more");

    if (hiddenText.style.display === "none" || hiddenText.style.display === "") {
        hiddenText.style.display = "inline";
        dots.style.display = "none";
        button.style.display = "none";
    }
}

document.addEventListener('DOMContentLoaded', function() {
  const open_auth_items = [...document.querySelectorAll('.open_auth')];
  if (open_auth_items.length) {
    open_auth_items.map((item) => item.addEventListener('click', (evt) => {
      evt.preventDefault();
      $('#auth_modal').css("display", "flex").hide().fadeIn();
    }));
  }

  const close_auth_items = [...document.querySelectorAll('.close_auth')];
  if (close_auth_items.length) {
    close_auth_items.map((item) => item.addEventListener('click', (evt) => {
      evt.preventDefault();
      $('#auth_modal').fadeOut();
    }))
  }
  
});