<?php
namespace XYZSP\Views\PriceStrategy;


interface DiscountStrategyInterface
{
    /**
     * Calculate discounted price from base price and percent
     * @param float $price
     * @param float $percent
     * @return float
     */
    public function apply(float $price, float $percent): float;
}