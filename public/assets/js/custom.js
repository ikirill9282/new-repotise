$('a.disabled').on('click', (evt) => evt.preventDefault());

const getCSRF = () => $('meta[name="csrf"]').attr('content');


if (window.outerWidth <= 768) {
  $('footer .group > h3').on('click', function() {
    $(this).children().last().toggleClass('!rotate-0 !stroke-white');
    $(this).siblings('ul').slideToggle();
  });
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
    
    const styles = {
      'background-position': `center top`,
      'background-size': `auto ${(this.height + this.top)}px`,
    }

    if (this.root.data('url') && this.root.data('url').length) {
      styles['background-image'] = `url(${this.root.data('url')})`;
    }

    this.root.css(styles);
  }

  this.init = () => {
    return [...document.querySelectorAll('.parallax')].map((elem) => this.createParallax(elem))
  }

  return this.init();
}

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
      // setTimeout(() => {
        if (!input.val().length && !input.is(':focus')) {
          $(input).animate({ 'height': '20px' });
          $(input).data('open', false);
        }
      // }, 500);
    });
  }

  this.init = () => {
    this.writers = [...$('.write_comment')].map((writer) => {
      return this.prepareOjbect(writer);
    });

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
    return this;
  }

  return this.discover(container);
}

const RepliesButtons = function(container) {
  this.group = '.commends_group';
  this.buttons = [];
  this.afterDiscover = null;

  this.addListeners = (button) => {
    const elem = button.item;
    
    elem.on('click', (evt) => {
      evt.preventDefault();
      $.ajax({
        url: '/api/data/comments',
        method: 'POST',
        data: {
          _token: getCSRF(),
          hash: button.hash,
        }
      }).then(response => {
        // elem.closest(this.group).html(response);
        elem.parents(this.group).eq(0).append(response);
        elem.detach();
        
        this.discover('.commend');
        new Editors();
        if (this.afterDiscover !== null) {
          this.afterDiscover();
        }

        window.ReplyButtons.discover();
      });
    });
  }

  this.onAfterDiscover = (callback) => this.afterDiscover = callback;

  this.format = function(button) {    
    const result = {
      hash: button.data('item'),
      item: button,
    }
    return result;
  }

  this.button_exists = (button) => this.buttons.find((btn) => btn.hash === button.data('item')) !== undefined;

  this.discover = (container, callback = null) => {
    $(container).each((key, item) => {
      const buttons = $(item).find('.replies-button');
      
      buttons.each((key, btn) => {
        const button = $(btn);
        if (!this.button_exists(button)) {
          const formatted = this.format(button);
          this.addListeners(formatted, callback);
          this.buttons.push(formatted);
        }
      });
    });
  }

  return this.discover(container);
}

const FavoriteButtons = function() {
  this.discover = (container) => {
    $(container).find('.favorite-button').each((k, btn) => {
      const button = $(btn);
      const hash = button.data('item');
      const key = button.data('key');

      button.on('click', function(evt) {
        evt.preventDefault();
      
        $.ajax({
          url: '/api/feedback/favorite',
          method: 'POST',
          data: {
            _token: getCSRF(),
            hash: hash,
          },
        }).then(response => {
          if (response.status) {
            if (response.value) {
              $(this).addClass('favorite-active');
              $(".favorite-button[data-key='"+ key +"']").addClass('favorite-active');
            } else {
              $(this).removeClass('favorite-active');
              $(".favorite-button[data-key='"+ key +"']").removeClass('favorite-active');
            }
            
            const counters = $('.favorite-counter');
            counters.each((key, counter) => {
              if (response.count > 0 && $(counter).hasClass('hidden')) {
                $(counter).fadeIn();
              }
              if (response.count == 0) {
                $(counter).fadeOut();
                $(counter).addClass('hidden');
              }
              
              $(counter).html(response.count);
              $(window).trigger('favoriteUpdated', {result: response, element: this});
            });
          }
        });
      });
    });
  }

  return {
    discover: this.discover,
  };
}

const CartButtons = function() {
  this.discover = (container) => {
    const buttons = $(container).find('.add-to-cart');
    if (buttons.length) {
      buttons.each((key, btn) => {
        const button = $(btn);
        
        button.on('click', function(evt) {
          evt.preventDefault();
          if (!$(this).hasClass('in-cart')) {
            $.ajax({
              method: 'POST',
              url: '/api/cart/push',
              data: {
                _token: getCSRF(),
                item: $(this).data('value'),
              },
              headers: {
                'X-CSRF-TOKEN': getCSRF(),
              }
            }).then(response => {              
              if (response.status === 'success') {
                $('.cart-counter').html(response.products_count);
                $('.cart-counter').removeClass('hidden');
                $('.cart-counter').attr('style', '');
                $(this).addClass('in-cart');
                $(this).html($(this).html().replace('Add to cart', 'In cart'));
              }
            });
          }
        });
      })
    }
  }

  return {
    discover: this.discover,
  }
}

const CommentForms = function () {
  this.forms = {};

  this.setListeners = (obj) => {
      const form = $(obj);
      const key = form.find('input[name="article"]').val();
      
      if (Object.keys(this.forms).includes(key)) {
        return ;
      }

      form.on("submit", (e) => {
          e.preventDefault();

          const formData = {
              _token: getCSRF(),
              article: form.find('input[name="article"]').val(),
              text: form.find('textarea[name="text"]').val(),
              reply: form.find('input[name="reply"]').val(),
          };
          
          const url = form.data('type') === 'review' ? "/api/feedback/review" : "/api/feedback/comment";
          if (form.data('type') === 'review') {
            formData['rating'] = document.querySelector('input[name="rating"]').value;
          }
          
          $.ajax({
              url: url,
              type: "POST",
              data: formData,
          })
              .then((response) => {
                  if (response.status === "success") {
                      form[0].reset();
                      const input = form.find('textarea');
                      setTimeout(() => {
                        if (!input.val().length && !input.is(':focus')) {
                          $(input).animate({ 'height': '20px' });
                          $(input).data('open', false);
                        }
                      }, 500);
                      // form.find('textarea[name="comment"]').val("");
                      // form.find('.feedback-form__message').text('Comment submitted successfully!');
                  } else {
                      // form.find('.feedback-form__message').text('Error submitting comment.');
                  }
              })
              .catch((error) => {
                  console.error("Error:", error);
                  // form.find('.feedback-form__message').text('An error occurred while submitting the comment.');
              });
      });

      this.forms[key] = form;
  };

  this.discover = () => {
      const feedbackForms = $(".feedback-form");
      feedbackForms.each((index, form) => {
          this.setListeners(form);
      });
  };

  return {
      discover: this.discover,
      forms: this.forms,
  };
};

const ReplyButtons = function() {
  this.buttons = [];

  this.setListeners = (button) => {
    $(button).on('click', function(evt) {
      evt.preventDefault();
      const input = $(this).closest('.about_block').find('.reply-input');
      const textarea = $(this).closest('.about_block').find('textarea');
      const value = $(this).data('reply');
      const text = $(this).closest('.commend').find('.review').clone();
      const reply = $(this).closest('.about_block').find('.reply-block');

      $('html, body').animate({
        scrollTop: $(this).closest('.about_block').offset().top,
      }, 100, 'swing');

      input.val(value);
      reply.find('.reply-text').html(text);

      if (reply.hasClass('hidden')) {
        reply.removeClass('hidden');
      }
    });
  }

  this.discover = () => {
    $('.reply-button').each((key, btn) => {
      this.setListeners(btn);
    });
  }
}

const DropReplyButtons = function() {
  this.buttons = [];

  this.setListeners = (btn) => {
    const button = $(btn);

    if (Object.keys(this.buttons).includes(button.data('key'))) {
      return;
    }
    button.on('click', function(evt) {
      evt.preventDefault();
      const input = $(this).closest('.about_block').find('.reply-input');
      const textarea = $(this).closest('.about_block').find('textarea');
      const reply = $(this).closest('.about_block').find('.reply-block');

      input.val('');
      textarea.val(''); 
      reply.addClass('hidden');
    });
  }

  this.discover = () => {
    $('.drop-reply').each((key, btn) => {
      this.setListeners(btn);
    });
  }
  return {
    discover: this.discover,
  }
}

const header = $('header');
const headerHeight = header.outerHeight();
// const start = (document.querySelector('.parallax')) ? $('.parallax').outerHeight() : headerHeight;
window.FavoriteButtons = new FavoriteButtons();
window.CartButtons = new CartButtons();
window.CommentForms = new CommentForms();
window.ReplyButtons = new ReplyButtons()
window.DropReplyButtons = new DropReplyButtons();

window.FavoriteButtons.discover('body');
window.CartButtons.discover('body');
window.CommentForms.discover();
window.ReplyButtons.discover();
window.DropReplyButtons.discover();

let lastPoint = 0;


function setCosts(data)
{
  const blocks = $('.costs');
  blocks.each((k, el) => {
    const cartSubtotal = $(el).find('.cart-subtotal');
    const cartDiscount = $(el).find('.cart-discount');
    const cartTax = $(el).find('.cart-tax');
    const cartTotal = $(el).find('.cart-total');

    cartSubtotal.html(data?.subtotal)
    cartDiscount.html(data?.discount)
    cartTax.html(data?.tax)
    cartTotal.html(data?.total)

    if (data?.discount > 0) {
      cartDiscount.closest('h4').addClass('!text-emerald-500');
    } else {
      cartDiscount.closest('h4').removeClass('!text-emerald-500');
    }
  })
}


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

$('.search-button').on('click', function(evt) {
  evt.preventDefault();
  $('.search-form').submit();
});


$('[data-input="integer"]').on('input', function(evt) {
  $(this).val(evt.target.value.replace(/[^0-9]+/is, ''));
});

$('[data-input="phone"]').on('input', function(evt) {
  $(this).val(evt.target.value.replace(/[^0-9\+\(\)_\s\-]+/is, ''));
});



const dropButtons = [];

function discoverCartDropButtons(container=null)
{
  const base = (container == null) ? 'body' : container;

  $(base).find('.cart-drop').each((k, btn) => {
    const hash = $(btn).data('item');
    if (!dropButtons.includes(hash)) {
      dropButtons.push(hash);
      $(btn).on('click', function(evt) {
        evt.preventDefault();
        $.ajax({
          method: 'POST',
          url: '/api/cart/remove',
          data: {
            _token: getCSRF(),
            item: $(this).data('item'),
          }
        }).then(response => {
          if (response.status === 'success') {
            $(this).closest('.item').animate({
              opacity: 0,
            });
            $(this).closest('.item').animate({
              height: 'toggle',
            });

            if (response.count == 0) {
              $('.cart-counter').fadeOut(function() {
                $(this).addClass('hidden');
              });
              $(this).closest('.order-view').fadeOut(() => $('.empty-container').fadeIn());
              
            } else {
              $('.cart-counter').html(response.count);
              $('.cart-counter').removeClass('hidden');
            }

            const key = $(this).data('key');
            console.log(key,$(`a[data-key="${key}"]`));
            $(`a[data-key="${key}"]`).each((k, a) => {
              $(a).removeClass('in-cart');
              $(a).html('Add to cart');
            })

            setCosts(response.costs);
          }
        })
      });
    }
  });
}

$(document).ready(function() {
  discoverCartDropButtons();
  Livewire.hook('morphed',  ({ el, component }) => {
    discoverCartDropButtons(el);
  })
});