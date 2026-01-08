<?php

namespace App\Core;

/**
 * Ядро платформы - ПОЛНЫЙ маппинг всех страниц, компонентов, API и функций
 * 
 * Использование:
 * PlatformCore::getPage('home') - получить информацию о странице
 * PlatformCore::getApi('cart.push') - получить информацию об API endpoint
 * PlatformCore::getModal('cart') - получить информацию о модальном окне
 */
class PlatformCore
{
    /**
     * ПОЛНЫЙ маппинг всех страниц платформы
     */
    private static array $pages = [
        // ========== ПУБЛИЧНЫЕ СТРАНИЦЫ ==========
        'home' => [
            'route' => 'home',
            'url' => '/',
            'controller' => ['SiteController', 'home'],
            'view' => 'site.pages.home',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Главная страница',
        ],

        'products' => [
            'route' => 'products',
            'url' => '/products',
            'controller' => ['SiteController', 'products'],
            'view' => 'site.pages.products',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Каталог продуктов',
        ],

        'product' => [
            'route' => 'products.country.product',
            'url' => '/products/{product}',
            'controller' => ['SiteController', 'product'],
            'view' => 'site.pages.product',
            'livewire' => ['Modals.Product', 'Modals.Cart'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Страница продукта',
        ],

        'creators' => [
            'route' => 'creators',
            'url' => '/creators',
            'controller' => ['SiteController', 'creators'],
            'view' => 'site.pages.creators',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Каталог создателей',
        ],

        'insights' => [
            'route' => 'insights',
            'url' => '/insights',
            'controller' => ['SiteController', 'insights'],
            'view' => 'site.pages.insights',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Статьи и инсайты',
        ],

        'insights.news' => [
            'route' => 'insights.news',
            'url' => '/insights/news',
            'controller' => ['SiteController', 'insightsNews'],
            'view' => 'site.pages.news',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Новости',
        ],

        'feed' => [
            'route' => 'feed',
            'url' => '/insights/{article}',
            'controller' => ['SiteController', 'feed'],
            'view' => 'site.pages.feed',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Просмотр статьи',
        ],

        'search' => [
            'route' => 'search',
            'url' => '/search',
            'controller' => ['SiteController', 'search'],
            'view' => 'site.pages.search',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Поиск',
        ],

        'favorites' => [
            'route' => 'favorites',
            'url' => '/favorites',
            'controller' => ['SiteController', 'favorites'],
            'view' => 'site.pages.favorites',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Избранное',
            'auth' => true,
        ],

        'help-center' => [
            'route' => 'help-center',
            'url' => '/help-center',
            'controller' => ['SiteController', 'helpCenter'],
            'view' => 'site.pages.help-center',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Центр помощи',
        ],

        'policies' => [
            'route' => 'policies',
            'url' => '/policies-all',
            'controller' => ['SiteController', 'allPolicies'],
            'view' => 'site.pages.policies-all',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Все политики',
        ],

        'policies.single' => [
            'route' => null,
            'url' => '/policies/{slug}',
            'controller' => ['SiteController', 'policies'],
            'view' => 'site.pages.policies',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Отдельная политика',
        ],

        'sellers' => [
            'route' => 'sellers',
            'url' => '/sellers',
            'controller' => ['SiteController', 'sellers'],
            'view' => 'site.pages.sellers',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Продавцы',
        ],

        'referal' => [
            'route' => 'referal',
            'url' => '/referal',
            'controller' => ['SiteController', 'referal'],
            'view' => 'site.pages.referal',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Реферальная программа',
        ],

        'gift' => [
            'route' => 'gift',
            'url' => '/gift/{token}',
            'controller' => ['SiteController', 'gift'],
            'view' => 'site.pages.gift',
            'livewire' => ['ClaimGift'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Подарок',
        ],

        'investments' => [
            'route' => 'investments',
            'url' => '/investments',
            'controller' => ['SiteController', 'investments'],
            'view' => 'site.pages.investments',
            'livewire' => ['Forms.Invest'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Инвестиции',
        ],

        // ========== ОПЛАТА ==========
        'checkout' => [
            'route' => 'checkout',
            'url' => '/payment/checkout',
            'controller' => ['PaymentController', 'checkout'],
            'view' => 'site.pages.checkout',
            'livewire' => ['Checkout'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Оформление заказа',
        ],

        'checkout.subscription' => [
            'route' => 'checkout.subscription',
            'url' => '/payment/checkout-subscription',
            'controller' => ['PaymentController', 'checkoutSubscription'],
            'view' => 'site.pages.checkout-subscription',
            'livewire' => ['CheckoutSubscription'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Оформление подписки',
        ],

        'payment.success' => [
            'route' => 'payment.success',
            'url' => '/payment/success',
            'controller' => ['PaymentController', 'success'],
            'view' => 'site.pages.payment-success',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Успешная оплата',
        ],

        'payment.error' => [
            'route' => 'payment.error',
            'url' => '/payment/error',
            'controller' => ['PaymentController', 'error'],
            'view' => 'site.pages.payment-error',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Ошибка оплаты',
        ],

        'subscription.success' => [
            'route' => 'subscription.success',
            'url' => '/payment/subscription-success',
            'controller' => ['PaymentController', 'success'],
            'view' => 'site.pages.subscription-success',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Успешная подписка',
        ],

        // ========== ПРОФИЛЬ ПОЛЬЗОВАТЕЛЯ ==========
        'profile' => [
            'route' => 'profile',
            'url' => '/profile',
            'controller' => ['CabinetController', 'profile'],
            'view' => 'site.pages.profile-creator',
            'livewire' => ['Profile.Page', 'Profile.SocialAside', 'Profile.Balances'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Профиль создателя (личный)',
            'auth' => true,
        ],

        'profile.edit' => [
            'route' => 'profile.edit',
            'url' => '/profile/edit',
            'controller' => ['CabinetController', 'edit'],
            'view' => 'site.pages.profile-edit',
            'livewire' => ['Profile.Edit'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Редактирование профиля',
            'auth' => true,
        ],

        'profile.dashboard' => [
            'route' => 'profile.dashboard',
            'url' => '/profile/dashboard',
            'controller' => ['CabinetController', 'dashboard'],
            'view' => 'site.pages.profile-dashboard',
            'livewire' => ['Profile.Analytics', 'Profile.Tables'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Дашборд профиля',
            'auth' => true,
        ],

        'profile.products' => [
            'route' => 'profile.products',
            'url' => '/profile/products',
            'controller' => ['CabinetController', 'products'],
            'view' => 'site.pages.profile-products',
            'livewire' => ['Profile.Tables.ProfileProduct'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Мои продукты',
            'auth' => true,
        ],

        'profile.products.create' => [
            'route' => 'profile.products.create',
            'url' => '/profile/products/create',
            'controller' => ['CabinetController', 'create_product'],
            'view' => 'site.pages.create-product',
            'livewire' => ['Forms.Product'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Создание продукта',
            'auth' => true,
        ],

        'profile.products.create.media' => [
            'route' => 'profile.products.create.media',
            'url' => '/profile/products/create/media',
            'controller' => ['CabinetController', 'create_product_media'],
            'view' => 'site.pages.create-product-media',
            'livewire' => ['Forms.ProductMedia'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Загрузка медиа для продукта',
            'auth' => true,
        ],

        'profile.articles' => [
            'route' => 'profile.articles',
            'url' => '/profile/articles',
            'controller' => ['CabinetController', 'articles'],
            'view' => 'site.pages.profile-articles',
            'livewire' => ['Profile.Tables.ProfileArticle'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Мои статьи',
            'auth' => true,
        ],

        'profile.articles.create' => [
            'route' => 'profile.articles.create',
            'url' => '/profile/articles/create',
            'controller' => ['CabinetController', 'create_article'],
            'view' => 'site.pages.create-article',
            'livewire' => ['Forms.Article'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Создание статьи',
            'auth' => true,
        ],

        'profile.purchases' => [
            'route' => 'profile.purchases',
            'url' => '/profile/purchases',
            'controller' => ['CabinetController', 'purchases'],
            'view' => 'site.pages.profile-purchases',
            'livewire' => ['Profile.Tables.Orders', 'Profile.Tables.Subs'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Мои покупки',
            'auth' => true,
        ],

        'profile.purchases.subscriptions' => [
            'route' => 'profile.purchases.subscriptions',
            'url' => '/profile/purchases/{type}',
            'controller' => ['CabinetController', 'purchases'],
            'view' => 'site.pages.profile-purchases',
            'livewire' => ['Profile.Tables.Orders', 'Profile.Tables.Subs'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Мои покупки (подписки)',
            'auth' => true,
        ],

        'profile.sales' => [
            'route' => 'profile.sales',
            'url' => '/profile/sales',
            'controller' => ['CabinetController', 'sales'],
            'view' => 'site.pages.profile-sales',
            'livewire' => ['Profile.Tables.Sales', 'Profile.Tables.SalesAnalytics'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Мои продажи',
            'auth' => true,
        ],

        'profile.reviews' => [
            'route' => 'profile.reviews',
            'url' => '/profile/reviews',
            'controller' => ['CabinetController', 'reviews'],
            'view' => 'site.pages.profile-reviews',
            'livewire' => ['Profile.Tables.ProfileReviews'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Отзывы',
            'auth' => true,
        ],

        'profile.settings' => [
            'route' => 'profile.settings',
            'url' => '/profile/settings',
            'controller' => ['CabinetController', 'settings'],
            'view' => 'site.pages.profile-settings',
            'livewire' => ['Profile.Settings'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Настройки профиля',
            'auth' => true,
        ],

        'profile.settings.email.verify' => [
            'route' => 'profile.settings.email.verify',
            'url' => '/profile/settings/email/verify/{token}',
            'controller' => ['CabinetController', 'confirmEmailChange'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'Подтверждение смены email',
            'auth' => true,
        ],

        'profile.referal' => [
            'route' => 'profile.referal',
            'url' => '/profile/referal',
            'controller' => ['CabinetController', 'referal'],
            'view' => 'site.pages.profile-referal',
            'livewire' => ['Profile.Tables.ProfileReferal'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Реферальная программа',
            'auth' => true,
        ],

        'profile.verify' => [
            'route' => 'verify',
            'url' => '/profile/verify',
            'controller' => ['CabinetController', 'verify'],
            'view' => 'site.pages.verify',
            'livewire' => [],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Верификация аккаунта',
            'auth' => true,
        ],

        'profile.verify.complete' => [
            'route' => null,
            'url' => '/profile/verify/complete',
            'controller' => ['CabinetController', 'verifyComplete'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'Завершение верификации',
            'auth' => true,
        ],

        'profile.verify.cancel' => [
            'route' => null,
            'url' => '/profile/verify/cancel',
            'controller' => ['CabinetController', 'verifyCancel'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'Отмена верификации',
            'auth' => true,
        ],

        'profile.checkout' => [
            'route' => 'profile.checkout',
            'url' => '/profile/checkout',
            'controller' => ['CabinetController', 'checkout'],
            'view' => null, // Редирект на checkout
            'livewire' => [],
            'components' => [],
            'description' => 'Переход к оформлению заказа',
            'auth' => true,
        ],

        // ========== ПУБЛИЧНЫЙ ПРОФИЛЬ ==========
        'view.profile' => [
            'route' => 'view.profile',
            'url' => '/profile/@{slug}',
            'controller' => ['CabinetController', 'public_profile'],
            'view' => 'site.pages.profile',
            'livewire' => ['Profile.Page', 'Profile.SocialAside'],
            'components' => ['site.header', 'site.footer'],
            'description' => 'Публичный профиль пользователя',
        ],

        // ========== АВТОРИЗАЦИЯ ==========
        'auth.signin' => [
            'route' => 'signin',
            'url' => '/auth/signin',
            'controller' => ['AuthController', 'signin'],
            'view' => null, // API endpoint
            'livewire' => ['Modals.Auth'],
            'components' => [],
            'description' => 'Вход',
        ],

        'auth.signout' => [
            'route' => 'signout',
            'url' => '/auth/signout',
            'controller' => ['AuthController', 'signout'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'Выход',
        ],

        'auth.email.verify' => [
            'route' => null,
            'url' => '/auth/email/verify',
            'controller' => ['AuthController', 'verifyEmail'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'Подтверждение email',
        ],

        'auth.google.callback' => [
            'route' => null,
            'url' => '/auth/google/callback',
            'controller' => ['AuthController', 'googleCallback'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'Google OAuth callback',
        ],

        'auth.facebook.callback' => [
            'route' => null,
            'url' => '/auth/facebook/callback',
            'controller' => ['AuthController', 'facebookCallback'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'Facebook OAuth callback',
        ],

        'auth.x.callback' => [
            'route' => null,
            'url' => '/auth/x/callback',
            'controller' => ['AuthController', 'xCallback'],
            'view' => null, // Редирект
            'livewire' => [],
            'components' => [],
            'description' => 'X (Twitter) OAuth callback',
        ],
    ];

    /**
     * Маппинг всех модальных окон
     */
    private static array $modals = [
        'auth' => [
            'livewire' => 'Modals.Auth',
            'view' => 'livewire.modals.auth',
            'description' => 'Авторизация',
        ],
        'register' => [
            'livewire' => 'Modals.Register',
            'view' => 'livewire.modals.register',
            'description' => 'Регистрация',
        ],
        'cart' => [
            'livewire' => 'Modals.Cart',
            'view' => 'livewire.modals.cart',
            'description' => 'Корзина',
        ],
        'product' => [
            'livewire' => 'Modals.Product',
            'view' => 'livewire.modals.product',
            'description' => 'Модальное окно продукта',
        ],
        'payment-method' => [
            'livewire' => 'Modals.PaymentMethod',
            'view' => 'livewire.modals.payment-method',
            'description' => 'Способ оплаты',
        ],
        'payout-method' => [
            'livewire' => 'Modals.PayoutMethod',
            'view' => 'livewire.modals.payout-method',
            'description' => 'Способ вывода средств',
        ],
        'donate' => [
            'livewire' => 'Modals.Donate',
            'view' => 'livewire.modals.donate',
            'description' => 'Донат',
        ],
        'withdraw' => [
            'livewire' => 'Modals.Withdraw',
            'view' => 'livewire.modals.withdraw',
            'description' => 'Вывод средств',
        ],
        'funds' => [
            'livewire' => 'Modals.Funds',
            'view' => 'livewire.modals.funds',
            'description' => 'Пополнение баланса',
        ],
        'change-email' => [
            'livewire' => 'Modals.ChangeEmail',
            'view' => 'livewire.modals.change-email',
            'description' => 'Смена email',
        ],
        'reset-password' => [
            'livewire' => 'Modals.ResetPassword',
            'view' => 'livewire.modals.reset-password',
            'description' => 'Сброс пароля',
        ],
        'twofa' => [
            'livewire' => 'Modals.Twofa',
            'view' => 'livewire.modals.twofa',
            'description' => 'Двухфакторная аутентификация',
        ],
        'delete-account' => [
            'livewire' => 'Modals.DeleteAccount',
            'view' => 'livewire.modals.delete-account',
            'description' => 'Удаление аккаунта',
        ],
        'delete-product' => [
            'livewire' => 'Modals.DeleteProduct',
            'view' => 'livewire.modals.delete-product',
            'description' => 'Удаление продукта',
        ],
        'delete-article' => [
            'livewire' => 'Modals.DeleteArticle',
            'view' => 'livewire.modals.delete-article',
            'description' => 'Удаление статьи',
        ],
        'refund' => [
            'livewire' => 'Modals.Refund',
            'view' => 'livewire.modals.refund',
            'description' => 'Возврат средств',
        ],
        'order' => [
            'livewire' => 'Modals.Order',
            'view' => 'livewire.modals.order',
            'description' => 'Детали заказа',
        ],
        'message' => [
            'livewire' => 'Modals.Message',
            'view' => 'livewire.modals.message',
            'description' => 'Сообщение',
        ],
        'contact' => [
            'livewire' => 'Modals.Contact',
            'view' => 'livewire.modals.contact',
            'description' => 'Контактная форма',
        ],
        'report' => [
            'livewire' => 'Modals.Report',
            'view' => 'livewire.modals.report',
            'description' => 'Жалоба',
        ],
    ];

    /**
     * Маппинг всех API endpoints
     */
    private static array $api = [
        // Поиск
        'search' => [
            'method' => 'GET',
            'url' => '/api/search',
            'controller' => ['Api\SearchController', 'search'],
            'description' => 'Поиск по платформе',
        ],

        // Данные
        'data.feed' => [
            'method' => 'GET',
            'url' => '/api/data/feed/{id}',
            'controller' => ['Api\DataController', 'feed'],
            'description' => 'Получить данные статьи',
        ],
        'data.tags' => [
            'method' => 'GET',
            'url' => '/api/data/tags',
            'controller' => ['Api\DataController', 'tags'],
            'description' => 'Получить теги',
        ],
        'data.types' => [
            'method' => 'GET',
            'url' => '/api/data/types',
            'controller' => ['Api\DataController', 'types'],
            'description' => 'Получить типы',
        ],
        'data.locations' => [
            'method' => 'GET',
            'url' => '/api/data/locations',
            'controller' => ['Api\DataController', 'locations'],
            'description' => 'Получить локации',
        ],
        'data.categories' => [
            'method' => 'GET',
            'url' => '/api/data/categories',
            'controller' => ['Api\DataController', 'categories'],
            'description' => 'Получить категории',
        ],
        'data.messages' => [
            'method' => 'POST',
            'url' => '/api/data/messages',
            'controller' => ['Api\DataController', 'messages'],
            'description' => 'Отправить сообщение',
        ],
        'data.favorite-author' => [
            'method' => 'POST',
            'url' => '/api/data/favorite-author',
            'controller' => ['Api\DataController', 'favorite_author'],
            'description' => 'Добавить автора в избранное',
        ],
        'data.upload-image' => [
            'method' => 'POST',
            'url' => '/api/data/upload-image',
            'controller' => ['Api\DataController', 'uploadImage'],
            'auth' => true,
            'description' => 'Загрузить изображение',
        ],

        // Корзина
        'cart.push' => [
            'method' => 'POST',
            'url' => '/api/cart/push',
            'controller' => ['Api\CartController', 'push'],
            'description' => 'Добавить в корзину',
        ],
        'cart.count' => [
            'method' => 'POST',
            'url' => '/api/cart/count',
            'controller' => ['Api\CartController', 'count'],
            'description' => 'Получить количество товаров в корзине',
        ],
        'cart.remove' => [
            'method' => 'POST',
            'url' => '/api/cart/remove',
            'controller' => ['Api\CartController', 'remove'],
            'description' => 'Удалить из корзины',
        ],
        'cart.promocode' => [
            'method' => 'POST',
            'url' => '/api/cart/promocode',
            'controller' => ['Api\CartController', 'promocode'],
            'description' => 'Применить промокод',
        ],

        // Обратная связь (требует авторизации)
        'feedback.views' => [
            'method' => 'GET',
            'url' => '/api/feedback/views',
            'controller' => ['Api\FeedbackController', 'views'],
            'auth' => true,
            'description' => 'Увеличить просмотры',
        ],
        'feedback.likes' => [
            'method' => 'POST',
            'url' => '/api/feedback/likes',
            'controller' => ['Api\FeedbackController', 'likes'],
            'auth' => true,
            'description' => 'Лайк',
        ],
        'feedback.comment' => [
            'method' => 'POST',
            'url' => '/api/feedback/comment',
            'controller' => ['Api\FeedbackController', 'comment'],
            'auth' => true,
            'description' => 'Комментарий',
        ],
        'feedback.review' => [
            'method' => 'POST',
            'url' => '/api/feedback/review',
            'controller' => ['Api\FeedbackController', 'review'],
            'auth' => true,
            'description' => 'Отзыв',
        ],
        'feedback.favorite' => [
            'method' => 'POST',
            'url' => '/api/feedback/favorite',
            'controller' => ['Api\FeedbackController', 'favorite'],
            'auth' => true,
            'description' => 'Добавить в избранное',
        ],
        'feedback.follow' => [
            'method' => 'POST',
            'url' => '/api/feedback/follow',
            'controller' => ['Api\FeedbackController', 'follow'],
            'auth' => true,
            'description' => 'Подписаться',
        ],

        // Оплата
        'payment.confirm' => [
            'method' => 'POST',
            'url' => '/api/payment/confirm',
            'controller' => ['PaymentController', 'confirm'],
            'description' => 'Подтвердить оплату',
        ],
    ];

    /**
     * Маппинг всех Blade компонентов
     */
    private static array $components = [
        'form' => [
            'input' => 'components.form.input',
            'textarea' => 'components.form.textarea',
            'select' => 'components.form.select',
            'checkbox' => 'components.form.checkbox',
            'file' => 'components.form.file',
            'files' => 'components.form.files',
            'text-editor' => 'components.form.text-editor',
            'datepicker' => 'components.form.datepicker',
            'toggle' => 'components.form.toggle',
            'chips' => 'components.form.chips',
            'payment-method' => 'components.form.payment-method',
        ],
        'ui' => [
            'btn' => 'components.btn',
            'link' => 'components.link',
            'card' => 'components.card',
            'loader' => 'components.loader',
            'empty' => 'components.empty',
            'tooltip' => 'components.tooltip',
            'accordion' => 'components.accordion',
            'breadcrumbs' => 'components.breadcrumbs',
        ],
        'product' => [
            'slider' => 'components.product.slider',
        ],
        'profile' => [
            'section' => 'components.profile.section',
            'title' => 'components.profile.title',
            'wrap' => 'components.profile.wrap',
        ],
    ];

    // ========== МЕТОДЫ ДЛЯ ПОЛУЧЕНИЯ ДАННЫХ ==========

    /**
     * Получить информацию о странице
     */
    public static function getPage(string $key): ?array
    {
        return self::$pages[$key] ?? null;
    }

    /**
     * Получить роут страницы
     */
    public static function getRoute(string $key): ?string
    {
        return self::$pages[$key]['route'] ?? null;
    }

    /**
     * Получить URL страницы
     */
    public static function getUrl(string $key): ?string
    {
        return self::$pages[$key]['url'] ?? null;
    }

    /**
     * Получить контроллер и метод
     */
    public static function getController(string $key): ?array
    {
        return self::$pages[$key]['controller'] ?? null;
    }

    /**
     * Получить путь к view
     */
    public static function getView(string $key): ?string
    {
        return self::$pages[$key]['view'] ?? null;
    }

    /**
     * Получить Livewire компоненты страницы
     */
    public static function getLivewire(string $key): array
    {
        return self::$pages[$key]['livewire'] ?? [];
    }

    /**
     * Получить информацию о модальном окне
     */
    public static function getModal(string $key): ?array
    {
        return self::$modals[$key] ?? null;
    }

    /**
     * Получить информацию об API endpoint
     */
    public static function getApi(string $key): ?array
    {
        return self::$api[$key] ?? null;
    }

    /**
     * Получить путь к компоненту
     */
    public static function getComponent(string $category, string $name): ?string
    {
        return self::$components[$category][$name] ?? null;
    }

    /**
     * Получить все страницы
     */
    public static function getAllPages(): array
    {
        return self::$pages;
    }

    /**
     * Получить все модальные окна
     */
    public static function getAllModals(): array
    {
        return self::$modals;
    }

    /**
     * Получить все API endpoints
     */
    public static function getAllApi(): array
    {
        return self::$api;
    }

    /**
     * Найти страницу по роуту
     */
    public static function findByRoute(string $route): ?array
    {
        foreach (self::$pages as $key => $page) {
            if (isset($page['route']) && $page['route'] === $route) {
                return array_merge(['key' => $key], $page);
            }
        }
        return null;
    }

    /**
     * Найти страницу по view
     */
    public static function findByView(string $view): ?array
    {
        foreach (self::$pages as $key => $page) {
            if (isset($page['view']) && $page['view'] === $view) {
                return array_merge(['key' => $key], $page);
            }
        }
        return null;
    }
}
