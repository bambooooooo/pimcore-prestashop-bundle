<?php

namespace Bnix\PimcorePrestashopBundle\Webservice;

use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;

interface PrestashopClientInterface
{
    public function createProduct(PrestashopProductData $product): int;

    public function updateProduct(PrestashopProductData $product, int $externalId);

    public function clearProductImages(int $externalId);

    public function uploadProductImage(int $externalId, string $imagePath);

    public function getProductIdByReference(string $reference, $referenceField = 'reference'): ?int;
}
