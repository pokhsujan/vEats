<?php

namespace MyStripe\Issuing;

/**
 * When an <a href="https://stripe.com/docs/issuing">issued card</a> is used to
 * make a purchase, an Issuing <code>Authorization</code> object is created. <a
 * href="https://stripe.com/docs/issuing/purchases/authorizations">Authorizations</a>
 * must be approved for the purchase to be completed successfully.
 *
 * Related guide: <a
 * href="https://stripe.com/docs/issuing/purchases/authorizations">Issued Card
 * Authorizations</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $amount The total amount in the card's currency that was authorized or rejected.
 * @property bool $approved Whether the authorization has been approved.
 * @property string $authorization_method How the card details were provided.
 * @property int $authorized_amount The amount that has been authorized. This will be <code>0</code> when the object is created, and increase after it has been approved.
 * @property string $authorized_currency The currency that was presented to the cardholder for the authorization. Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property \MyStripe\BalanceTransaction[] $balance_transactions List of balance transactions associated with this authorization.
 * @property \MyStripe\Issuing\Card $card You can <a href="https://stripe.com/docs/issuing/cards">create physical or virtual cards</a> that are issued to cardholders.
 * @property null|string|\MyStripe\Issuing\Cardholder $cardholder The cardholder to whom this authorization belongs.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property int $held_amount The amount the authorization is expected to be in <code>held_currency</code>. When Stripe holds funds from you, this is the amount reserved for the authorization. This will be <code>0</code> when the object is created, and increase after it has been approved. For multi-currency transactions, <code>held_amount</code> can be used to determine the expected exchange rate.
 * @property string $held_currency The currency of the <a href="https://stripe.com/docs/api#issuing_authorization_object-held_amount">held amount</a>. This will always be the card currency.
 * @property bool $is_held_amount_controllable If set <code>true</code>, you may provide <a href="https://stripe.com/docs/api/issuing/authorizations/approve#approve_issuing_authorization-held_amount">held_amount</a> to control how much to hold for the authorization.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property int $merchant_amount The total amount that was authorized or rejected in the local merchant_currency.
 * @property string $merchant_currency The currency that was presented to the cardholder for the authorization. Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property \MyStripe\StripeObject $merchant_data
 * @property \MyStripe\StripeObject $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property int $pending_authorized_amount The amount the user is requesting to be authorized. This field will only be non-zero during an <code>issuing.authorization.request</code> webhook.
 * @property int $pending_held_amount The additional amount Stripe will hold if the authorization is approved. This field will only be non-zero during an <code>issuing.authorization.request</code> webhook.
 * @property null|\MyStripe\StripeObject $pending_request The pending authorization request. This field will only be non-null during an <code>issuing.authorization.request</code> webhook.
 * @property \MyStripe\StripeObject[] $request_history History of every time the authorization was approved/denied (whether approved/denied by you directly, or by Stripe based on your authorization_controls). If the merchant changes the authorization by performing an <a href="https://stripe.com/docs/issuing/purchases/authorizations">incremental authorization or partial capture</a>, you can look at request_history to see the previous states of the authorization.
 * @property string $status The current status of the authorization in its lifecycle.
 * @property \MyStripe\Issuing\Transaction[] $transactions List of <a href="https://stripe.com/docs/api/issuing/transactions">transactions</a> associated with this authorization.
 * @property \MyStripe\StripeObject $verification_data
 * @property null|string $wallet What, if any, digital wallet was used for this authorization. One of <code>apple_pay</code>, <code>google_pay</code>, or <code>samsung_pay</code>.
 * @property null|string $wallet_provider [DEPRECATED] What, if any, digital wallet was used for this authorization. One of <code>apple_pay</code>, <code>google_pay</code>, or <code>samsung_pay</code>.
 */
class Authorization extends \MyStripe\ApiResource
{
    const OBJECT_NAME = 'issuing.authorization';

    use \MyStripe\ApiOperations\All;
    use \MyStripe\ApiOperations\Retrieve;
    use \MyStripe\ApiOperations\Update;

    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @throws \MyStripe\Exception\ApiErrorException if the request fails
     *
     * @return Authorization the approved authorization
     */
    public function approve($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/approve';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @throws \MyStripe\Exception\ApiErrorException if the request fails
     *
     * @return Authorization the declined authorization
     */
    public function decline($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/decline';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
