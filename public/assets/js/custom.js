$.fn.isInViewport = function() {
  let elementTop = $(this).offset().top;
  let elementBottom = elementTop + $(this).outerHeight();

  let viewportTop = $(window).scrollTop();
  let viewportBottom = viewportTop + $(window).height();

  return elementBottom > viewportTop && elementTop < viewportBottom;
};


$('a.disabled').on('click', (evt) => evt.preventDefault());

const getCSRF = () => $('meta[name="csrf"]').attr('content');

function copyTextToClipboard(text) {
  if (!text) return;

  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(text).then(() => {
      $.toast({
        text: 'Copied!',
        icon: 'success',
        heading: 'Success',
        position: 'top-right',
      });
    }).catch(err => {
      fallbackCopyTextToClipboard(text);
    });
  } else {
    fallbackCopyTextToClipboard(text);
  }
}

function fallbackCopyTextToClipboard(text) {
  const textArea = document.createElement("textarea");
  textArea.value = text;
  textArea.style.position = "fixed";
  textArea.style.top = "-9999px";
  textArea.style.left = "-9999px";

  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    const successful = document.execCommand('copy');
    if (successful) {
      $.toast({
        text: 'Copied!',
        icon: 'success',
        heading: 'Success',
        position: 'top-right',
      });
    } else {
      $.toast({
        text: 'Something went wront...',
        icon: 'error',
        heading: 'error',
        position: 'top-right',
      });
    }
  } catch (err) {
    $.toast({
      text: 'Something went wront...',
      icon: 'error',
      heading: 'error',
      position: 'top-right',
    });
  }
  document.body.removeChild(textArea);
}

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

const EmojiButtons = function() {
  this.buttons = [];
  this.pickers = [];

  this.setListeners = (elem) => {
    const item = $(elem);
    const target = $(elem).data('target');
    const textarea = $(`#${target}`);

    const picker = textarea.emojiPicker({
        width: ($(window).outerWidth() > 576) ? '300px' : '200px',
        height: ($(window).outerWidth() > 576) ? '200px' : '100px',
        button: false,
        recentCount: 10,
        container: item,
    });

    item.on('click', () => picker.emojiPicker('toggle'));
    textarea.on('focusout', () => picker.emojiPicker('close'));
    this.pickers.push(picker);
  }

  this.discover = () => {
    $('.emoji-btn').each((k, elem) => {
      if (!this.buttons.includes(elem)) {
        this.setListeners(elem);
        this.buttons.push(elem);
      }
    });
  }
}

const Editors = function() {
  this.editors = [];

  this.discover = () => {
    [...$('.editor_btn')].map((editor) => {
      if (!this.editors.includes(editor)) {
        $(editor).on('click', function(event) {
          event.preventDefault();
          if ($(this).hasClass('open_auth')) {
            return ;
          }

          const target = $(this).data('target');
          const list = $(`#${target}`);

          if (target) {
            list.toggleClass('opened');
            if (list.hasClass('opened')) {
              const height = list.find('.editor-buttons').outerHeight();
              list.css({ height: height + 'px' });
            } else {
              list.attr('style', '');
            }
          }
        });

        this.editors.push(editor);
      }
    });
  }
}

const EditorButtons = function() {
  this.buttons = [];

  this.setListeners = (elem) => {
    
    $(elem).on('click', function(evt) {
      evt.preventDefault();
      const action = $(this).data('action');
      const hash = $(elem).closest('.editor-wrap').data('model');
      const resource = $(elem).closest('.editor-wrap').data('resource');
      
      if (action === 'report') {
        Livewire.dispatch('openModal', { modalName: 'report', args: { model: hash, resource: resource } });
      }

      if (action === 'edit') {
        const textarea = $(elem).closest('.chat').find('textarea');
        const replyInput = $(elem).closest('.chat').find('.reply-input');
        const replyBlock = $(elem).closest('.chat').find('.reply-block');
        const textBlock = $(elem).closest('.content').find('.message-text');
        const text = textBlock.find('.read-more-text') ? textBlock.find('.read-more-text').text() : textBlock.text();
        
        const event = new Event('input', {
          bubbles: true,
          cancelable: true,
        });

        if (replyInput.val().length) {
          replyBlock.find('.drop-reply').click();
          replyInput.val(null);
          textarea.val(null);
          textarea.empty(); 
        }
        
        textarea.val(text);
        textarea.eq(0).get(0).dispatchEvent(event);

        $(elem).closest('.chat').find('input[name="edit"]').val(hash);
        
        if (!textarea.isInViewport()) {
          $('html, body').animate({
            scrollTop: $(elem).closest('.chat').offset().top,
          }, 100, 'swing');
        }
      }

      const eventClick = new Event('click', {
        bubbles: true,
        cancelable: true,
      });
      $(elem).closest('.settings').find('.editor_btn').eq(0).get(0).dispatchEvent(eventClick);
    });
  }

  this.discover = () => {
    $('.editor-buttons').each((k, elem) => {
      if (!this.buttons.includes(elem)) {
        $(elem).find('a').each((k, el) => {
          this.setListeners(el);
        });

        this.buttons.push(elem);
      }
    });
  }
}

const LikeButtons = function() {
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

  this.discover = () => {
    const buttons = $(document).find('.feedback_button');
    
    buttons.each((key, element) => {
      if (!this.buttons.includes(element)) {
        const button = this.prepareElement(element);
        
        if (button !== null) {
          this.setListeners(button);
          this.buttons.push(element);
        }
      }
    });

    return this;
  }
}

const RepliesButtons = function() {
  this.group = '.show-more';
  this.buttons = [];
  this.afterDiscover = null;

  this.addListeners = (button) => {
    const elem = $(button);
    
    elem.on('click', (evt) => {
      evt.preventDefault();
      $.ajax({
        url: '/api/data/messages',
        method: 'POST',
        data: {
          _token: getCSRF(),
          resource: elem.data('resource'),
        }
      }).then(response => {
        elem.parents(this.group).eq(0).replaceWith(response);
        this.discover();

        new Editors();
        if (this.afterDiscover !== null) {
          this.afterDiscover();
        }

        window.ReplyButtons.discover();
        window.ReadMoreButtons.discover();
        window.LikeButtons.discover();
        window.Editors.discover();
        window.EditorButtons.discover();
        window.AuthButtons.discover();
      });
    });
  }

  this.onAfterDiscover = (callback) => this.afterDiscover = callback;

  this.button_exists = (button) => this.buttons.find((btn) => btn.hash === button.data('item')) !== undefined;

  this.discover = (container, callback = null) => {
    $(document).each((key, item) => {
      const buttons = $(item).find('.replies-button');
      
      buttons.each((key, btn) => {
        const button = $(btn);
        if (!this.buttons.includes(btn)) {
          this.addListeners(btn, callback);
          this.buttons.push(btn);
        }
      });
    });
  }
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
          if ($(this).hasClass('in-cart')) {
            return;
          }

          evt.preventDefault();
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
                $(this).attr('href', '/payment/checkout');
                $(this).html($(this).html().replace('Add to cart', 'View Cart'));
								if (window.Livewire?.dispatch) {
									Livewire.dispatch('openModal', { modalName: 'cart' });
								}
								
              }
            });
        });
      })
    }
  }

  return {
    discover: this.discover,
  }
}

$(document).on('click', '.add-to-cart.in-cart', function (evt) {
  evt.preventDefault();
  if (window.Livewire?.dispatch) {
    Livewire.dispatch('openModal', { modalName: 'cart' });
  }
});


const ReviewForms = function () {
  this.forms = {};

  this.setListeners = (obj) => {
      const form = $(obj);
      const key = form.find('input[name="model"]').val();
      
      if (Object.keys(this.forms).includes(key)) {
        return ;
      };
      
      form.on("submit", (e) => {
          e.preventDefault();

          const formData = {
              _token: getCSRF(),
              model: form.find('input[name="model"]').val(),
              text: form.find('textarea[name="text"]').val(),
              reply: form.find('input[name="reply"]').val(),
              edit: form.find('input[name="edit"]').val(),
              rating: document.querySelector('input[name="rating"]')?.value,
          };
          
          const url = form.data('type') === 'review' ? "/api/feedback/review" : "/api/feedback/comment";
          
          $.ajax({
              url: url,
              type: "POST",
              data: formData,
          })
          .then((response) => {
              if (response.status === "success") {
                
                if (form.data('type') == 'review') {
                  form.detach();
                } else {
                  form.eq(0).get(0).reset();
                }
                
                const name = formData.reply ? 'reply' : (form.data('type') == 'review' ? 'review' : 'comment');
                $.toast({
                  text: `Your ${name} has been received and is now awaiting moderation.`,
                  icon: 'success',
                  heading: 'Success',
                  position: 'top-right',
                })
                  // form[0].reset();
                  // form.find('input[name="rating"]').val(null).trigger('change');
                  // form.find('.stars').find('span').removeClass('active');

                  // const input = form.find('textarea');
                  // // setTimeout(() => {
                  // //   if (!input.val().length && !input.is(':focus')) {
                  // //     $(input).animate({ 'height': '20px' });
                  // //     $(input).data('open', false);
                  // //   }
                  // // }, 500);
                  // // form.find('textarea[name="comment"]').val("");
                  // // form.find('.feedback-form__message').text('Comment submitted successfully!');
              } else {
                  // form.find('.feedback-form__message').text('Error submitting comment.');
              }
          })
          .catch((error) => {
              const errors = error.responseJSON?.errors;
              for (const key in errors) {
                const element = document.getElementById(`${key}-error`);
                console.log(`${key}-error`);
                
                if (element) {
                  element.classList.remove('hidden');
                  element.innerHTML = errors[key].join("\n");
                }
              }
              
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
  this.wrap = '.chat';
  this.group = '.message';
  this.buttons = [];

  this.setListeners = (button) => {
    // if (!this.buttons.includes(button)) {
      $(button).on('click', (evt) => {
        
        evt.preventDefault();
        const input = $(button).closest(this.wrap).find('.reply-input');
        const inputEdit = $(button).closest(this.wrap).find('.edit-input');
        const textarea = $(button).closest(this.wrap).find('textarea');
        const value = $(button).data('reply');
        const reply = $(button).closest(this.wrap).find('.reply-block');
        const text = $(button).closest('.content').find('.message-text').clone();

        input.val(value);
        reply.find('.reply-text').html(text);

        if (reply.hasClass('hidden')) {
          reply.removeClass('hidden');
        }

        if (!reply.isInViewport()) {
          $('html, body').animate({
            scrollTop: $(button).closest(this.wrap).offset().top,
          }, 100, 'swing');
        }
        if (inputEdit.val()) {
          inputEdit.val(0);
          textarea.val(null);
          textarea.empty();
        }
        window.DropReplyButtons.discover();
      });
      this.buttons.push(button);
    // }
  }

  this.discover = () => {
    $('.reply-button').each((key, btn) => {
      if (!this.buttons.includes(btn));
      this.setListeners(btn);
    });
  }
}

const DropReplyButtons = function() {
  this.wrap = '.chat';
  this.group = '.message';
  this.buttons = [];

  this.setListeners = (btn) => {
    const button = $(btn);

    if (Object.keys(this.buttons).includes(button.data('key'))) {
      return;
    }
    button.on('click', (evt) => {
      evt.preventDefault();
      const input = $(button).closest(this.wrap).find('.reply-input');
      const textarea = $(button).closest(this.wrap).find('textarea');
      const reply = $(button).closest(this.wrap).find('.reply-block');

      input.val('');
      textarea.val(''); 
      reply.addClass('hidden');
    });

    this.buttons.push(btn);
  }

  this.discover = () => {
    $('.drop-reply').each((key, btn) => {
      if (!this.buttons.includes(btn)) {
        this.setListeners(btn);
      }
    });
  }
}

const FollowButtons = function() {
  this.buttons = [];

  this.discover = () => {
    [...document.querySelectorAll('.follow-btn')].forEach((button, key) => {
      
      if (!this.buttons.includes(button)) {
        button.addEventListener('click', function(evt) {
          evt.preventDefault();
          const hash = this.getAttribute('data-resource');
          const group = this.getAttribute('data-group');

          $.ajax({
            method: 'POST',
            url: '/api/feedback/follow',
            data: {
              _token: getCSRF(),
              resource: hash,
              type: 'article',
            }
          }).then(response => {
            const btns = document.querySelectorAll(`[data-group="${group}"]`)
            if (btns.length) {
              btns.forEach((btn, k) => {
                if (response.sub) {
                  btn.innerHTML = 'Unsubscribe';
                } else {
                  btn.innerHTML = 'Subscribe';
                }
              });

              if (response.sub) {
                $.toast({
                  text: 'You’re now following and won’t miss any updates.',
                  icon: 'success',
                  heading: 'Success',
                  position: 'top-right',
                });
              } else {
                $.toast({
                  text: 'You have unfollowed the user\'s updates.',
                  icon: 'success',
                  heading: 'Success',
                  position: 'top-right',
                });
              }
            }
          });
        });
        this.buttons.push(button);
      }
    });
  }
}

const ReadMoreButtons = function() {
  this.buttons = [];

  this.discover = () => {
    [...document.querySelectorAll('.read-more')].forEach((elem, k) => {
      if (!elem.getAttribute('data-inited')) {
        const text = $('<div>', { class: "read-more-text inline-block", html: $(elem).html() });
        const btnWrap = $('<div>', { 
          class: "read-more-wrap",
          style: `box-shadow: 0px 0px 30px 30px ${$(elem).data('color') ?? '#fff'}; background-color: ${$(elem).data('color') ?? '#fff'};`
        });
        const btn = $('<span>', { href: "#", class: 'read-more-btn', text: $(elem).data('text') ?? 'Read More' });
        
        btn.on('click', function() {
          $(elem).toggleClass('read-more-open');

          if (!$(elem).hasClass('read-more-open')) {
            const str = [...elem.classList].find(elem => /read-more-\d+/is.test(elem));
            if (str) {
              const height = str.replace(/read-more-(\d+)/is, "$1");
              $(elem).css({ height: `${height}px` });
            } else {
              const height = 150;
              $(elem).css({ height: `${height}px` });
            }
            btn.text('Learn More');
            // btnWrap.show();
          } else {
            const height = text.outerHeight() + btn.outerHeight();
            $(elem).css({ height: `${height}px` });
            btnWrap.hide();
          }
        });

        $(elem).empty();
        $(elem).append(text);

        if (text.outerHeight() <= 150) {
          $(elem).css({height: 'auto'});
          return ;
        };

        btnWrap.append(btn);
        $(elem).append(btnWrap);
        this.buttons.push(elem);
        elem.setAttribute('data-inited', 'true');
      }
    });
  }
}

const AuthButtons = function() {
  this.buttons = [];

  this.discover = () => {
    $('.open_auth').each((k, elem) => {
      if (!this.buttons.includes(elem)) {
        $(elem).on('click', function(evt) {
          evt.preventDefault();
          Livewire.dispatch("openModal", { modalName: "auth" });
        });
      }
    });
  }
}

const CopyToClipboard = function() {
  this.buttons = [];
  
  this.discover = () => {
    
    $('.copyToClipboard').each((k, el) => {
      
      if (!this.buttons.includes(el)) {
        $(el).on('click', function(evt) {
          
          evt.preventDefault();
          const target = $(`[data-copyId='${$(this).data('target')}']`);
          
          if (target) {
            copyTextToClipboard(target.val() || target.text())
          }
        });
        this.buttons.push(el);
      }
    });
  }
}

const CartCounter = function() {
  this.buttons = [];

  this.discover = () => {
    document.querySelectorAll(".counter").forEach((counter) => {
        if (!this.buttons.includes(counter)) {
          const minusBtn = counter.querySelector(".minus");
          const plusBtn = counter.querySelector(".plus");
          const countEl = counter.querySelector(".count");

          let count = parseInt(countEl.textContent);

          plusBtn.addEventListener("click", function () {
              count++;
              countEl.textContent = count;
              counterChanged(this.closest(".counter"), count);
          });

          minusBtn.addEventListener("click", function () {
              if (count > 1) {
                  count--;
                  countEl.textContent = count;
                  counterChanged(this.closest(".counter"), count);
              }
          });

          this.buttons.push(counter);
        }
    });
  }
}

const header = $('header');
const headerHeight = header.outerHeight();

window.FavoriteButtons = new FavoriteButtons();
window.CartButtons = new CartButtons();
window.ReviewForms = new ReviewForms();
window.ReplyButtons = new ReplyButtons()
window.DropReplyButtons = new DropReplyButtons();
window.FollowButtons = new FollowButtons();
window.RepliesButtons = new RepliesButtons();
window.ReadMoreButtons = new ReadMoreButtons();
window.LikeButtons = new LikeButtons();
window.Editors = new Editors();
window.EditorButtons = new EditorButtons();
window.AuthButtons = new AuthButtons();
window.EmojiButtons = new EmojiButtons();
window.CopyToClipboard = new CopyToClipboard();
window.CartCounter = new CartCounter();

$(document).ready(function() {
  window.FavoriteButtons.discover('body');
  window.CartButtons.discover('body');
  window.ReviewForms.discover();
  window.ReplyButtons.discover();
  window.DropReplyButtons.discover();
  window.FollowButtons.discover();
  window.RepliesButtons.discover();
  window.ReadMoreButtons.discover();
  window.LikeButtons.discover();
  window.Editors.discover();
  window.EditorButtons.discover();
  window.AuthButtons.discover();
  window.CopyToClipboard.discover();
  window.CartCounter.discover();
  
  
  setTimeout(() => window.EmojiButtons.discover(), 100);
});


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
  const form = $(this).closest('.search-form');
  if (form.length) {
    form.trigger('submit');
  }
});

$('[data-input="integer"]').on('input', function(evt) {
  $(this).val(evt.target.value.replace(/[^0-9]+/is, ''));
});

$('[data-input="percent"]').on('input', function(evt) {
  $(this).val(evt.target.value.replace(/[^0-9\.]+/is, '') + '%');
});

$('[data-input="price"]').on('input', function(evt) {
  $(this).val('$' + evt.target.value.replace(/[^0-9.]/g, ''));
});

$('[data-input="phone"]').on('input', function(evt) {
  $(this).val(evt.target.value.replace(/[^0-9\+\(\)_\s\-]+/is, ''));
});



const dropButtons = [];

function discoverCartDropButtons(container=null) {
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
              $(this).closest('.order-view').fadeOut(() => {
                
                $('.empty-container').css({opacity: 0});
                $('.empty-container').removeClass('hidden');
                $('.empty-container').animate({opacity: 1});

              });
              
            } else {
              $('.cart-counter').html(response.count);
              $('.cart-counter').removeClass('hidden');
            }

            const key = $(this).data('key');

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

    window.LikeButtons.discover();
    window.CartCounter.discover();
    window.CopyToClipboard.discover();
    window.EmojiButtons.discover();
    window.ReadMoreButtons.discover();

    $('[data-input="percent"]').on('input', function(evt) {
      $(this).val(evt.target.value.replace(/[^0-9\.]+/is, '') + '%');
    });

    $('[data-input="integer"]').on('input', function(evt) {
      $(this).val(evt.target.value.replace(/[^0-9]+/is, ''));
    });

    $('[data-input="price"]').on('input', function(evt) {
      $(this).val('$' + evt.target.value.replace(/[^0-9.]/g, ''));
    });

  });

  [...document.querySelectorAll('.stars_filter')].map(stars => {
    $(stars)
      .find('span')
      .each((key, elem) => {
          $(elem).off("click");
          $(elem).on("click", function() {
              $(this).addClass("active");
              const key = +$(this).data("value");
              $(this)
                  .siblings()
                  .each((k, sibling) => {
                      if (+$(sibling).data("value") <= key) {
                          $(sibling).addClass("active");
                      } else {
                          $(sibling).removeClass("active");
                      }
                  });
              $(stars).find('input[name="rating"]').val(key);
              $(stars).find('input[name="rating"]').trigger('change');
              $('#rating-error')?.addClass('hidden');
          });
          $(elem).mouseenter(function() {
              const key = +$(this).data("value");
              $(this)
                  .siblings()
                  .each((k, sibling) => {
                      if (+$(sibling).data("value") <= key) {
                          $(sibling).addClass("hover");
                      }
                  });
          });
          $(elem).mouseleave(function() {
              $(this).closest(".stars").find("span").removeClass("hover");
          });
      });
  });

  const inputs = [...document.querySelectorAll('input'), ...document.querySelectorAll('textarea')];
  inputs.forEach((input, key) => {
    function hideError() {
      try {
      const name = this.getAttribute('name')
      const errorElement = document?.querySelector(`#${name}-error`);
      if (errorElement) errorElement.classList.add('hidden');
      
      } catch (error) {}
    }

    input.addEventListener('input', hideError);
    input.addEventListener('change', hideError);
  });


  Livewire.on('toastSuccess', params => {
    const message = params[0]?.message;
    $.toast({
      text: message,
      icon: 'success',
      heading: 'Success',
      position: 'top-right',
      hideAfter: 5000,
    });
  });

  Livewire.on('toastError', params => {
    const message = params[0]?.message;
    $.toast({
      text: message,
      icon: 'error',
      heading: 'Error',
      position: 'top-right',
      hideAfter: 5000,
    });
  });

  Livewire.on('setCartCounter', params => {
    const count = params[0]?.count;
    console.log(count);
    
    if (count !== undefined) {
      if (count > 0) {
        $('.cart-counter').html(count);
        $('.cart-counter').removeClass('hidden');
        $('.cart-counter').attr('style', '');
      } else {
        $('.cart-counter').fadeOut(function() {
          $(this).addClass('hidden');
        });
      }
    }
  });
});
