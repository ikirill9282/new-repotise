<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ModalsAdditionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Backup::class)
            ->assertSuccessful();
    }

    public function test_backup_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\BackupAccept::class)
            ->assertSuccessful();
    }

    public function test_cancelsub_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Cancelsub::class)
            ->assertSuccessful();
    }

    public function test_cancelsub_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\CancelsubAccept::class)
            ->assertSuccessful();
    }

    public function test_change_email_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\ChangeEmailAccept::class)
            ->assertSuccessful();
    }

    public function test_contact_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\Contact::class)
            ->assertSuccessful();
    }

    public function test_creator_plus_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\CreatorPlus::class)
            ->assertSuccessful();
    }

    public function test_delete_account_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DeleteAccountAccept::class)
            ->assertSuccessful();
    }

    public function test_delete_product_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DeleteProduct::class, ['product' => $product])
            ->assertSuccessful();
    }

    public function test_delete_product_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DeleteProductAccept::class, ['product' => $product])
            ->assertSuccessful();
    }

    public function test_delete_subscription_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DeleteSubscription::class)
            ->assertSuccessful();
    }

    public function test_delete_subscription_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DeleteSubscriptionAccept::class)
            ->assertSuccessful();
    }

    public function test_donate_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DonateAccept::class, ['product' => $product])
            ->assertSuccessful();
    }

    public function test_donate_error_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\DonateError::class)
            ->assertSuccessful();
    }

    public function test_donate_sub_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\DonateSubAccept::class)
            ->assertSuccessful();
    }

    public function test_edit_contacts_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\EditContacts::class)
            ->assertSuccessful();
    }

    public function test_file_description_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\FileDescription::class)
            ->assertSuccessful();
    }

    public function test_funds_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Funds::class)
            ->assertSuccessful();
    }

    public function test_funds_error_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\FundsError::class)
            ->assertSuccessful();
    }

    public function test_funds_success_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\FundsSuccess::class)
            ->assertSuccessful();
    }

    public function test_levels_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Levels::class)
            ->assertSuccessful();
    }

    public function test_message_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Message::class)
            ->assertSuccessful();
    }

    public function test_order_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Order::class, ['order' => $order])
            ->assertSuccessful();
    }

    public function test_payout_details_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\PayoutDetails::class)
            ->assertSuccessful();
    }

    public function test_payout_method_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\PayoutMethod::class)
            ->assertSuccessful();
    }

    public function test_refund_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Refund::class, ['order' => $order])
            ->assertSuccessful();
    }

    public function test_refund_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\RefundAccept::class, ['order' => $order])
            ->assertSuccessful();
    }

    public function test_register_success_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\RegisterSuccess::class)
            ->assertSuccessful();
    }

    public function test_report_error_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\ReportError::class)
            ->assertSuccessful();
    }

    public function test_report_success_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\ReportSuccess::class)
            ->assertSuccessful();
    }

    public function test_reset_password_success_modal_can_be_rendered(): void
    {
        Livewire::test(\App\Livewire\Modals\ResetPasswordSuccess::class)
            ->assertSuccessful();
    }

    public function test_social_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Social::class)
            ->assertSuccessful();
    }

    public function test_subscription_product_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'subscription' => true,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\SubscriptionProduct::class, ['product' => $product])
            ->assertSuccessful();
    }

    public function test_transaction_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Transaction::class)
            ->assertSuccessful();
    }

    public function test_twofa_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\TwofaAccept::class)
            ->assertSuccessful();
    }

    public function test_twofa_disable_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\TwofaDisable::class)
            ->assertSuccessful();
    }

    public function test_twofa_disable_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\TwofaDisableAccept::class)
            ->assertSuccessful();
    }

    public function test_withdraw_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Withdraw::class)
            ->assertSuccessful();
    }

    public function test_withdraw_accept_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();
        
        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\WithdrawAccept::class)
            ->assertSuccessful();
    }
}
