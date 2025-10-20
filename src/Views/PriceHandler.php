<?php
namespace XYZSP\Views;

use XYZSP\Helpers\Constants;
use XYZSP\Helpers\RoleChecker;
use XYZSP\Views\PriceStrategy\PercentDiscountStrategy;

class PriceHandler
{
    private static $strategy;

    public static function init(): void
    {
        self::$strategy = new PercentDiscountStrategy();

        add_filter('woocommerce_product_get_price', [self::class, 'apply_supplier_discount'], 10, 2);
        add_filter('woocommerce_product_get_regular_price', [self::class, 'apply_supplier_discount'], 10, 2);
        add_filter('woocommerce_product_get_sale_price', [self::class, 'apply_supplier_discount'], 10, 2);

        add_filter('woocommerce_get_price_html', [self::class, 'render_price_html'], 10, 2);
    }

    public static function apply_supplier_discount($price, $product)
    {
        if (!RoleChecker::current_user_is_supplier())
            return $price;
        if ($price === '' || $price === null)
            return $price;

        $percent = self::get_percent_for_product($product);
        if ($percent <= 0)
            return $price;

        return self::$strategy->apply((float) $price, (float) $percent);
    }

    private static function get_percent_for_product($product): float
    {
        if (!$product)
            return 0.0;

        // variation first
        if (method_exists($product, 'is_type') && $product->is_type('variation')) {
            $meta = get_post_meta($product->get_id(), Constants::META_KEY, true);
            if ($meta !== '' && $meta !== false)
                return (float) $meta;

            $parent_id = $product->get_parent_id();
            if ($parent_id) {
                $parent_meta = get_post_meta($parent_id, Constants::META_KEY, true);
                if ($parent_meta !== '' && $parent_meta !== false)
                    return (float) $parent_meta;
            }
        }

        // simple
        $meta = get_post_meta($product->get_id(), Constants::META_KEY, true);
        if ($meta !== '' && $meta !== false)
            return (float) $meta;

        return 0.0;
    }

    public static function render_price_html($price_html, $product)
    {
        if (!RoleChecker::current_user_is_supplier())
            return $price_html;

        $percent = self::get_percent_for_product($product);
        if ($percent <= 0)
            return $price_html;

        $regular = $product->get_regular_price();
        $discounted = $product->get_price();

        if ($regular === '' || (float) $regular <= 0)
            return $price_html;

        if ((float) $regular == (float) $discounted) {
            return wc_price($discounted);
        }

        return '<del>' . wc_price($regular) . '</del> <ins>' . wc_price($discounted) . '</ins>';
    }
}