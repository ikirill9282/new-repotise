window.addEventListener('DOMContentLoaded', function() {
  $('.search-input').on('input', function() {

    if (!$(this).data('loading')) {
      $(this).data('loading', true);

      setTimeout(() => {
        $(this).data('loading', false);
      }, 300);

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
                // $(this).focus();
                $(this).trigger('searchItemSelected', item);
                if ($(this).data('autosubmit')) {
                  $(this).closest('form').submit();
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
    }
    
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