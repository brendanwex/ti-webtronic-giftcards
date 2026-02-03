<?php

declare(strict_types=1);

namespace WebtronicIE\GiftCards\Classes;

use Exception;
use Igniter\Cart\Facades\Cart;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Exception\ApplicationException;
use WebtronicIE\GiftCards\Models\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Manager
{

    protected static $responseCache = [];

    public function applyGiftCardCode(string $code)
    {
        try {
            if (!$condition = Cart::getCondition('giftcards')) {
                return null;
            }

            // Get gift card by code
            $giftCardObj = $this->fetchGiftCard($code);

            $this->validateGiftCard($giftCardObj);

            $condition->setMetaData(['code' => $code]);

            Cart::loadCondition($condition);

            return $condition;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }

        return null;
    }

    public function validateGiftCard($giftCard): void
    {




        if ($giftCard->status !== 'success') {
            throw new ApplicationException(lang('webtronicie.giftcards::default.alert_gift_card_redeemed'));
        }

        if ($giftCard->balances->current_balance <= Settings::getMinimumValue()) {
            throw new ApplicationException(lang('webtronicie.giftcards::default.alert_gift_card_balance_low'));
        }
    }

    public function redeemGiftCard(Order $order): void
    {
        if (!$condition = Cart::conditions()->get('giftcards')) {
            return;
        }

        if ((string)$condition->getMetaData('code') === '') {
            return;
        }

        if ($order->isPaymentProcessed()) {
            throw new ApplicationException(lang('webtronicie.giftcards::default.alert_order_not_processed'));
        }

        $payload = [
            'amount' => abs($condition->getValue()),
            'api_key' => null,
            'code' => $condition->getMetaData('code')
        ];

        $response = $this->sendRequest('POST', 'redeem_voucher', $payload);

        if ($order->payment_method) {
            $order->logPaymentAttempt('Gift card redeemed successful', 1, $payload, $response);
        }
    }



    public function fetchGiftCard(string $code)
    {
        if (array_key_exists($code, self::$responseCache)) {
            return self::$responseCache[$code];
        }
        $apiKey = Settings::getApiKey();

        return self::$responseCache[$code] = (object)$this->sendRequest('POST', 'check_balance/', ['code' => $code, 'api_key' => $apiKey]);
    }

    public function clearInternalCache(): void
    {
        self::$responseCache = [];
    }

    protected function sendRequest(string $method, string $uri, array $payload = [])
    {
        try {
            $endpoint = Settings::getApiEndPoint();
            $apiKey = Settings::getApiKey();
            if ($apiKey === '') {
                throw new Exception(lang('webtronicie.giftcards::default.alert_missing_api_key'));
            }

            $headers = [
                'Content-Type' => 'application/json',
            ];



            $request = Http::withHeaders($headers)->send($method, $endpoint.'/'.$uri, $payload);

            if (!$request->ok()) {
                throw new ApplicationException('Error while communicating with the gift card server '.json_encode($request->json()));
            }

            return $request->json();
        } catch (Exception $ex) {
            logger()->error($ex);

            throw $ex;
        }
    }
}
