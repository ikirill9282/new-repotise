document.addEventListener('DOMContentLoaded', function() {
  history.scrollRestoration = 'manual';
});

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

// Throttle функция для оптимизации скролла
function throttle(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Создание индикатора загрузки
function createLoadingSpinner() {
  const spinner = $('<div class="feed-loading-spinner" style="text-align:center;padding:40px 20px;color:#FC7361;font-size:16px;">Loading more articles...</div>');
  return spinner;
}

// Создание сообщения об окончании статей
function createEndMessage() {
  const message = $('<div class="feed-end-message" style="text-align:center;padding:40px 20px;color:#A4A0A0;font-size:16px;">No more articles to load</div>');
  return message;
}

// Удаление индикатора загрузки
function removeLoadingSpinner() {
  $('.feed-loading-spinner').remove();
}

$(document).ready(function() {
  InitSliders();
  
  let isLoading = false;
  let hasMore = true;
  
  // Throttled scroll handler
  const handleScroll = throttle(function() {
    if (isLoading || !hasMore) return;
    
    $('.stopper').each(function(i, el) {
        if ($(this).isInViewport()) {
            const params = new URLSearchParams(document.location.search);
            const clone = $(this).clone();
            const feed = $('#feed');
            const last_child = feed.find('.feed-item').last();
            const id = last_child.data('content');
            
            $(this).detach();
            
            if (id !== undefined && !isLoading) {
                isLoading = true;
                const spinner = createLoadingSpinner();
                feed.append(spinner);
                
                // Собираем все ID уже загруженных статей
                const loadedIds = [];
                feed.find('.feed-item').each(function() {
                    const itemId = $(this).data('content');
                    if (itemId) {
                        loadedIds.push(itemId);
                    }
                });
                
                // Формируем URL с параметром exclude
                let url = `/api/data/feed/${id}?aid=${params.get('aid') ?? 0}`;
                if (loadedIds.length > 0) {
                    url += `&exclude=${loadedIds.join(',')}`;
                }
                
                $.ajax({
                    method: 'GET',
                    url: url,
                    timeout: 30000, // 30 секунд таймаут
                })
                .done(function(response) {
                    removeLoadingSpinner();
                    
                    if (!response || response.trim() === '') {
                        hasMore = false;
                        feed.append(createEndMessage());
                        return;
                    }
                    
                    feed.append(clone);
                    feed.append(response);

                    // Инициализация только для новых элементов
                    const newItems = feed.find('.feed-item').slice(-3);
                    newItems.each(function() {
                      const $item = $(this);
                      
                      // Инициализация слайдеров только для новых элементов
                      $item.find('div[id*="analogs-swiper-"]').each(function() {
                        const selector = $(this).attr('id');
                        if (!$(this).hasClass('swiper-initialized')) {
                          new Swiper(`#${selector}`, {
                            slidesPerView: 4,
                            spaceBetween: 20,
                            navigation: {
                              nextEl: `#${selector} .swiper-button-next`,
                              prevEl: `#${selector} .swiper-button-prev`,
                            },
                            breakpoints: {
                              320: { slidesPerView: 1.1, spaceBetween: 10 },
                              400: { slidesPerView: 1.3, spaceBetween: 10 },
                              500: { slidesPerView: 1.6, spaceBetween: 10 },
                              600: { slidesPerView: 1.9, spaceBetween: 10 },
                              700: { slidesPerView: 2.2, spaceBetween: 10 },
                              768: { slidesPerView: 2.2, spaceBetween: 15 },
                              1024: { slidesPerView: 3, spaceBetween: 20 },
                              1200: { slidesPerView: 4, spaceBetween: 20 },
                            },
                          });
                        }
                      });
                    });
                    
                    // Инициализация компонентов только для новых элементов
                    initModal();
                    window.ReviewForms?.discover();
                    window.ReplyButtons?.discover();
                    window.DropReplyButtons?.discover();
                    window.FollowButtons?.discover();
                    window.LikeButtons?.discover();
                    window.Editors?.discover();
                    window.EditorButtons?.discover();
                    window.RepliesButtons?.discover();
                    window.EmojiButtons?.discover();
                    window.ReadMoreButtons?.discover();
                    window.CopyToClipboard?.discover();
                    
                    isLoading = false;
                })
                .fail(function(xhr, status, error) {
                    removeLoadingSpinner();
                    console.error('Error loading articles:', error);
                    
                    // Показываем сообщение об ошибке
                    const errorMsg = $('<div class="feed-error-message" style="text-align:center;padding:40px 20px;color:#dc2626;font-size:16px;">Failed to load articles. Please try again.</div>');
                    feed.append(errorMsg);
                    
                    // Удаляем сообщение через 5 секунд
                    setTimeout(() => errorMsg.fadeOut(() => errorMsg.remove()), 5000);
                    
                    isLoading = false;
                });
            }
        }
    });
  }, 200); // Throttle 200ms
  
  $(window).on('scroll', handleScroll);
});


window.addEventListener('refresh-page', event => {
  window.location.reload(false);
})