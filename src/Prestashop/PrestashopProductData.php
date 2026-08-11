<?php

namespace Bnix\PimcorePrestashopBundle\Prestashop;

final class PrestashopProductData
{
    public function __construct(
        public ?string $reference = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $supplierReference = null,
        public ?float $price = null,
        public ?string $image = null,
        public ?array $images = [],
        public ?float $Width = null,
        public ?float $Height = null,
        public ?float $Depth = null,
        public ?float $Mass = null,
    )
    {
    }
}
