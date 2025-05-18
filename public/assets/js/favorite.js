
$(window).on("favoriteUpdated", function (evt, data) {
    const elem = $(data.element);
    const block = elem.parents(".item");
    const parent = block.parents(".tab-pane");
    const wrap = parent.find(".favorites_second");
    const empty = parent.find(".empty-block");
    const analogs = $('.favorite-button[data-key="' + elem.data("key") + '"]');

    const toggleEmpty = (callback = null) => {
        const items = wrap.find(".item");

        if (!items.length) {
            empty.fadeIn();
            empty.removeClass("hidden");
        } else {
            empty.hasClass("hidden")
                ? callback === null
                    ? true
                    : callback()
                : empty.fadeOut(() => {
                      empty.addClass("hidden");
                      callback();
                  });
        }
    };

    const hideElement = (obj, byHeight = false) => {
        if (byHeight) {
            obj.animate({ opacity: 0 });
            obj.animate(
                { height: "toggle" },
                400,
                "swing",
                () => obj.detach() && toggleEmpty()
            );
        } else {
            obj.animate({ opacity: 0 }, 400, "swing", () =>
                obj.css({ height: "0px" })
            );
            obj.animate(
                { width: "toggle", "flex-basis": "0%" },
                400,
                "swing",
                () => obj.detach() && toggleEmpty()
            );
        }
    };

    if (!data.result.value) {
        if (block.hasClass("removable")) {
            hideElement(block, data.result.type === "author");
        }

        if (analogs.length) {
            analogs.each((key, item) => {
                const analog = $(item).parents(".item");
                if (analog.hasClass("removable")) {
                    hideElement(analog);
                }
            });
        }
    } else {
        if (data.result.type === "author") {
            $.ajax({
                method: "POST",
                url: "/api/data/favorite-author",
                data: {
                    _token: getCSRF(),
                    id: data.result.model_id,
                },
            }).then((response) => {
                if (response.status) {
                    const new_block = $(response.content);
                    new_block.addClass("removable");
                    new_block.addClass("hidden");
                    FavoriteButtons().discover(new_block);
                    parent.find(".cards_why_need").prepend(new_block);
                    toggleEmpty(() => {
                        new_block.fadeOut(() =>
                            new_block.removeClass("hidden")
                        );
                    });
                }
            });
        }

        if (data.result.type === "product") {
            const clone = block.clone();
            clone.addClass("removable");
            clone.addClass("hidden");
            FavoriteButtons().discover(clone);
            wrap.find(".favorite_cards_group").prepend(clone);
            toggleEmpty(() => {
                clone.fadeOut(() => clone.removeClass("hidden"));
            });
        }
    }
});
