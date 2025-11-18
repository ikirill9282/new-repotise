document.addEventListener('DOMContentLoaded', function() {
  const articlesContainer = document.querySelector('.insights-articles');
  const newsContainer = document.querySelector('.travel-news-container');
  const newsList = newsContainer ? newsContainer.querySelector('.travel-news-list') : null;
  const newsLoader = newsContainer ? newsContainer.querySelector('.travel-news-loader') : null;

  let articlesPage = 1;
  let articlesLoading = false;
  let articlesHasMore = true;

  let newsLoading = false;
  const parseNumber = (value) => {
    if (value === undefined || value === null || value === '') {
      return null;
    }

    const parsed = Number(value);
    return Number.isNaN(parsed) ? null : parsed;
  };

  let newsNextPage = newsContainer ? parseNumber(newsContainer.dataset.nextPage) : null;
  const newsEndpoint = newsContainer ? newsContainer.dataset.endpoint : null;
  const newsPerPage = newsContainer ? newsContainer.dataset.perPage : null;

  const ensureArticlesSpinner = () => {
    if (!articlesContainer) {
      return null;
    }

    let spinner = articlesContainer.querySelector('.loading-spinner');
    if (!spinner) {
      spinner = document.createElement('div');
      spinner.className = 'loading-spinner';
      spinner.style.cssText = 'width:100%; text-align:center; padding:20px;';
      spinner.innerHTML = '<p style="color:#FC7361; font-size:16px;">Loading more articles...</p>';
      articlesContainer.appendChild(spinner);
    }

    return spinner;
  };

  const loadMoreArticles = () => {
    if (!articlesContainer || articlesLoading || !articlesHasMore) {
      return;
    }

    const nextPage = articlesPage + 1;
    articlesLoading = true;

    const spinner = ensureArticlesSpinner();

    const url = new URL(window.location.pathname, window.location.origin);
    url.searchParams.set('page', nextPage);

    fetch(url.toString(), {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    })
      .then((response) => response.text())
      .then((html) => {
        if (spinner) {
          spinner.remove();
        }

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newArticles = doc.querySelectorAll('.cards_group');

        if (!newArticles.length) {
          articlesHasMore = false;
          return;
        }

        const existingLinks = new Set();
        articlesContainer.querySelectorAll('.cards_group a[href*="/insights/"]').forEach((link) => {
          existingLinks.add(link.href);
        });

        let addedCount = 0;

        newArticles.forEach((article) => {
          const articleLink = article.querySelector('a[href*="/insights/"]');

          if (articleLink && !existingLinks.has(articleLink.href)) {
            articlesContainer.appendChild(article.cloneNode(true));
            existingLinks.add(articleLink.href);
            addedCount += 1;
          }
        });

        if (addedCount > 0) {
          articlesPage = nextPage;
        } else {
          articlesHasMore = false;
        }
      })
      .catch((error) => {
        console.error('Error loading articles:', error);
      })
      .finally(() => {
        if (spinner && spinner.parentNode) {
          spinner.remove();
        }
        articlesLoading = false;
      });
  };

  const loadMoreNews = () => {
    if (!newsContainer || !newsList || newsLoading || !newsEndpoint || !newsNextPage) {
      return;
    }

    newsLoading = true;
    if (newsLoader) {
      newsLoader.classList.remove('hidden');
    }

    const url = new URL(newsEndpoint, window.location.origin);
    url.searchParams.set('page', newsNextPage);
    if (newsPerPage) {
      url.searchParams.set('per_page', newsPerPage);
    }

    fetch(url.toString(), {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
      },
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Failed to load travel news');
        }
        return response.json();
      })
      .then((data) => {
        const html = data?.html ?? '';

        if (html.trim().length) {
          const wrapper = document.createElement('div');
          wrapper.innerHTML = html;
          const items = wrapper.querySelectorAll('.travel-news-item');

          items.forEach((item) => {
            newsList.appendChild(item);
          });
        }

        const next = data?.next_page ?? null;
        newsNextPage = next ? Number(next) : null;
        newsContainer.dataset.nextPage = newsNextPage ?? '';
      })
      .catch((error) => {
        console.error('Error loading travel news:', error);
      })
      .finally(() => {
        if (newsLoader) {
          newsLoader.classList.add('hidden');
        }
        newsLoading = false;
      });
  };

  // Throttle функция для оптимизации обработчика скролла
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

  const handleScroll = throttle(() => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
      loadMoreArticles();
      loadMoreNews();
    }
  }, 100); // Проверяем каждые 100ms вместо каждого события скролла

  window.addEventListener('scroll', handleScroll, { passive: true });
});
