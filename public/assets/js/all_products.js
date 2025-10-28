function getFiltersData(container) {
    const stars_wrap = container.find(".stars");
    const types_wrap = container.find(".type_products");
    const categories_wrap = container.find(".categories-results");
    const locations_wrap = container.find(".locations-results");
    const price = container.find(".price");
    const sale = container.find(".on_sale");
    const search = $("#search-filter");

    return {
        rating: stars_wrap.find(".active").length,
        price: {
            min: price.find("#slider-1").val(),
            max: price.find("#slider-2").val(),
        },
        categories: [
            ...categories_wrap
                .find("span")
                .map((key, item) => $(item).data("value")),
        ].join(","),
        locations: [
            ...locations_wrap
                .find("span")
                .map((key, item) => $(item).data("value")),
        ].join(","),
        sale: +$("#sale").is(":checked"),
        q: search.val() ?? "",
    };
}

function getUrlParams() {
    const params = {};
    const queryString = window.location.search.substring(1);
    if (queryString) {
        const pairs = queryString.split("&");
        $.each(pairs, function (i, pair) {
            const parts = pair.split("=");
            const key = decodeURIComponent(parts[0]);
            const value = decodeURIComponent(parts[1] || "");
            params[key] = value;
        });
    }
    return params;
}

function makeSearchableItem(data) {
    const item = $("<span>");
    const remove = $("<a>", {
        href: "#",
        class: "disabled",
    });
    remove.on("click", function (evt) {
        evt.preventDefault();
        $(this).parents("span").detach();
    });
    remove.html(
        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" fill="#667eea"></path></svg>'
    );
    item.attr("data-value", data.slug);
    item.text(data.label);
    item.append(remove);
    return item;
}

function capitalizeWords(str) {
    const replaced = str.replace(/-/g, " ");
    const capitalized = replaced
        .split(" ")
        .map(function (word) {
            if (word.length === 0) return word;
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        })
        .join(" ");
    return capitalized;
}
window.onload = function () {
    slideOne();
    slideTwo();
};

let sliderOne = document.getElementById("slider-1");
let sliderTwo = document.getElementById("slider-2");
let minGap = 0;
let sliderTrack = document.querySelector(".slider-track");
let sliderMaxValue = document.getElementById("slider-1").max;

// Функция форматирования в валюту
function formatCurrency(value) {
    const number = parseFloat(value);
    return '$' + number.toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function slideOne() {
    if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
        sliderOne.value = parseInt(sliderTwo.value) - minGap;
    }
    fillColor();
}

function slideTwo() {
    if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
        sliderTwo.value = parseInt(sliderOne.value) + minGap;
    }
    fillColor();
}

function fillColor() {
    // Форматируем значения с запятыми
    const minFormatted = formatCurrency(sliderOne.value);
    const maxFormatted = formatCurrency(sliderTwo.value);
    
    $("#min-price-7").val(minFormatted);
    $("#max-price-7").val(maxFormatted);
    
    let percent1 = (sliderOne.value / sliderMaxValue) * 100;
    let percent2 = (sliderTwo.value / sliderMaxValue) * 100;
    sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}%, rgb(252, 115, 97) ${percent1}%, rgb(252, 115, 97) ${percent2}%, #dadae5 ${percent2}%)`;
}


// Infinite Scroll
const PRODUCTS_SCROLL_OFFSET = 500;
const PRODUCTS_SPINNER_CLASS = "products-loading-spinner";
const PRODUCTS_END_CLASS = "products-end-of-list";
let loading = false;
let finished = false;
let currentPage = window.productsCurrentPage || 1;
let lastPage = window.productsLastPage || currentPage;

$(document).ready(function() {
    const $container = $(".filter_cards_group");
    if (!$container.length) return;

    syncPaginationState();

    if (currentPage >= lastPage) {
        finished = true;
        renderEndMessage();
    }

    $(window)
        .off("scroll.products")
        .on("scroll.products", function() {
            if (finished || loading) return;

            const scrolledToBottom = $(window).scrollTop() + $(window).height() >= $(document).height() - PRODUCTS_SCROLL_OFFSET;
            if (!scrolledToBottom) return;

            const nextPage = currentPage + 1;
            if (nextPage > lastPage) {
                finished = true;
                renderEndMessage();
                return;
            }

            loading = true;
            renderSpinner();

            const params = getUrlParams();
            params.page = nextPage;

            $.ajax({
                url: window.location.pathname,
                type: "GET",
                data: params,
                success: function(response) {
                    removeSpinner();

                    const parser = new DOMParser();
                    const doc = parser.parseFromString(response, "text/html");

                    updatePaginationFromDoc(doc);

                    const newItems = doc.querySelectorAll(".filter_cards_group .item");

                    if (newItems.length === 0) {
                        finished = true;
                        renderEndMessage();
                        loading = false;
                        return;
                    }

                    newItems.forEach(function(item) {
                        $container.append(item.cloneNode(true));
                    });

                    currentPage = nextPage;
                    syncMetaCurrentPage();

                    if (currentPage >= lastPage) {
                        finished = true;
                        renderEndMessage();
                    }

                    loading = false;
                },
                error: function() {
                    removeSpinner();
                    loading = false;
                },
            });
        });

    function syncPaginationState() {
        const $meta = $(".js-products-pagination").first();
        if (!$meta.length) return;

        const metaCurrent = parseInt($meta.data("current-page"), 10);
        const metaLast = parseInt($meta.data("last-page"), 10);

        if (!Number.isNaN(metaCurrent)) currentPage = metaCurrent;
        if (!Number.isNaN(metaLast)) lastPage = metaLast;
    }

    function updatePaginationFromDoc(doc) {
        const meta = doc.querySelector(".js-products-pagination");
        if (!meta) return;

        const metaLast = parseInt(meta.getAttribute("data-last-page"), 10);
        if (!Number.isNaN(metaLast)) {
            lastPage = metaLast;
        }
    }

    function syncMetaCurrentPage() {
        const $meta = $(".js-products-pagination").first();
        if ($meta.length) {
            $meta.attr("data-current-page", currentPage);
        }
    }

    function renderSpinner() {
        if ($container.find("." + PRODUCTS_SPINNER_CLASS).length) return;

        $container.append(
            '<div class="' +
                PRODUCTS_SPINNER_CLASS +
                '" style="width:100%;text-align:center;padding:20px;"><p style="color:#FC7361;font-size:16px;">Loading...</p></div>'
        );
    }

    function removeSpinner() {
        $container.find("." + PRODUCTS_SPINNER_CLASS).remove();
    }

    function renderEndMessage() {
        if ($container.find("." + PRODUCTS_END_CLASS).length) return;

        $container.append(
            '<div class="' +
                PRODUCTS_END_CLASS +
                '" style="width:100%;text-align:center;padding:20px;"><p style="color:#FC7361;font-size:16px;">Great journeys start here! Exciting travel products arriving soon</p></div>'
        );
    }
});



// function getFiltersData(container) {
//     const stars_wrap = container.find(".stars");
//     const types_wrap = container.find(".type_products");
//     const categories_wrap = container.find(".categories-results");
//     const locations_wrap = container.find(".locations-results");
//     const price = container.find(".price");
//     const sale = container.find(".on_sale");
//     const search = $("#search-filter");

//     return {
//         rating: stars_wrap.find(".active").length,
//         price: {
//             min: price.find("#slider-1").val(),
//             max: price.find("#slider-2").val(),
//         },
//         categories: [
//             ...categories_wrap
//                 .find("span")
//                 .map((key, item) => $(item).data("value")),
//         ].join(","),
//         locations: [
//             ...locations_wrap
//                 .find("span")
//                 .map((key, item) => $(item).data("value")),
//         ].join(","),
//         sale: +$("#sale").is(":checked"),
//         q: search.val() ?? "",
//     };
// }

// function getUrlParams() {
//     const params = {};
//     const queryString = window.location.search.substring(1);
//     if (queryString) {
//         const pairs = queryString.split("&");
//         $.each(pairs, function (i, pair) {
//             const parts = pair.split("=");
//             const key = decodeURIComponent(parts[0]);
//             const value = decodeURIComponent(parts[1] || "");
//             params[key] = value;
//         });
//     }
//     return params;
// }

// function makeSearchableItem(data) {
//     const item = $("<span>");
//     const remove = $("<a>", {
//         href: "#",
//         class: "disabled",
//     });

//     remove.on("click", function (evt) {
//         evt.preventDefault();
//         $(this).parents("span").detach();
//     });

//     remove.html(
//         '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none"><path d="M3 3.5C5.34315 5.84315 6.65686 7.15685 9 9.5M3 9.5C5.34315 7.15685 6.65686 5.84315 9 3.5" stroke="#A4A0A0" stroke-width="0.5" stroke-linecap="round" /> </svg>'
//     );

//     item.attr("data-value", data.slug);
//     item.text(data.label);
//     item.append(remove);

//     return item;
// }
// function capitalizeWords(str) {
//     const replaced = str.replace(/-/g, " ");
//     const capitalized = replaced
//         .split(" ")
//         .map(function (word) {
//             if (word.length === 0) return word;
//             return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
//         })
//         .join(" ");

//     return capitalized;
// }

// window.onload = function () {
//   slideOne();
//   slideTwo();
// };

// let sliderOne = document.getElementById("slider-1");
// let sliderTwo = document.getElementById("slider-2");
// let minGap = 0;
// let sliderTrack = document.querySelector(".slider-track");
// let sliderMaxValue = document.getElementById("slider-1").max;

// function slideOne() {
//     if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
//         sliderOne.value = parseInt(sliderTwo.value) - minGap;
//     }
//     fillColor();
// }
// function slideTwo() {
//     if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
//         sliderTwo.value = parseInt(sliderOne.value) + minGap;
//     }
//     fillColor();
// }
// function fillColor() {
//     $("#min-price-7").val(`$${sliderOne.value}`);
//     $("#max-price-7").val(`$${sliderTwo.value}`);

//     percent1 = (sliderOne.value / sliderMaxValue) * 100;
//     percent2 = (sliderTwo.value / sliderMaxValue) * 100;
//     sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , rgb(252, 115, 97) ${percent1}% , rgb(252, 115, 97) ${percent2}%, #dadae5 ${percent2}%)`;
//     // sliderTrack.style.background = `linear-gradient(to right, rgb(252, 115, 97) 48.56%, rgb(243, 242, 242) 48.56%);`;
// }

// // Infinite Scroll
// let loading = false;
// let currentPage = window.productsCurrentPage || 1;
// let lastPage = window.productsLastPage || 1;

// $(window).scroll(function() {
//     if (loading || currentPage >= lastPage) return;
    
//     // Проверка: пользователь долистал до конца
//     if ($(window).scrollTop() + $(window).height() >= $(document).height() - 500) {
//         loading = true;
//         currentPage++;
        
//         // Показать индикатор загрузки
//         $('.filtercards__group').append('<div class="loading-spinner text-center py-4"><p>Loading more products...</p></div>');
        
//         // Получить параметры фильтров
//         const filterData = getFiltersData($('.filters'));
//         filterData.page = currentPage;
        
//         // AJAX запрос
//         $.ajax({
//             url: window.location.pathname,
//             type: 'GET',
//             data: filterData,
//             success: function(response) {
//                 // Убрать спиннер
//                 $('.loading-spinner').remove();
                
//                 // Распарсить HTML и добавить товары
//                 const $response = $(response);
//                 const $newProducts = $response.find('.filtercards__group .item');
                
//                 if ($newProducts.length > 0) {
//                     $('.filtercards__group').append($newProducts);
//                     loading = false;
//                 } else {
//                     // Больше товаров нет
//                     currentPage = lastPage;
//                 }
//             },
//             error: function() {
//                 $('.loading-spinner').remove();
//                 loading = false;
//                 currentPage--;
//             }
//         });
//     }
// });

// // Сброс infinite scroll при изменении фильтров
// $(document).on('filter-updated', function() {
//     currentPage = 1;
//     loading = false;
// });
