console.log('Test insights.js');


// Infinite Scroll для статей
let loading = false;
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('scroll', function() {
        if (loading) {
            console.log('Already loading, skip');
            return;
        }
        
        // Проверка: пользователь долистал до конца (500px от низа)
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            loading = true;
            currentPage++;
            
            console.log('Loading articles page:', currentPage);
            
            // Показать индикатор загрузки
            if (!document.querySelector('.loading-spinner')) {
                const spinner = document.createElement('div');
                spinner.className = 'loading-spinner';
                spinner.style.cssText = 'width:100%; text-align:center; padding:20px;';
                spinner.innerHTML = '<p style="color:#FC7361; font-size:16px;">Loading more articles...</p>';
                
                // Найди контейнер со статьями
                const articlesContainer = document.querySelector('.insights-articles');
                if (articlesContainer) {
                    articlesContainer.appendChild(spinner);
                }
            }
            
            // AJAX запрос
            fetch(window.location.pathname + '?page=' + currentPage)
                .then(response => response.text())
                .then(html => {
                    // Убрать спиннер
                    const spinner = document.querySelector('.loading-spinner');
                    if (spinner) spinner.remove();
                    
                    // Парсим HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Ищем карточки статей (класс .cards_group)
                    const newArticles = doc.querySelectorAll('.cards_group');
                    
                    console.log('Found articles:', newArticles.length);
                    
                    if (newArticles.length > 0) {
                        // Получаем существующие ссылки для проверки дублей
                        const existingLinks = new Set();
                        document.querySelectorAll('.cards_group a[href*="/insights/"]').forEach(link => {
                            existingLinks.add(link.href);
                        });
                        
                        console.log('Existing articles:', existingLinks.size);
                        
                        // Находим контейнер для добавления статей
                        const targetContainer = document.querySelector('.insights-articles');
                        
                        if (targetContainer) {
                            let addedCount = 0;
                            
                            newArticles.forEach(article => {
                                const articleLink = article.querySelector('a[href*="/insights/"]');
                                
                                if (articleLink && !existingLinks.has(articleLink.href)) {
                                    targetContainer.appendChild(article.cloneNode(true));
                                    existingLinks.add(articleLink.href);
                                    addedCount++;
                                }
                            });
                            
                            console.log('New articles added:', addedCount);
                            
                            if (addedCount > 0) {
                                setTimeout(function() {
                                    loading = false;
                                }, 500);
                            } else {
                                console.log('No new articles, all are duplicates');
                                loading = true; // Остановить загрузку
                            }
                        } else {
                            console.error('Target container .insights-articles not found');
                            loading = true;
                        }
                    } else {
                        console.log('No more articles');
                        loading = true;
                    }
                })
                .catch(error => {
                    console.error('Error loading articles:', error);
                    const spinner = document.querySelector('.loading-spinner');
                    if (spinner) spinner.remove();
                    loading = false;
                    currentPage--;
                });
        }
    });
});
