<?php
namespace XYZSP\Views\PriceStrategy;


class PercentDiscountStrategy implements DiscountStrategyInterface
{
    public function apply(float $price, float $percent): float
    {
        if ($percent <= 0)
            return $price;
        if ($percent > 100)
            $percent = 100;
        $discounted = $price * (1 - $percent / 100.0);
        return round(max(0.0, $discounted), wc_get_price_decimals());
    }
}