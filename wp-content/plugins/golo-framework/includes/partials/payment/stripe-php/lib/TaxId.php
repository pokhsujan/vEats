<?php

namespace MyStripe;

/**
 * You can add one or multiple tax IDs to a <a
 * href="https://stripe.com/docs/api/customers">customer</a>. A customer's tax IDs
 * are displayed on invoices and credit notes issued for the customer.
 *
 * Related guide: <a href="https://stripe.com/docs/billing/taxes/tax-ids">Customer
 * Tax Identification Numbers</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|string $country Two-letter ISO code representing the country of the tax ID.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string|\MyStripe\Customer $customer ID of the customer.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property string $type Type of the tax ID, one of <code>au_abn</code>, <code>ca_bn</code>, <code>ca_qst</code>, <code>ch_vat</code>, <code>es_cif</code>, <code>eu_vat</code>, <code>hk_br</code>, <code>in_gst</code>, <code>jp_cn</code>, <code>kr_brn</code>, <code>li_uid</code>, <code>mx_rfc</code>, <code>my_itn</code>, <code>my_sst</code>, <code>no_vat</code>, <code>nz_gst</code>, <code>ru_inn</code>, <code>sg_uen</code>, <code>th_vat</code>, <code>tw_vat</code>, <code>us_ein</code>, or <code>za_vat</code>. Note that some legacy tax IDs have type <code>unknown</code>
 * @property string $value Value of the tax ID.
 * @property \MyStripe\StripeObject $verification
 */
class TaxId extends ApiResource
{
    const OBJECT_NAME = 'tax_id';

    use ApiOperations\Delete;

    const TYPE_AU_ABN = 'au_abn';
    const TYPE_CA_BN = 'ca_bn';
    const TYPE_CA_QST = 'ca_qst';
    const TYPE_CH_VAT = 'ch_vat';
    const TYPE_ES_CIF = 'es_cif';
    const TYPE_EU_VAT = 'eu_vat';
    const TYPE_HK_BR = 'hk_br';
    const TYPE_IN_GST = 'in_gst';
    const TYPE_JP_CN = 'jp_cn';
    const TYPE_KR_BRN = 'kr_brn';
    const TYPE_LI_UID = 'li_uid';
    const TYPE_MX_RFC = 'mx_rfc';
    const TYPE_MY_ITN = 'my_itn';
    const TYPE_MY_SST = 'my_sst';
    const TYPE_NO_VAT = 'no_vat';
    const TYPE_NZ_GST = 'nz_gst';
    const TYPE_RU_INN = 'ru_inn';
    const TYPE_SG_UEN = 'sg_uen';
    const TYPE_TH_VAT = 'th_vat';
    const TYPE_TW_VAT = 'tw_vat';
    const TYPE_UNKNOWN = 'unknown';
    const TYPE_US_EIN = 'us_ein';
    const TYPE_ZA_VAT = 'za_vat';

    const VERIFICATION_STATUS_PENDING = 'pending';
    const VERIFICATION_STATUS_UNAVAILABLE = 'unavailable';
    const VERIFICATION_STATUS_UNVERIFIED = 'unverified';
    const VERIFICATION_STATUS_VERIFIED = 'verified';

    /**
     * @return string the API URL for this tax id
     */
    public function instanceUrl()
    {
        $id = $this['id'];
        $customer = $this['customer'];
        if (!$id) {
            throw new Exception\UnexpectedValueException(
                "Could not determine which URL to request: class instance has invalid ID: {$id}"
            );
        }
        $id = Util\Util::utf8($id);
        $customer = Util\Util::utf8($customer);

        $base = Customer::classUrl();
        $customerExtn = \urlencode($customer);
        $extn = \urlencode($id);

        return "{$base}/{$customerExtn}/tax_ids/{$extn}";
    }

    /**
     * @param array|string $_id
     * @param null|array|string $_opts
     *
     * @throws \MyStripe\Exception\BadMethodCallException
     */
    public static function retrieve($_id, $_opts = null)
    {
        $msg = 'Tax IDs cannot be retrieved without a customer ID. Retrieve ' .
               "a tax ID using `Customer::retrieveTaxId('customer_id', " .
               "'tax_id_id')`.";

        throw new Exception\BadMethodCallException($msg);
    }
}
