<?php

declare(strict_types=1);

namespace WebtronicIE\GiftCards;

use Igniter\Cart\Models\Order;
use Igniter\System\Classes\BaseExtension;
use WebtronicIE\GiftCards\CartConditions\RedeemGiftCard;
use WebtronicIE\GiftCards\Classes\Manager;
use WebtronicIE\GiftCards\Models\Settings;
use Illuminate\Support\Facades\Event;
use Override;

/**
 * GiftCard Extension Information File
 */
class Extension extends BaseExtension
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(Manager::class);
    }

    #[Override]
    public function boot(): void
    {
        Event::listen('igniter.cart.beforeApplyCoupon', function($code) {
            return resolve(Manager::class)->applyGiftCardCode($code);

        });

        Event::listen('igniter.checkout.beforePayment', function(Order $order, $data): void {
            resolve(Manager::class)->redeemGiftCard($order);

        });
    }

    #[Override]
    public function registerPermissions(): array
    {
        return [
            'WebtronicIE.GiftCards.ManageSettings' => [
                'description' => 'lang:webtronicie.giftcards::default.help_permission',
                'group' => 'igniter.cart::default.text_permission_order_group',
            ],
        ];
    }

    #[Override]
    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label' => 'lang:webtronicie.giftcards::default.text_settings',
                'description' => 'lang:webtronicie.giftcards::default.help_settings',
                'icon' => 'fa fa-gift',
                'model' => Settings::class,
                'permissions' => ['WebtronicIE.GiftCards.ManageSettings'],
            ],
        ];
    }



    public function registerCartConditions(): array
    {
        return [
            RedeemGiftCard::class => [
                'name' => 'giftcards',
                'label' => 'lang:webtronicie.giftcards::default.text_cart_condition',
                'description' => 'lang:webtronicie.giftcards::default.help_cart_condition',
            ],
        ];
    }
}
