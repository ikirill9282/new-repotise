@php
    // #region agent log
    \Illuminate\Support\Facades\Log::info('DEBUG: Dashboard template rendering start', [
        'hypothesisId' => 'D',
        'widgetsUpdateKey' => $this->widgetsUpdateKey ?? 0,
        'startDate' => $this->startDate ?? null,
        'endDate' => $this->endDate ?? null
    ]);
    // #endregion
@endphp
<x-filament-panels::page>
    {{-- Принудительно устанавливаем белый фон для body через CSS --}}
    <style>
        body.fi-body {
            background-color: #ffffff !important;
            background: #ffffff !important;
        }
        
        /* Убеждаемся, что контент не перекрывается */
        .fi-layout {
            display: flex !important;
            flex-direction: row !important;
        }
        
        .fi-main {
            flex: 1 !important;
            position: relative !important;
            z-index: 1 !important;
            margin-left: 282.5px !important;
            width: calc(100% - 282.5px) !important;
            max-width: calc(100% - 282.5px) !important;
        }
        
        /* Убеждаемся, что sidebar не перекрывает контент */
        .fi-sidebar {
            position: fixed !important;
            z-index: 0 !important;
        }
        
        /* Убеждаемся, что контент виден */
        .fi-page-content {
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Принудительно устанавливаем черный цвет для всего текста */
        .fi-main *,
        .fi-page * {
            color: #000000 !important;
        }
        
        /* Исключения для ссылок и кнопок */
        .fi-main a,
        .fi-page a,
        .fi-main button,
        .fi-page button {
            color: inherit !important;
        }
    </style>
    
    <script>
        console.log('Dashboard script loaded');
        console.log('Main element:', document.querySelector('.fi-main'));
        console.log('Page element:', document.querySelector('.fi-page'));
        console.log('Body:', document.body);
        
        // Force show main content
        function forceShowContent() {
            const main = document.querySelector('.fi-main');
            if (main) {
                const computed = window.getComputedStyle(main);
                console.log('Main computed styles:', {
                    display: computed.display,
                    visibility: computed.visibility,
                    opacity: computed.opacity,
                    backgroundColor: computed.backgroundColor,
                    color: computed.color,
                    zIndex: computed.zIndex
                });
                
                main.style.cssText = `
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    z-index: 10000 !important;
                    position: relative !important;
                    background-color: #ffffff !important;
                    color: #000000 !important;
                    min-height: 100vh !important;
                `;
                console.log('Main element fixed');
            }
            
            const page = document.querySelector('.fi-page');
            if (page) {
                const computed = window.getComputedStyle(page);
                console.log('Page computed styles:', {
                    display: computed.display,
                    visibility: computed.visibility,
                    opacity: computed.opacity,
                    backgroundColor: computed.backgroundColor,
                    color: computed.color
                });
                
                page.style.cssText = `
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    z-index: 9999 !important;
                    position: relative !important;
                    background-color: #ffffff !important;
                    color: #000000 !important;
                `;
                console.log('Page element fixed');
            }
            
            // Проверяем и убираем все overlay - это критично!
            // Особенно важно скрыть fi-modal-close-overlay с z-index 40
            const overlays = document.querySelectorAll('.fi-modal-close-overlay, .fi-modal-overlay, [class*="overlay"], [class*="backdrop"]');
            overlays.forEach(overlay => {
                const computed = window.getComputedStyle(overlay);
                const zIndex = parseInt(computed.zIndex) || 0;
                // Скрываем все overlay, особенно с высоким z-index
                if (zIndex >= 30 || overlay.classList.contains('fi-modal-close-overlay')) {
                    console.log('Hiding overlay:', overlay.className, 'Display:', computed.display, 'Z-index:', zIndex);
                    overlay.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; z-index: -1 !important;';
                    // Также удаляем Alpine.js атрибуты, которые могут их показывать
                    overlay.removeAttribute('x-show');
                    overlay.removeAttribute('x-transition');
                    overlay.setAttribute('aria-hidden', 'true');
                    // Удаляем элемент из DOM, если это возможно
                    if (overlay.parentNode && overlay.classList.contains('fi-modal-close-overlay')) {
                        try {
                            overlay.remove();
                        } catch(e) {
                            console.log('Could not remove overlay:', e);
                        }
                    }
                }
            });
            
            // Также проверяем родительские элементы с модальными окнами
            const modalContainers = document.querySelectorAll('[class*="modal"], [class*="Modal"]');
            modalContainers.forEach(container => {
                const computed = window.getComputedStyle(container);
                if (computed.display !== 'none' && computed.zIndex > 100) {
                    console.log('Found modal container:', container.className, 'Display:', computed.display);
                    // Не скрываем сами модальные окна, только overlay
                }
            });
            
            // Убеждаемся, что все дочерние элементы видны
            const allChildren = document.querySelectorAll('.fi-main *, .fi-page *');
            console.log('Total children found:', allChildren.length);
            let hiddenCount = 0;
            allChildren.forEach((child, index) => {
                const computed = window.getComputedStyle(child);
                if (computed.display === 'none' || computed.visibility === 'hidden' || computed.opacity === '0') {
                    hiddenCount++;
                    if (index < 10) { // Логируем первые 10 скрытых элементов
                        console.log('Hidden child found:', child.tagName, child.className, {
                            display: computed.display,
                            visibility: computed.visibility,
                            opacity: computed.opacity
                        });
                    }
                    child.style.display = 'block';
                    child.style.visibility = 'visible';
                    child.style.opacity = '1';
                }
            });
            console.log('Hidden children fixed:', hiddenCount);
            
            // Проверяем, есть ли вообще контент внутри
            const mainContent = document.querySelector('.fi-main > *');
            const pageContent = document.querySelector('.fi-page > *');
            console.log('Main has content:', !!mainContent, mainContent);
            console.log('Page has content:', !!pageContent, pageContent);
            
            // Детальная диагностика внутренних элементов
            if (mainContent) {
                const mainContentRect = mainContent.getBoundingClientRect();
                const mainContentComputed = window.getComputedStyle(mainContent);
                console.log('Main content rect:', mainContentRect);
                console.log('Main content display:', mainContentComputed.display);
                console.log('Main content visibility:', mainContentComputed.visibility);
                console.log('Main content opacity:', mainContentComputed.opacity);
                console.log('Main content height:', mainContentComputed.height);
                console.log('Main content min-height:', mainContentComputed.minHeight);
                console.log('Main content innerHTML length:', mainContent.innerHTML.length);
                
                // Проверяем все дочерние элементы
                const allChildren = mainContent.querySelectorAll('*');
                console.log('Total children in main content:', allChildren.length);
                let visibleChildren = 0;
                let hiddenChildren = 0;
                allChildren.forEach((child, index) => {
                    const childRect = child.getBoundingClientRect();
                    const childComputed = window.getComputedStyle(child);
                    if (childRect.width > 0 && childRect.height > 0 && 
                        childComputed.display !== 'none' && 
                        childComputed.visibility !== 'hidden' &&
                        childComputed.opacity !== '0') {
                        visibleChildren++;
                    } else {
                        hiddenChildren++;
                        if (index < 10) {
                            console.log('Hidden child:', child.tagName, child.className, 'Display:', childComputed.display, 'Visibility:', childComputed.visibility, 'Opacity:', childComputed.opacity, 'Rect:', childRect);
                        }
                    }
                });
                console.log('Visible children:', visibleChildren, 'Hidden children:', hiddenChildren);
            }
            
            // Принудительно устанавливаем цвета для всего контента
            const allTextElements = document.querySelectorAll('.fi-main *, .fi-page *');
            let whiteTextCount = 0;
            allTextElements.forEach((el, index) => {
                const computed = window.getComputedStyle(el);
                // Если цвет белый или очень светлый, меняем на черный
                if (computed.color === 'rgb(255, 255, 255)' || 
                    computed.color === 'rgba(255, 255, 255, 1)' ||
                    computed.color.includes('255, 255, 255')) {
                    whiteTextCount++;
                    if (index < 20) { // Логируем первые 20
                        console.log('White text found:', el.tagName, el.className, 'Color:', computed.color);
                    }
                    el.style.color = '#000000';
                    el.style.setProperty('color', '#000000', 'important');
                }
                // Если фон прозрачный или белый, устанавливаем белый
                if (computed.backgroundColor === 'rgba(0, 0, 0, 0)' || 
                    computed.backgroundColor === 'transparent') {
                    el.style.backgroundColor = '#ffffff';
                    el.style.setProperty('background-color', '#ffffff', 'important');
                }
            });
            console.log('White text elements fixed:', whiteTextCount);
            
            // Также принудительно устанавливаем цвет для всех текстовых элементов
            const textTags = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div', 'a', 'label', 'td', 'th'];
            textTags.forEach(tag => {
                const elements = document.querySelectorAll(`.fi-main ${tag}, .fi-page ${tag}`);
                elements.forEach(el => {
                    const computed = window.getComputedStyle(el);
                    if (computed.color === 'rgb(255, 255, 255)' || computed.color.includes('255, 255, 255')) {
                        el.style.setProperty('color', '#000000', 'important');
                    }
                });
            });
            
            // Также проверяем body фон - это критично!
            const body = document.body;
            if (body) {
                const bodyComputed = window.getComputedStyle(body);
                console.log('Body background:', bodyComputed.backgroundColor);
                // Принудительно устанавливаем белый фон для body
                body.style.backgroundColor = '#ffffff';
                body.style.background = '#ffffff';
            }
            
            // Проверяем, не перекрывает ли sidebar контент
            const sidebar = document.querySelector('.fi-sidebar');
            if (sidebar) {
                const sidebarComputed = window.getComputedStyle(sidebar);
                const sidebarRect = sidebar.getBoundingClientRect();
                console.log('Sidebar z-index:', sidebarComputed.zIndex, 'Position:', sidebarComputed.position, 'Rect:', sidebarRect);
                // Убеждаемся, что sidebar не перекрывает контент
                sidebar.style.zIndex = '0';
                sidebar.style.position = 'fixed';
            }
            
            // Проверяем позицию main относительно sidebar
            const mainElement = document.querySelector('.fi-main');
            if (mainElement && sidebar) {
                // Убираем класс mx-auto, который может мешать
                mainElement.classList.remove('mx-auto');
                
                const sidebarRect = sidebar.getBoundingClientRect();
                const sidebarWidth = sidebarRect.width;
                
                console.log('Sidebar width:', sidebarWidth);
                
                // Принудительно устанавливаем margin-left и ширину
                mainElement.style.setProperty('margin-left', sidebarWidth + 'px', 'important');
                mainElement.style.setProperty('width', `calc(100% - ${sidebarWidth}px)`, 'important');
                mainElement.style.setProperty('max-width', `calc(100% - ${sidebarWidth}px)`, 'important');
                mainElement.style.setProperty('margin-right', '0', 'important');
                
                // Проверяем результат
                const mainRect = mainElement.getBoundingClientRect();
                console.log('Main rect after fix:', mainRect);
                console.log('Main computed margin-left:', window.getComputedStyle(mainElement).marginLeft);
                console.log('Main computed width:', window.getComputedStyle(mainElement).width);
            }
            
            // Проверяем layout структуру
            const layout = document.querySelector('.fi-layout');
            if (layout) {
                const layoutComputed = window.getComputedStyle(layout);
                console.log('Layout display:', layoutComputed.display, 'Position:', layoutComputed.position);
                layout.style.display = 'flex';
                layout.style.flexDirection = 'row';
            }
            
            // Проверяем, есть ли элемент, который перекрывает весь экран
            const allFixedElements = document.querySelectorAll('[style*="position: fixed"], [style*="position:fixed"]');
            allFixedElements.forEach(el => {
                const computed = window.getComputedStyle(el);
                if (computed.position === 'fixed' && computed.zIndex > 10000 && 
                    !el.classList.contains('fi-topbar') && 
                    !el.classList.contains('fi-sidebar')) {
                    const rect = el.getBoundingClientRect();
                    if (rect.width > window.innerWidth * 0.9 && rect.height > window.innerHeight * 0.9) {
                        console.log('Found large fixed element covering screen:', el, 'Z-index:', computed.zIndex);
                        el.style.display = 'none';
                    }
                }
            });
        }
        
        // Выполняем сразу и после загрузки - несколько раз для надежности
        forceShowContent();
        setTimeout(forceShowContent, 50);
        setTimeout(forceShowContent, 100);
        setTimeout(forceShowContent, 200);
        setTimeout(forceShowContent, 500);
        setTimeout(forceShowContent, 1000);
        
        // Также используем MutationObserver для отслеживания появления новых overlay
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const className = node.className || '';
                        const classStr = typeof className === 'string' ? className : (className.baseVal || '');
                        if (node.classList && (
                            node.classList.contains('fi-modal-close-overlay') ||
                            node.classList.contains('fi-modal-overlay') ||
                            (typeof classStr === 'string' && classStr.includes('overlay'))
                        )) {
                            console.log('New overlay detected, hiding it:', node);
                            node.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; z-index: -1 !important;';
                            node.removeAttribute('x-show');
                            node.removeAttribute('x-transition');
                            node.setAttribute('aria-hidden', 'true');
                            // Удаляем элемент, если это overlay
                            if (node.classList.contains('fi-modal-close-overlay')) {
                                try {
                                    setTimeout(() => node.remove(), 0);
                                } catch(e) {
                                    console.log('Could not remove overlay:', e);
                                }
                            }
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Также при обновлении Livewire
        if (window.Livewire) {
            Livewire.hook('morph.updated', () => {
                setTimeout(forceShowContent, 100);
            });
        }
    </script>
    {{-- Explicitly render header widgets --}}
    @php
        try {
            $headerWidgets = $this->getHeaderWidgets();
            \Illuminate\Support\Facades\Log::info('DEBUG: Rendering header widgets in template', ['hypothesisId' => 'K', 'widgets' => $headerWidgets, 'count' => count($headerWidgets)]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DEBUG: Error getting header widgets in template', ['hypothesisId' => 'K', 'error' => $e->getMessage()]);
            $headerWidgets = [];
        }
    @endphp
    @if(count($headerWidgets) > 0)
        <x-filament-widgets::widgets
            :widgets="$headerWidgets"
            :columns="['default' => 1, 'lg' => 2]"
            class="gap-4 mb-4"
        />
    @endif
    
    <div class="space-y-2" wire:key="dashboard-container-{{ $this->widgetsUpdateKey ?? 0 }}">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-lg font-semibold">Dashboard</h2>
        </div>

        {{-- Date Range Selector --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4" wire:key="date-range-selector">
            <form wire:submit.prevent>
                {{ $this->form }}
            </form>
        </div>





        {{-- Key Metrics Overview --}}
        <div class="mb-4" wire:key="key-metrics-{{ $this->widgetsUpdateKey ?? 0 }}-{{ $this->startDate ?? '' }}-{{ $this->endDate ?? '' }}">
            <x-filament-widgets::widgets
                :widgets="[\App\Filament\Widgets\KeyMetricsWidget::class]"
                :columns="['default' => 1]"
                class="gap-2"
            />
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
            <h3 class="text-lg font-semibold mb-3">Quick Actions</h3>
            <div class="flex flex-wrap gap-2">
                <x-filament::button 
                   href="{{ \App\Filament\Resources\ProductResource::getUrl('create') }}"
                   color="primary"
                   size="md">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    Add Product
                </x-filament::button>
                <x-filament::button 
                   href="{{ \App\Filament\Resources\ArticleResource::getUrl('create') }}"
                   color="primary"
                   size="md">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    Add Article
                </x-filament::button>
                <x-filament::button 
                   href="{{ \App\Filament\Resources\UserResource::getUrl('create') }}"
                   color="primary"
                   size="md">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    Add User
                </x-filament::button>
                <x-filament::button 
                   href="{{ \App\Filament\Resources\ModerationQueueResource::getUrl('index') }}"
                   color="warning"
                   size="md">
                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                    Moderation Queue
                </x-filament::button>
                <x-filament::button 
                   href="{{ \App\Filament\Resources\TransactionResource::getUrl('index') }}"
                   color="success"
                   size="md">
                    <x-heroicon-o-credit-card class="w-4 h-4 mr-1" />
                    View Transactions
                </x-filament::button>
                <x-filament::button 
                   href="{{ \App\Filament\Pages\SettingsGeneral::getUrl() }}"
                   color="gray"
                   size="md">
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-1" />
                    General Settings
                </x-filament::button>
            </div>
        </div>

        @php
            // #region agent log
            try {
                $userWidgets = $this->getUserWidgets();
                $productWidgets = $this->getProductWidgets();
                $systemWidgets = $this->getSystemWidgets();
                \Illuminate\Support\Facades\Log::info('DEBUG: Widgets retrieved', [
                    'userWidgets' => $userWidgets,
                    'productWidgets' => $productWidgets,
                    'systemWidgets' => $systemWidgets,
                    'userCount' => count($userWidgets),
                    'productCount' => count($productWidgets),
                    'systemCount' => count($systemWidgets),
                    'hypothesisId' => 'G'
                ]);
                \Illuminate\Support\Facades\Log::info('DEBUG: widgets retrieved in template', ['hypothesisId' => 'G', 'userWidgets' => $userWidgets, 'productWidgets' => $productWidgets, 'systemWidgets' => $systemWidgets]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('DEBUG: Error getting widgets', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
            // #endregion
        @endphp
        {{-- Widgets are now automatically rendered by Filament through getHeaderWidgets() --}}
        {{-- This section is kept for custom layout if needed in the future --}}

        {{-- Recent Activity Feeds --}}
        <div class="mb-4" wire:key="recent-activity-{{ $this->widgetsUpdateKey ?? 0 }}">
            <x-filament-widgets::widgets
                :widgets="[\App\Filament\Widgets\RecentActivityWidget::class]"
                :columns="['default' => 1]"
                class="gap-2"
            />
        </div>

        <!-- Revenue by Category - отдельный блок на половину ширины -->
        <div class="mt-3 revenue-widget-container" wire:key="revenue-widget-{{ $this->widgetsUpdateKey ?? 0 }}">
            <div class="w-full md:w-1/2">
                <x-filament-widgets::widgets
                    :widgets="[\App\Filament\Widgets\RevenueWidget::class]"
                    :columns="['default' => 1]"
                    class="gap-2"
                />
            </div>
        </div>
    </div>

    <style>
        /* Горизонтальное расположение блоков */
        @media (min-width: 1024px) {
            .dashboard-grid {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }
        
        /* Компактные графики */
        .fi-wi-chart canvas {
            max-height: 180px !important;
        }
        
        /* Большой график Revenue by Category - принудительная высота */
        .revenue-widget-container .fi-wi-chart {
            min-height: 600px !important;
            height: 600px !important;
        }
        
        .revenue-widget-container .fi-wi-chart canvas {
            max-height: 600px !important;
            height: 600px !important;
            min-height: 600px !important;
        }
        
        .revenue-widget-container canvas {
            max-height: 600px !important;
            height: 600px !important;
            min-height: 600px !important;
        }
        
        /* Увеличиваем контейнер Revenue widget */
        .revenue-widget-container .fi-wi-widget {
            min-height: 650px !important;
            height: 650px !important;
        }
        
        /* Секция виджета */
        .revenue-widget-container .fi-section {
            min-height: 650px !important;
            height: 650px !important;
        }
        
        /* Контент секции */
        .revenue-widget-container .fi-section-content-ctn {
            min-height: 600px !important;
            height: 600px !important;
            padding: 1rem !important;
        }
        
        /* Принудительная высота для всех элементов Revenue */
        .revenue-widget-container > div {
            min-height: 650px !important;
        }
        
        .revenue-widget-container > div > div {
            min-height: 650px !important;
        }
        
        /* Все вложенные div'ы внутри revenue контейнера */
        .revenue-widget-container .fi-wi {
            min-height: 650px !important;
        }
        
        /* Chart.js контейнер */
        .revenue-widget-container .fi-wi-chart > div,
        .revenue-widget-container .fi-wi-chart > div > div {
            min-height: 600px !important;
            height: 600px !important;
        }
        
        /* Компактные виджеты статистики */
        .fi-wi-stats-overview-widget {
            padding: 0.5rem !important;
        }
        
        /* Уменьшаем отступы в секциях */
        .fi-section-content-ctn {
            padding: 0.5rem !important;
        }
        
        /* Компактные заголовки виджетов */
        .fi-wi-widget-header {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }
        
        /* Ограничиваем высоту колонок */
        .dashboard-column {
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }
        
        /* Компактные карточки статистики */
        .fi-wi-stats-overview-widget .fi-stats-overview-stat {
            padding: 0.5rem !important;
        }
        
        /* Убеждаемся, что виджеты видны */
        .fi-wi-widget,
        .fi-wi,
        .fi-page-header-widgets,
        .fi-wi-stats-overview-widget,
        .fi-wi-chart {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Убеждаемся, что контент виджетов виден */
        .fi-wi-widget *,
        .fi-wi *,
        .fi-page-header-widgets * {
            color: inherit !important;
            visibility: visible !important;
        }
        
        /* Убеждаемся, что статистика виджета видна */
        .fi-stats-overview-stat {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Критическое исправление - скрываем все overlay, которые перекрывают контент */
        .fi-modal-close-overlay {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            z-index: -1 !important;
        }
        
        .fi-modal-overlay:not(.fi-modal-overlay-visible) {
            display: none !important;
        }
        
        /* Убеждаемся, что основной контент выше overlay */
        .fi-main {
            z-index: 10000 !important;
            position: relative !important;
        }
        
        .fi-page {
            z-index: 10000 !important;
            position: relative !important;
        }
    </style>

    @script
    <script>
        // Принудительная установка высоты для Revenue widget после загрузки
        function setRevenueHeight() {
            const revenueContainer = document.querySelector('.revenue-widget-container');
            if (revenueContainer) {
                const chart = revenueContainer.querySelector('.fi-wi-chart');
                const canvas = revenueContainer.querySelector('canvas');
                const section = revenueContainer.querySelector('.fi-section');
                
                if (chart) {
                    chart.style.minHeight = '600px';
                    chart.style.height = '600px';
                }
                if (canvas) {
                    canvas.style.height = '600px';
                    canvas.style.minHeight = '600px';
                }
                if (section) {
                    section.style.minHeight = '650px';
                    section.style.height = '650px';
                }
            }
        }
        
        // Устанавливаем высоту после обновления Livewire
        $wire.on('dashboard-date-range-updated', () => {
            setTimeout(setRevenueHeight, 200);
        });
        
        // Устанавливаем высоту после загрузки
        setTimeout(setRevenueHeight, 500);
    </script>
    @endscript
</x-filament-panels::page>
