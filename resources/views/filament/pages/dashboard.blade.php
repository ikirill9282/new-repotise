<x-filament-panels::page>
    <div class="space-y-2">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-lg font-semibold">Dashboard</h2>
            <div class="flex gap-2">
                <!-- Фильтры будут добавлены позже -->
            </div>
        </div>

        <div class="dashboard-grid grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Левая колонка: Пользователи -->
            <div class="dashboard-column space-y-2">
                <div class="flex items-center gap-2 pb-1 border-b border-gray-200">
                    <x-heroicon-o-user-group class="w-4 h-4 text-white" />
                    <h3 class="text-[30px] font-semibold text-white">Users</h3>
                </div>
                <x-filament-widgets::widgets
                    :widgets="$this->getUserWidgets()"
                    :columns="['default' => 1]"
                    class="gap-2"
                />
            </div>

            <!-- Центральная колонка: Товары -->
            <div class="dashboard-column space-y-2">
                <div class="flex items-center gap-2 pb-1 border-b border-gray-200">
                    <x-heroicon-o-shopping-bag class="w-4 h-4 text-white" />
                    <h3 class="text-[30px] font-semibold text-white">Products</h3>
                </div>
                <x-filament-widgets::widgets
                    :widgets="$this->getProductWidgets()"
                    :columns="['default' => 1]"
                    class="gap-2"
                />
            </div>

            <!-- Правая колонка: Системные -->
            <div class="dashboard-column space-y-2">
                <div class="flex items-center gap-2 pb-1 border-b border-gray-200">
                    <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-white" />
                    <h3 class="text-[30px] font-semibold text-white">System</h3>
                </div>
                <x-filament-widgets::widgets
                    :widgets="$this->getSystemWidgets()"
                    :columns="['default' => 1]"
                    class="gap-2"
                />
            </div>
        </div>

        <!-- Revenue by Category - отдельный блок на половину ширины -->
        <div class="mt-3 revenue-widget-container">
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
    </style>

    <script>
        // Принудительная установка высоты для Revenue widget после загрузки
        document.addEventListener('DOMContentLoaded', function() {
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
            
            setRevenueHeight();
            
            // Повторяем после обновления Livewire
            Livewire.hook('morph.updated', () => {
                setTimeout(setRevenueHeight, 100);
            });
        });
    </script>
</x-filament-panels::page>
