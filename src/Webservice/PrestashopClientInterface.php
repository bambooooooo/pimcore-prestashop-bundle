<?php

namespace Bnix\PimcorePrestashopBundle\Webservice;

use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Webservice\Response\UploadAttachmentResponse;

interface PrestashopClientInterface
{
    public function createProduct(PrestashopProductData $product): int;

    public function updateProduct(PrestashopProductData $product, int $externalId);

    public function clearProductImages(int $externalId);

    public function clearProductAttachments(int $externalId);

    public function uploadProductImage(int $externalId, string $imagePath);

    public function getProductIdByReference(string $reference, $referenceField = 'reference'): ?int;

    public function uploadAttachment(string $filePath, string $name, string $mimeType): UploadAttachmentResponse;

    public function updateProductAttachment(UploadAttachmentResponse $data, array $name, string $filename, int $productId);
}
