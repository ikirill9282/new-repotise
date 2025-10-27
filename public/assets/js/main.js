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

const swiper2 = new Swiper(".mySwiper2", {
    slidesPerView: 2,
    spaceBetween: 10,
    navigation: {
        nextEl: ".mySwiper2 .swiper-button-next",
        prevEl: ".mySwiper2 .swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
    },
    breakpoints: {
        320: {
            slidesPerView: 1.6,
            spaceBetween: 10,
        },
        400: {
            slidesPerView: 1.9,
            spaceBetween: 10,
        },
        500: {
            slidesPerView: 2.4,
            spaceBetween: 10,
        },
        600: {
            slidesPerView: 2.9,
            spaceBetween: 10,
        },
        700: {
            slidesPerView: 3.1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 3.1,
            spaceBetween: 10,
        },
        1024: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
        1200: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
    },
});

const swiperArticles = new Swiper("#swiper-articles", {
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
    },
});

const swiperNews = new Swiper("#swiper-news", {
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
    },
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

const swiperTogether = new Swiper(".swiperTogether", {
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

const swiper13 = new Swiper(".mySwiper13", {
    slidesPerView: 4,
    spaceBetween: 6,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        320: {
            slidesPerView: 4,
            spaceBetween: 6,
        },
        768: {
            slidesPerView: 5,
            spaceBetween: 6,
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 6,
        },
        1200: {
            slidesPerView: 4,
            spaceBetween: 6,
        },
    },
});

const swiper14 = new Swiper(".mySwiper14", {
    spaceBetween: 10,
    thumbs: {
        swiper: swiper13,
    },
});

// const swiperProfileProducts = new Swiper("#profile-product-slider", {
//     slidesPerView: 3,
//     spaceBetween: 10,
    // navigation: {
    //     nextEl: ".swiper-button-next",
    //     prevEl: ".swiper-button-prev",
    // },
// });

function toggleText() {
    let hiddenText = document.querySelector(".hidden-text");
    let dots = document.querySelector(".dots");
    let button = document.querySelector(".show-more");

    if (
        hiddenText.style.display === "none" ||
        hiddenText.style.display === ""
    ) {
        hiddenText.style.display = "inline";
        dots.style.display = "none";
        button.style.display = "none";
    }
}

function updateMaxPrice(value, sliderNumber) {
    const slider = document.getElementById(`range-slider-${sliderNumber}`);
    const percentage = ((value - slider.min) / (slider.max - slider.min)) * 100;

    slider.style.background = `linear-gradient(to right, #FC7361 ${percentage}%, #F3F2F2 ${percentage}%)`;

    const formattedValue = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value);

    document.getElementById(`max-price-${sliderNumber}`).textContent =
        formattedValue;
}

const counterChanged = (elem, count) => {
    const item = $(elem).data("item");

    $.ajax({
        method: "POST",
        url: "/api/cart/count",
        data: {
            _token: getCSRF(),
            item: item,
            count: count,
        },
    }).then((response) => {
        if (response.status === "success") {
            $(".cart-counter").html(response.count);
            $(".cart-counter").removeClass("hidden");
            setCosts(response.costs);
        }
    });
};

function initModal() {
    // const open_auth_items = [...document.querySelectorAll(".open_auth")];

    // if (open_auth_items.length) {
    //     open_auth_items.map((item) =>
    //         item.addEventListener("click", (evt) => {
    //             evt.preventDefault();
    //             Livewire.dispatch("openModal", { modalName: "auth" });
    //         })
    //     );
    // }

    const open_reset_password = [
        ...document.querySelectorAll(".reset_password"),
    ];

    if (open_reset_password.length) {
        open_reset_password.map((item) =>
            item.addEventListener("click", (evt) => {
                evt.preventDefault();
                Livewire.dispatch("modal.openReset");
            })
        );
    }

    const open_cart = [...document.querySelectorAll(".open_cart")];
    if (open_cart.length) {
        open_cart.map((item) =>
            item.addEventListener("click", (evt) => {
                evt.preventDefault();
                Livewire.dispatch("modal.openCart");
            })
        );
    }
}
function goRightCart() {
  const params = new URLSearchParams(window.location.search);
  if (params.get('modal') === 'cart') {
    const cartOverlay = document.querySelector('.cartMayBe');
    if (cartOverlay) {
      cartOverlay.classList.add('goRight');
    }
  }
}

// function goRightCart() {
// 	console.log('Cart modal opened');
// 	const cartMayBeNode = document.querySelector('.cartMayBe');
// 	console.log(cartMayBeNode);
	
// 	if (cartMayBeNode && window.location.href.includes('/?modal=cart')) {
// 		console.log(cartMayBeNode);

// 		cartMayBeNode.classList.add('goRight');
// 	}
// 	let cartMayBeTrue = document.querySelector('.scrollbar-custom')
// 	console.log(cartMayBeTrue);
// 	cartMayBeTrue.style.backgroundColor = 'blue';
// 	let cartMayBeTrueOne = document.querySelector('.scrollbar-custom')
// 	console.log(cartMayBeTrueOne);
// 	const siblingHeight = 10;
// 	$('.scrollbar-custom').css({ height: siblingHeight + 'px' });
// 	console.log($('.scrollbar-custom'));
	
// }
function initCartSlider() {
    const heigth = $('.cart-order').outerHeight();
    
    const siblingHeight = $('.cart-order').outerHeight();
    if (window.outerWidth >= 768) {
      $('#cart-slider').css({ height: siblingHeight + 'px' });
    }

    let perView = 4.5;

    switch (true) {
        case heigth > 900:
            perView = 6;
            break;

        // case (heigth > 600):
        //   perView = 4.5;
        //   break;

        case heigth > 500:
            perView = 3.5;
            break;

        case heigth > 0:
            perView = 2.5;
            break;
    }

    let swiperCart = new Swiper("#cart-slider", {
        direction: "horizontal",
        // autoHeight: true,
        spaceBetween: 10,
        slidesPerView: 1.5,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        scrollbar: {
            el: ".swiper-scrollbar",
            draggable: true,
        },
        breakpoints: {
            375: {
                slidesPerView: 2.5,
            },
            576: {
                slidesPerView: 3.5,
            },
            768: {
                direction: "vertical",
                slidesPerView: perView,
            },
        },
    });
}

/**
 * Limit visible tags on product cards to keep layout tidy.
 */
function limitProductCardTags() {
    const maxVisibleTags = 2;
    const containers = document.querySelectorAll(".inf_cards");

    containers.forEach((container) => {
        if (container.dataset.tagsLimited === "true") {
            return;
        }

        const tags = Array.from(container.querySelectorAll("a"));
        if (tags.length <= maxVisibleTags) {
            return;
        }

        const hiddenTags = tags.slice(maxVisibleTags);
        hiddenTags.forEach((tag) => {
            tag.setAttribute("hidden", "hidden");
        });

        const hiddenCount = hiddenTags.length;
        if (hiddenCount > 0) {
            const indicator = document.createElement("span");
            indicator.className = "text-nowrap product-tags-more";
            indicator.textContent = `+${hiddenCount}`;
            indicator.addEventListener("click", () => {
                hiddenTags.forEach((tag) => {
                    tag.removeAttribute("hidden");
                });
                indicator.remove();
                container.dataset.tagsLimited = "expanded";
            });
            container.appendChild(indicator);
        }

        container.dataset.tagsLimited = "true";
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initModal();
    initCartSlider();
    limitProductCardTags();
});

document.querySelectorAll(".counter").forEach((counter) => {
    const minusBtn = counter.querySelector(".minus");
    const plusBtn = counter.querySelector(".plus");
    const countEl = counter.querySelector(".count");

    let count = parseInt(countEl.textContent);

    plusBtn.addEventListener("click", function () {
        count++;
        countEl.textContent = count;
        counterChanged(this.closest(".counter"), count);
    });

    minusBtn.addEventListener("click", function () {
        if (count > 1) {
            count--;
            countEl.textContent = count;
            counterChanged(this.closest(".counter"), count);
        }
    });
});

