<?php

namespace Bnix\PimcorePrestashopBundle\Prestashop;

final class PrestashopProductData
{
    public function __construct(
        public ?string $reference = null,
        public ?array $name = null,
        public ?array $description = null,
        public ?array $descriptionShort = null,
        public ?string $supplierReference = null,
        public ?float  $price = null,
        public ?string $image = null,
        public ?array  $images = [],
        public ?float  $width = null,
        public ?float  $height = null,
        public ?float  $depth = null,
        public ?float  $mass = null,
        public ?string $ean = null,
        public ?string $isbn = null,
        public ?string $upc = null,
        public ?string $mpn = null,
        public ?array $metaDescription = null,
        public ?array $metaTitle = null,
        public ?array $linkRewrite = null,
        public ?array $files = null,
        public ?array $parameters = null,
        public ?int $id_manufacturer = null,
        public ?int $id_supplier = null,
        public ?int $id_category_default = null,
        public ?bool $cache_default_attribute = null,
        public ?int $id_tax_rules_group = null,
        public ?string $type = null,
        public ?string $location = null,
        public ?int $quantity_discount = null,
        public ?string $additional_delivery_times = null,
        public ?string $delivery_in_stock = null,
        public ?string $delivery_out_of_stock = null,
        public ?bool $on_sale = null,
        public ?bool $online_only = null,
        public ?float $ecotax = null,
        public ?int $minimum_quantity = null,
        public ?int $low_stock_threshold = null,
        public ?int $low_stock_alert = null,
        public ?string $unity = null,
        public ?float $unit_price_ratio = null,
        public ?float $additional_shipping_cost = null,
        public ?bool $available_for_order = null,
        public ?bool $show_condition = null,
        public ?string $condition = null,
        public ?bool $show_price = null,
        public ?string $visibility = null,
        public ?string $available_date = null,
        public ?string $redirect_type = null,
        public ?int $id_type_redirected = null,
        public ?bool $indexed = null,
        public ?array $meta_keywords = null,
        public ?string $available_now = null,
        public ?string $available_later = null,
        public ?bool $advanced_stock_management = null,
        public ?int $stock_pack_type = null,
        public ?bool $active = null,
    )
    {
    }

    public function getHash(): string
    {
        return hash('sha256', json_encode($this));
    }
}
