const swiper = new Swiper(".mySwiper", {
    slidesPerView: 5,
    spaceBetween: 20,
    autoHeight: true,
    navigation: {
      enabled: true,
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        320: {
            slidesPerView: 1.6,
            spaceBetween: 10,
        },
        430: {
            slidesPerView: 2.2,
            spaceBetween: 10,
        },
        600: {
            slidesPerView: 2.4,
            spaceBetween: 10,
        },
        700: {
            slidesPerView: 2.4,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 3.2,
            spaceBetween: 15,
        },
        800: {
            slidesPerView: 4,
            spaceBetween: 15,
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

function initAuth() {
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
}

document.addEventListener('DOMContentLoaded', function() {
  initAuth();
});


const swiperArticles = new Swiper('#swiper-articles', {
  slidesPerView: 1.2,
  spaceBetween: 10,
  enabled: true,
  breakpoints: {
    568: {
      slidesPerView: 2,
    },
    768: {
      enabled: false,
      slidesPerView: 3,
      spaceBetween: 20,
    },
  }
});

const swiperNews = new Swiper('#swiper-news', {
  slidesPerView: 1.4,
  spaceBetween: 10,
  enabled: true,
  breakpoints: {
    400: {
      slidesPerView: 1.6,
    },
    500: {
      slidesPerView: 2,
    },
    768: {
      enabled: false,
      slidesPerView: 4,
      spaceBetween: 20,
    },
    1200: {
      slidesPerView: 5,
    },
  }
});


const swiperTrending = new Swiper(".mySwiperTrending", {
  slidesPerView: 5,
  spaceBetween: 20,
  autoHeight: true,
  navigation: {
    enabled: true,
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
      320: {
          slidesPerView: 1.6,
          spaceBetween: 10,
      },
      430: {
          slidesPerView: 2.2,
          spaceBetween: 10,
      },
      600: {
          slidesPerView: 2.4,
          spaceBetween: 10,
      },
      700: {
          slidesPerView: 2.4,
          spaceBetween: 10,
      },
      768: {
          slidesPerView: 3.2,
          spaceBetween: 15,
      },
      1200: {
          slidesPerView: 5,
          spaceBetween: 20,
      },
  },
});

const swiperRecomend = new Swiper(".mySwiperRecomend", {
  slidesPerView: 5,
  spaceBetween: 20,
  autoHeight: true,
  navigation: {
    enabled: true,
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
      320: {
          slidesPerView: 1.6,
          spaceBetween: 10,
      },
      430: {
          slidesPerView: 2.2,
          spaceBetween: 10,
      },
      600: {
          slidesPerView: 2.4,
          spaceBetween: 10,
      },
      700: {
          slidesPerView: 2.4,
          spaceBetween: 10,
      },
      768: {
          slidesPerView: 3.2,
          spaceBetween: 15,
      },
      1200: {
          slidesPerView: 5,
          spaceBetween: 20,
      },
  },
});



// const stars = document.querySelectorAll('.stars span');

// stars.forEach((star, index) => {
//   star.addEventListener('click', () => {
//     stars.forEach(s => s.classList.remove('active'));

//     for (let i = 0; i <= index; i++) {
//       stars[i].classList.add('active');
//     }
//   });
// });


function updateMaxPrice(value, sliderNumber) {
    const slider = document.getElementById(`range-slider-${sliderNumber}`);
    const percentage = ((value - slider.min) / (slider.max - slider.min)) * 100;
  
    slider.style.background = `linear-gradient(to right, #FC7361 ${percentage}%, #F3F2F2 ${percentage}%)`;
  
    const formattedValue = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
    }).format(value);
  
    document.getElementById(`max-price-${sliderNumber}`).textContent = formattedValue;
}
  
document.addEventListener("DOMContentLoaded", function () {
    const sliders = document.querySelectorAll('.slider');

    sliders.forEach((slider, index) => {
        const sliderNumber = slider.id.replace(/^.*?(\d+)$/is, "$1");
        
        const initialPercentage = ((slider.value - slider.min) / (slider.max - slider.min)) * 100;
        slider.style.background = `linear-gradient(to right, #FC7361 ${initialPercentage}%, #F3F2F2 ${initialPercentage}%)`;

        const formattedValue = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        }).format(slider.value);

        document.getElementById(`max-price-${sliderNumber}`).textContent = formattedValue;
});
});







document.querySelectorAll('.counter').forEach(counter => {
    const minusBtn = counter.querySelector('.minus');
    const plusBtn = counter.querySelector('.plus');
    const countEl = counter.querySelector('.count');
  
    let count = parseInt(countEl.textContent);
  
    plusBtn.addEventListener('click', () => {
      count++;
      countEl.textContent = count;
    });
  
    minusBtn.addEventListener('click', () => {
      if (count > 1) {
        count--;
        countEl.textContent = count;
      }
    });
});