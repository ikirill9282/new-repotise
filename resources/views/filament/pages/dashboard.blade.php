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
            overflow: visible !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
        }
        
        .fi-page,
        .fi-page-content,
        .fi-page-content > * {
            overflow: visible !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
            position: relative !important;
            z-index: 1 !important;
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
        
        /* КРИТИЧНО: Исправляем opacity: 0 на родительском контейнере main */
        .fi-main-ctn {
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
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
        // Исправление видимости контента в Filament
        function forceShowContent() {
            // Скрываем все overlay элементы
            const overlays = document.querySelectorAll('.fi-modal-close-overlay, .fi-sidebar-close-overlay, .fi-modal-overlay');
            overlays.forEach(overlay => {
                overlay.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; z-index: -1 !important;';
                overlay.removeAttribute('x-show');
                overlay.removeAttribute('x-transition');
                overlay.setAttribute('aria-hidden', 'true');
            });
            
            // Устанавливаем белый фон для body
            const body = document.body;
            if (body) {
                body.style.backgroundColor = '#ffffff';
                body.style.background = '#ffffff';
            }
            
            // Исправляем позицию main относительно sidebar
            const sidebar = document.querySelector('.fi-sidebar');
            const mainElement = document.querySelector('.fi-main');
            
            if (mainElement && sidebar) {
                mainElement.classList.remove('mx-auto');
                
                const sidebarWidth = sidebar.getBoundingClientRect().width;
                
                mainElement.style.setProperty('margin-left', sidebarWidth + 'px', 'important');
                mainElement.style.setProperty('width', `calc(100% - ${sidebarWidth}px)`, 'important');
                mainElement.style.setProperty('max-width', `calc(100% - ${sidebarWidth}px)`, 'important');
                mainElement.style.setProperty('padding-left', '0', 'important');
                
                // Исправляем overflow на родительских элементах
                const mainContent = mainElement.querySelector('.fi-page, [wire\\:id]');
                if (mainContent) {
                    mainContent.style.setProperty('overflow', 'visible', 'important');
                    mainContent.style.setProperty('overflow-x', 'visible', 'important');
                    mainContent.style.setProperty('overflow-y', 'visible', 'important');
                    
                    // Проверяем родительские элементы на overflow: hidden
                    let parent = mainContent.parentElement;
                    let level = 0;
                    while (parent && parent !== document.body && level < 10) {
                        const parentComputed = window.getComputedStyle(parent);
                        if (parentComputed.overflow === 'hidden' || 
                            parentComputed.overflowX === 'hidden' || 
                            parentComputed.overflowY === 'hidden') {
                            parent.style.setProperty('overflow', 'visible', 'important');
                            parent.style.setProperty('overflow-x', 'visible', 'important');
                            parent.style.setProperty('overflow-y', 'visible', 'important');
                        }
                        parent = parent.parentElement;
                        level++;
                    }
                }
            }
            
            // КРИТИЧНО: Исправляем opacity: 0 на родительском контейнере main
            const mainCtn = document.querySelector('.fi-main-ctn');
            if (mainCtn) {
                const mainCtnComputed = window.getComputedStyle(mainCtn);
                if (mainCtnComputed.opacity === '0' || mainCtnComputed.visibility === 'hidden') {
                    mainCtn.style.setProperty('opacity', '1', 'important');
                    mainCtn.style.setProperty('visibility', 'visible', 'important');
                    mainCtn.style.setProperty('display', 'block', 'important');
                    mainCtn.classList.remove('opacity-0');
                }
            }
            
            // Исправляем layout структуру
            const layout = document.querySelector('.fi-layout');
            if (layout) {
                layout.style.display = 'flex';
                layout.style.flexDirection = 'row';
            }
        }
        
        // Выполняем с защитой от множественных вызовов
        let forceShowContentCalled = false;
        const callForceShowContent = () => {
            if (!forceShowContentCalled) {
                forceShowContentCalled = true;
                forceShowContent();
                setTimeout(() => { forceShowContentCalled = false; }, 2000);
            }
        };
        
        callForceShowContent();
        setTimeout(callForceShowContent, 100);
        setTimeout(callForceShowContent, 500);
        
        // MutationObserver для отслеживания новых overlay
        let observerActive = true;
        let lastObserverAction = 0;
        const OBSERVER_THROTTLE = 500;
        
        const observer = new MutationObserver(function(mutations) {
            const now = Date.now();
            if (!observerActive || (now - lastObserverAction) < OBSERVER_THROTTLE) {
                return;
            }
            
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList && (
                        node.classList.contains('fi-modal-close-overlay') ||
                        node.classList.contains('fi-sidebar-close-overlay') ||
                        node.classList.contains('fi-modal-overlay') ||
                        node.classList.contains('fi-main-ctn')
                    )) {
                        lastObserverAction = now;
                        observerActive = false;
                        
                        if (node.classList.contains('fi-main-ctn')) {
                            node.style.setProperty('opacity', '1', 'important');
                            node.classList.remove('opacity-0');
                        } else {
                            node.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; z-index: -1 !important;';
                            node.removeAttribute('x-show');
                            node.removeAttribute('x-transition');
                            node.setAttribute('aria-hidden', 'true');
                            
                            if (node.classList.contains('fi-modal-close-overlay')) {
                                setTimeout(() => {
                                    node.remove();
                                    setTimeout(() => { observerActive = true; }, 100);
                                }, 0);
                                return;
                            }
                        }
                        
                        setTimeout(() => { observerActive = true; }, 100);
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Обновление при изменении Livewire
        if (window.Livewire) {
            let isUpdating = false;
            let lastUpdateTime = 0;
            const UPDATE_THROTTLE = 1000;
            
            Livewire.hook('morph.updated', () => {
                const now = Date.now();
                if (!isUpdating && (now - lastUpdateTime) > UPDATE_THROTTLE) {
                    isUpdating = true;
                    lastUpdateTime = now;
                    setTimeout(() => {
                        forceShowContent();
                        isUpdating = false;
                    }, 100);
                }
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
