$('a.disabled').on('click', (evt) => evt.preventDefault());

const CommentWriters = function() {
  this.writers = [];
  
  this.prepareOjbect = (object) => {
    const obj = $(object);
    const input = obj.find('textarea');
    
    if (input) {
      input.on('input', function(event) {
        let length = $(this).val().length;
        
        if (length > 1000) {
          $(this).val($(this).val().slice(0, 1000));
          return;
        }

        $(this).data('length', length);
        obj.find('a.numbers').text(`${length}/1000`);
      });

      input.on('focus', () => {
        $(input).animate({ 'height': '240px' });
        // $(input).attr('rows', 5);
        // $(input).slideToggle();
        $(input).data('open', true);
      });
      
      if (input.data('open')) $(input).attr('rows', 5);

      input.on('focusout', () => {
        if (!input.val().length) {
          $(input).animate({ 'height': '20px' });
          // $(input).attr('rows', 1);
          // $(input).slideToggle();
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
      });
    });
    return this;
  }

  return this.init();
}

const makeParallax = function() {
  this.root;
  this.top;
  this.height;
  this.img;

  this.createParallax = (obj) => {
    this.root = $(obj);
    this.top = Math.ceil($('header').outerHeight())
    this.height =  Math.ceil(this.root.outerHeight());
    this.img = this.root.data('img');
    
    this.setStyles();
  }

  this.setStyles = () => {
    if (this.root.hasClass('parallax-home')) {
      if ($(window).outerWidth() < 769) {
        let h = this.height * 1.3;
        let t = h / 100 * 10;
        this.root.css({
          'background-size': `auto ${h}px`,
          'background-position': `center -${t}px`,
        });
        return;
      }
    }

    this.root.css({
      // 'background-image': `url(${this.img})`,
      'background-position': `center top`,
      'background-size': `auto ${(this.height + this.top)}px`,
    })
    // this.root.attr('style', `background-image: url(${this.img}); background-position: center ${this.top}px; background-size: auto ${this.height}px;`);
  }

  this.init = () => {
    return [...document.querySelectorAll('.parallax')].map((elem) => this.createParallax(elem))
  }

  return this.init();
}