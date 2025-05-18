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
        '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none"><path d="M3 3.5C5.34315 5.84315 6.65686 7.15685 9 9.5M3 9.5C5.34315 7.15685 6.65686 5.84315 9 3.5" stroke="#A4A0A0" stroke-width="0.5" stroke-linecap="round" /> </svg>'
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
    $("#min-price-7").val(`$${sliderOne.value}`);
    $("#max-price-7").val(`$${sliderTwo.value}`);

    percent1 = (sliderOne.value / sliderMaxValue) * 100;
    percent2 = (sliderTwo.value / sliderMaxValue) * 100;
    sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , rgb(252, 115, 97) ${percent1}% , rgb(252, 115, 97) ${percent2}%, #dadae5 ${percent2}%)`;
    // sliderTrack.style.background = `linear-gradient(to right, rgb(252, 115, 97) 48.56%, rgb(243, 242, 242) 48.56%);`;
}
