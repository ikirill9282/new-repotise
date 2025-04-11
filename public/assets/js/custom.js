$('a.disabled').on('click', (evt) => evt.preventDefault());

const getCSRF = () => $('meta[name="csrf"]').attr('content');


$('footer .group > h3').on('click', function() {
  $(this).children().last().toggleClass('!rotate-0 !stroke-white');
  $(this).siblings('ul').slideToggle();
});

const CommentWriters = function() {
  this.writers = [];
  
  this.prepareOjbect = (object) => {
    const obj = $(object);
    const input = obj.find('textarea');
    const container = input.data('emojibtn');
    const btn = obj.find(container);

    if (input) {
      this.setInputListeners(input);
      input.emojiPicker({
        width: ($(window).outerWidth() > 576) ? '300px' : '200px',
        height: ($(window).outerWidth() > 576) ? '200px' : '100px',
        button: false,
        recentCount: 10,
        container: container,
      });
    }

    if (btn) {
      btn.off('click');
      btn.on('click', (event) => {
        event.preventDefault();
        input.emojiPicker('toggle');
      })
    }

    return obj;
  }

  this.setInputListeners = (input) => {
    input.on('input', function(event) {
      let length = $(this).val().length;
      
      if (length > 1000) {
        $(this).val($(this).val().slice(0, 1000));
        return;
      }

      $(this).data('length', length);
      input.prevObject.find('a.numbers').text(`${length}/1000`);
    });

    input.on('focus', () => {
      $(input).animate({ 'height': '240px' });
      $(input).data('open', true);
    });

    input.on('focusout', (evt) => {
      setTimeout(() => {
        if (!input.val().length && !input.is(':focus')) {
          $(input).animate({ 'height': '20px' });
          $(input).data('open', false);
        }
      }, 500);
    });
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

const LikeButtons = function(container) {
  this.buttons = [];

  this.setListeners = (item) => {
    item.elem.on('click', function(evt) {
      evt.preventDefault();

      if ($(this).hasClass('open_auth')) {
        return;
      }

      if (item.elem.requested) {
        return;
      }

      item.elem.requested = true;

      let path = $(this).attr('href') || $(this).data('path');
      if (path !== undefined && path !== null && path.length) {
        path = (path[0] === '/') ? path : `/${path}`
        
        $.ajax({
          url: `/api${path}`,
          method: 'POST',
          data: {
            _token: getCSRF(),
            item: item.hash, 
          }
        }).then(response => {
          if (Object.keys(item).includes('counter')) {
            item.counter.each((k, counter) => {
              const content = Number($(counter).text());
              const new_content = response.status ? (content + 1) : (content - 1)
              $(counter).text(new_content);
              
              if (response.status) {
                $(this).addClass('liked');
              } else {
                $(this).removeClass('liked')
              }
            });
            item.elem.requested = false;
          }
        });
      }
    });
  }

  this.prepareElement = (element) => {
    const result = {
      elem: $(element),
      id: $(element).data('id'),
    }
    
    if (result.id !== undefined && result.id !== null && result.id.length > 0) {
      result.hash = $(element).data('item');

      if (
          result.hash === undefined || 
          result.hash === null || 
          typeof result.hash !== 'string' || 
          result.hash.length === 0
        ) {
        return null;
      }

      const counter = $(`*[data-counter="${result.id}"]`);
      if (counter.length) { 
        result.counter = counter;
      }
      
      return result;
    }

    return null;
  }

  this.discover = (container) => {
    $(container).each((key, item) => {
      const buttons = $(item).find('a.feedback_button');
      buttons.each((key, element) => {
        const button = this.prepareElement(element);
        if (this.buttons.find(btn => btn.id == button.id) !== undefined) {
          return;
        }

        if (button !== null) {
          this.setListeners(button);
          this.buttons.push(button);
        }
        
      })
    });

    console.log(this.buttons);
    

    return this;
  }

  return this.discover(container);
}

const header = $('header');
const headerHeight = header.outerHeight();
// const start = (document.querySelector('.parallax')) ? $('.parallax').outerHeight() : headerHeight;

let lastPoint = 0;

$(window).on('scroll', function(evt) {
    const point = $(this).scrollTop();
    if (point > (headerHeight + 10)) {
        if (!header.hasClass('!sticky')) {
            header.addClass('!sticky top-0 left-0 translate-y-[-100%] shadow-md');
        }
        if (point <= lastPoint) {
            header.addClass('transition !translate-y-0');
        } else if (!$('#mobile_menu').data('open')) {
            header.removeClass('!translate-y-0');
        }
    }

    if (point == 0) {
        header.removeClass('!sticky top-0 left-0 translate-y-[-100%] shadow-md');
        header.removeClass('transition');
    }

    lastPoint = point;
});

$('.hamburger-menu').on('click', function(evt) {
    const menu = $('#mobile_menu');
    const button = $(this).find('.menu__btn');

    menu.removeClass('translate-x-full');
    menu.data('open', true);
    $(button).toggleClass('menu_open');
    // $('#close_menu').toggleClass('menu_open');

    if (menu.data('open')) {
        $('body').addClass('overflow-hidden');
    } else {
        $('body').removeClass('overflow-hidden');
    }
});

$('#close_menu').on('click', function(evt) {
    const menu = $('#mobile_menu');
    const button = $('.hamburger-menu').find('.menu__btn');
    menu.data('open', false);

    menu.addClass('translate-x-full');
    $(button).toggleClass('menu_open');
    // $('#close_menu').toggleClass('menu_open');

    if (menu.data('open')) {
        $('body').addClass('overflow-hidden');
    } else {
        $('body').removeClass('overflow-hidden');
    }
});