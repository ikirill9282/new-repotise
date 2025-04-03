window.addEventListener('DOMContentLoaded', function() {
  $('.search-input').on('input', function() {
    // console.log($(this).data('loading'));
    
    if (!$(this).data('loading')) {
      $(this).data('loading', true);
      $.ajax({
        method: 'GET',
        url: '/api/search?q=' + $(this).val()
      })
      .then((response) => {
        const id = $(this).data('hits');
        if (id) {
          const hits = $(`#${id}`);
          const items = response.data;
          // console.log(response.data);
          
          const formatted = [...items]
            .map((item) => {
              const span = $('<span>', {
                class: 'text-gray-700 px-4 py-2 hover:ring-2 ring-[#FC7361] hover:text-[#FC7361] hover:bg-[#FC7361]/10 cursor-pointer block transition',
                text: item.label,
              });
  
              span.on('click', (evt) => {
                $(this).val($(span).text());
                $(this).focus();
              });
  
              const row = $('<div>', {
                append: [span],
              })
  
              return row;
          })
  
          if (!$(this).val().length) {
            hits.fadeOut();
            return;
          }
  
          hits.empty();
          hits.append(formatted);
          hits.fadeIn();
        }
      })
      .then(() => {
        setTimeout(() => {
          $(this).data('loading', false);
        }, 200);
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