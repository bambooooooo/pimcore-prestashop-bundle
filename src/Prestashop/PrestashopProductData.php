<?php

namespace Bnix\PimcorePrestashopBundle\Prestashop;

final class PrestashopProductData
{
    public function __construct(
        public ?string $referencePrefix = null,
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
    )
    {
    }

    public function getHash(): string
    {
        return hash('sha256', json_encode($this));
    }
}
