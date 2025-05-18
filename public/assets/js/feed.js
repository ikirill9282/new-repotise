document.addEventListener('DOMContentLoaded', function() {
  history.scrollRestoration = 'manual';
});

$.fn.isInViewport = function() {
  let elementTop = $(this).offset().top;
  let elementBottom = elementTop + $(this).outerHeight();

  let viewportTop = $(window).scrollTop();
  let viewportBottom = viewportTop + $(window).height();

  return elementBottom > viewportTop && elementTop < viewportBottom;
};

const InitSliders = function() {
  const items = document.querySelectorAll('div[id*="analogs-swiper-"]');

  const sliders = [...items].map((elem) => {
      const selector = elem.getAttribute('id');
      return new Swiper(`#${selector}`, {
          slidesPerView: 4,
          spaceBetween: 20,
          navigation: {
              nextEl: `#${selector} .swiper-button-next`,
              prevEl: `#${selector} .swiper-button-prev`,
          },
          breakpoints: {
              320: {
                  slidesPerView: 1.1,
                  spaceBetween: 10,
              },
              400: {
                  slidesPerView: 1.3,
                  spaceBetween: 10,
              },
              500: {
                  slidesPerView: 1.6,
                  spaceBetween: 10,
              },
              600: {
                  slidesPerView: 1.9,
                  spaceBetween: 10,
              },
              700: {
                  slidesPerView: 2.2,
                  spaceBetween: 10,
              },
              768: {
                  slidesPerView: 2.2,
                  spaceBetween: 15,
              },
              1024: {
                  slidesPerView: 3,
                  spaceBetween: 20,
              },
              1200: {
                  slidesPerView: 4,
                  spaceBetween: 20,
              },
          },
      });
  });

  if ($(window).outerWidth() < 768) {
      return new Swiper('#last_news_swiper', {
          slidesPerView: 1.2,
          spaceBetween: 20,
          enabled: true,
          breakpoints: {
              370: {
                  slidesPerView: 1.4
              },
              400: {
                  slidesPerView: 1.6,
              },
              500: {
                  slidesPerView: 1.9,
              },
              768: {
                  enabled: false,
                  slidesPerView: 4,
              },
              1200: {
                  slidesPerView: 5,
              },
          }
      });
  }

  return sliders;
}

$(document).ready(function() {
  let sli = InitSliders();
  let writers = new CommentWriters();
  let editors = new Editors();
  const likers = new LikeButtons('.feed-item');
  const repliers = new RepliesButtons('.commend');
  
  repliers.onAfterDiscover(() => likers.discover('.feed-item'));
  $(window).scroll((event) => {
    $('.stopper').each(function(i, el) {
        if ($(this).isInViewport()) {
            const params = new URLSearchParams(document.location.search);
            const clone = $(this).clone();
            const feed = $('#feed');
            const last_child = feed.find('.feed-item').last();
            const id = last_child.data('content');
            
            $(this).detach();
            
            if (id !== undefined) {
                $.ajax({
                    method: 'GET',
                    url: `/api/data/feed/${id}?aid=${params.get('aid') ?? 0}`,
                }).then(response => {
                    feed.append(clone);
                    feed.append(response);

                    sli = InitSliders();
                    writers = new CommentWriters();
                    editors = new Editors();
                    
                    likers.discover('.feed-item');
                    repliers.discover('.commend');
                    initAuth();
                    window.CommentForms.discover();
                    window.ReplyButtons.discover();
                    window.DropReplyButtons.discover();
                });
            }
        }
    })
  });
});


window.addEventListener('refresh-page', event => {
  window.location.reload(false);
  //  scrollTo({top: 0, behavior: 'instant'});
})