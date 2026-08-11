<?php

namespace Bnix\PimcorePrestashopBundle\Prestashop;

final class PrestashopProductData
{
    public function __construct(
        public ?string $reference = null,
        public ?string $name = null,
        public ?string $supplierReference = null,
        public ?float $price = null,
        public array $images = [],
    )
    {
    }
}
