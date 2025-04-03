$('a.disabled').on('click', (evt) => evt.preventDefault());

const CommentWriters = function() {
  this.writers = [];
  
  this.prepareOjbect = (object) => {
    const obj = $(object);
    const input = obj.find('textarea');
    
    if (input) {
      input.on('input', function() {
        let length = $(this).val().length;
        $(this).data('length', length);
        obj.find('a.numbers').text(`${length}/1000`);
      });

      input.on('focus', () => {
        $(input).attr('rows', 5);
        $(input).data('open', true);
      });
      
      if (input.data('open')) $(input).attr('rows', 5);

      input.on('focusout', () => {
        if (!input.val().length) {
          $(input).attr('rows', 1);
          $(input).data('open', false);
        }
      });
    }
  }

  this.init = () => {
    this.writers = [...$('.write_comment')].map((writer) => {
      return this.prepareOjbect(writer);
    })

    return this;
  }

  return this.init();
}


const Editors = function() {
  this.editors = [];

  this.init = () => {
    this.editors = [...$('.editor_btn')].map((editor) => {
      $(editor).off('click');
      $(editor).on('click', function(event) {
        event.preventDefault();
        const target = $(this).data('target');
        $(`#${target}`).toggleClass('h-48');
        console.log('ok');
        
      });
    });
    return this;
  }

  return this.init();
}