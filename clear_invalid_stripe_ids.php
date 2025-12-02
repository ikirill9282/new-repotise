<?php

/**
 * Скрипт для очистки невалидных stripe_id у пользователей
 * Запуск: php clear_invalid_stripe_ids.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Log;

echo "Начинаем проверку stripe_id пользователей...\n";

$total = User::whereNotNull('stripe_id')->count();
echo "Найдено пользователей с stripe_id: {$total}\n\n";

$cleared = 0;
$valid = 0;
$errors = 0;

User::whereNotNull('stripe_id')->chunk(100, function ($users) use (&$cleared, &$valid, &$errors) {
    foreach ($users as $user) {
        try {
            // Пытаемся получить клиента из Stripe
            Cashier::stripe()->customers->retrieve($user->stripe_id);
            $valid++;
            echo "✓ User #{$user->id}: stripe_id валиден ({$user->stripe_id})\n";
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            if (str_contains($e->getMessage(), 'No such customer')) {
                // Клиент не найден - очищаем stripe_id
                $oldStripeId = $user->stripe_id;
                $user->update(['stripe_id' => null]);
                $cleared++;
                echo "✗ User #{$user->id}: очищен невалидный stripe_id ({$oldStripeId})\n";
            } else {
                $errors++;
                echo "⚠ User #{$user->id}: ошибка при проверке - {$e->getMessage()}\n";
            }
        } catch (\Exception $e) {
            $errors++;
            echo "⚠ User #{$user->id}: неожиданная ошибка - {$e->getMessage()}\n";
        }
    }
});

echo "\n=== Результаты ===\n";
echo "Валидных stripe_id: {$valid}\n";
echo "Очищено невалидных stripe_id: {$cleared}\n";
echo "Ошибок: {$errors}\n";
echo "\nГотово!\n";

