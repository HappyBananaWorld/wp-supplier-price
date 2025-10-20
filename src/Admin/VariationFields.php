<?php
namespace XYZSP\Admin;


use XYZSP\Helpers\Constants;
use XYZSP\Helpers\RoleChecker;


class VariationFields
{
    public static function init(): void
    {
        add_action('woocommerce_product_after_variable_attributes', [self::class, 'render_variation_field'], 10, 3);
        add_action('woocommerce_save_product_variation', [self::class, 'save_variation_meta'], 10, 2);
    }


    public static function render_variation_field($loop, $variation_data, $variation): void
    {
        $value = get_post_meta($variation->ID, Constants::META_KEY, true);
        woocommerce_wp_text_input(array(
            'id' => Constants::META_KEY . "_{$loop}",
            'name' => Constants::META_KEY . "[{$loop}]",
            'value' => $value,
            'label' => __('درصد تخفیف تأمین‌کننده برای این واریاسیون (%)', 'xyz-supplier-discount'),
            'desc_tip' => true,
            'description' => __('مقدار بین 0 تا 100. خالی یعنی از والد استفاده شود.', 'xyz-supplier-discount'),
            'type' => 'number',
            'custom_attributes' => array('min' => 0, 'max' => 100, 'step' => '0.01'),
        ));
    }


    public static function save_variation_meta($variation_id, $i): void
    {
        if (!RoleChecker::current_user_can_manage())
            return;


        $key = Constants::META_KEY;
        if (isset($_POST[$key]) && is_array($_POST[$key]) && isset($_POST[$key][$i])) {
            $raw = wp_unslash($_POST[$key][$i]);
            $val = trim($raw);
            if ($val === '') {
                delete_post_meta($variation_id, $key);
                return;
            }
            $num = self::sanitize_percent($val);
            update_post_meta($variation_id, $key, $num);
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