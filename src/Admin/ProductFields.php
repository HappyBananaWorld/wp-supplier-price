<?php

namespace XYZSP\Admin;
use XYZSP\Helpers\Constants;
use XYZSP\Helpers\RoleChecker;

class ProductFields
{
    public static function init(): void
    {
        add_action('woocommerce_product_options_pricing', [self::class, 'render_price_field']);
        add_action('woocommerce_process_product_meta', [self::class, 'save_product_meta']);
    }


    public static function render_price_field(): void
    {
        // show field
        woocommerce_wp_text_input(array(
            'id' => Constants::META_KEY,
            'label' => __('درصد تخفیف تأمین‌کننده (%)', 'xyz-supplier-discount'),
            'desc_tip' => true,
            'description' => __('مقدار عددی بین 0 تا 100. خالی یعنی غیرفعال.', 'xyz-supplier-discount'),
            'type' => 'number',
            'custom_attributes' => array('min' => 0, 'max' => 100, 'step' => '0.01'),
        ));
    }


    public static function save_product_meta($post_id): void
    {
        // capability check
        if (!RoleChecker::current_user_can_manage())
            return;


        if (isset($_POST[Constants::META_KEY])) {
            $raw = wp_unslash($_POST[Constants::META_KEY]);
            $val = trim($raw);
            if ($val === '') {
                delete_post_meta($post_id, Constants::META_KEY);
                return;
            }
            $num = self::sanitize_percent($val);
            update_post_meta($post_id, Constants::META_KEY, $num);
        }
    }


    private static function sanitize_percent($value): float
    {
        $num = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($num === false)
            $num = 0.0;
        if ($num < 0)
            $num = 0;
        if ($num > 100)
            $num = 100;
        return round((float) $num, 2);
    }
}