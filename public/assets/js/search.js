window.addEventListener('DOMContentLoaded', function() {
  const SEARCH_ERROR_TEXT = 'Please enter a search term.';

  const resolveErrorElement = ($form) => {
    if (!$form || !$form.length) {
      return $();
    }

    let $error = $form.data('searchErrorEl');
    if ($error && $error.length) {
      return $error;
    }

    $error = $form.next('.search-error');
    if (!$error.length) {
      $error = $form.find('.search-error');
    }

    if (!$error.length) {
      $error = $('<div>', {
        class: 'search-error text-sm text-red-500 mt-2',
        text: SEARCH_ERROR_TEXT,
      }).addClass('hidden');

      $form.after($error);
    } else if (!$error.text().trim().length) {
      $error.text(SEARCH_ERROR_TEXT);
    }

    $form.data('searchErrorEl', $error);
    return $error;
  };

  $('.search-form').each(function() {
    const $form = $(this);
    const $error = resolveErrorElement($form);
    $error.addClass('hidden').text(SEARCH_ERROR_TEXT);
  });

  $('.search-form').on('submit', function(evt) {
    const $form = $(this);
    const $input = $form.find('.search-input').first();
    const value = ($input.val() || '').trim();
    const $error = resolveErrorElement($form);

    if (!value.length) {
      evt.preventDefault();
      evt.stopPropagation();
      $error.text(SEARCH_ERROR_TEXT).removeClass('hidden');
      $input.focus();
      return false;
    }

    $error.addClass('hidden');
    return true;
  });

  $('.search-input').on('input', function() {
    const searchForm = $(this).closest('.search-form');
    const error = resolveErrorElement(searchForm);
    error.addClass('hidden');

    $.ajax({
        method: 'GET',
        url: '/api/search?q=' + $(this).val() + ($(this).data('source')?.length ? "&source=" + $(this).data('source') : '')
      })
      .then((response) => {
        const id = $(this).data('hits');
        if (id) {
          const hits = $(`#${id}`);
          const items = response.data ?? [];
          
          const formatted = [...items]
            .map((item) => {
              const span = $('<span>', {
                class: 'text-left text-gray-700 px-4 py-2 hover:ring-2 ring-[#FC7361] hover:text-[#FC7361] hover:bg-[#FC7361]/10 cursor-pointer block transition',
                text: item.label,
              });
  
              span.on('click', (evt) => {
                $(this).val($(span).text());
                $(this).trigger('searchItemSelected', item);

                const autoSubmit = $(this).data('autosubmit');
                const searchForm = $(this).closest('form');
                resolveErrorElement(searchForm).addClass('hidden');
                
                if ((autoSubmit === undefined || autoSubmit !== false) && searchForm.length) {
                  searchForm.trigger('submit');
                }
              });
  
              const row = $('<div>', {
                append: [span],
              })
  
              return row;
          })
          
          if (!formatted.length) {
            const span = $('<span>', {
              class: 'text-left text-gray-700 px-4 py-2 block',
              text: 'No Search Results...',
            });
            const row = $('<div>', {
              append: [span],
            })

            hits.empty();
            hits.append(row);
            hits.fadeIn();

            return ;
          }

          if (!$(this).val().length) {
            hits.fadeOut();
            return;
          }
  
          hits.empty();
          hits.append(formatted);
          hits.fadeIn();
        }
      });
    // }
    
    $(this).on('focus', function() {
      const id = $(this).data('hits');
      if (id && $(this).val().length) {
        $(`#${id}`).fadeIn();
      }
    });

    $(this).on('focusout', function() {
      setTimeout(() => {
        $(`#${$(this).data('hits')}`).fadeOut();
      }, 200);
    });
  });
});
