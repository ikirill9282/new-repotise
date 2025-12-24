<style>
    /* Мягкие исправления для видимости контента в Filament */
    /* Исправляем только основные контейнеры контента */
    .fi-main {
        position: relative !important;
        z-index: 1 !important;
        overflow: visible !important;
    }
    
    .fi-page {
        position: relative !important;
        z-index: 1 !important;
        overflow: visible !important;
    }
    
    /* Убираем только скрытые overlay */
    .fi-modal-overlay:not(.fi-modal-overlay-visible) {
        display: none !important;
    }
</style>

<script>
    // Глобальный скрипт для исправления видимости контента
    document.addEventListener('DOMContentLoaded', function() {
        function fixContentVisibility() {
            const selectors = [
                '.fi-main',
                '.fi-page',
                '.fi-page-content',
                '.fi-body',
                '.fi-content',
                '.fi-wrapper',
                '.fi-main-content',
                '.fi-page-wrapper',
                '.fi-content-wrapper',
                '.fi-layout'
            ];
            
            selectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => {
                    if (el) {
                        el.style.position = 'relative';
                        el.style.zIndex = '1';
                        el.style.overflow = 'visible';
                        el.style.display = 'block';
                        el.style.visibility = 'visible';
                        el.style.opacity = '1';
                        el.style.height = 'auto';
                        el.style.minHeight = 'auto';
                        el.style.backgroundColor = 'transparent';
                        el.style.background = 'transparent';
                    }
                });
            });
            
            const body = document.querySelector('body.fi-body');
            if (body) {
                // Не меняем фон body, чтобы не сломать верстку
            }
            
            const overlays = document.querySelectorAll('.fi-modal-overlay:not(.fi-modal-overlay-visible)');
            overlays.forEach(overlay => {
                overlay.style.display = 'none';
            });
        }
        
        fixContentVisibility();
        
        // Исправляем при каждом обновлении Livewire
        if (window.Livewire) {
            Livewire.hook('morph.updated', () => {
                setTimeout(fixContentVisibility, 100);
            });
        }
        
        // Исправляем при изменении DOM
        const observer = new MutationObserver(() => {
            fixContentVisibility();
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>

