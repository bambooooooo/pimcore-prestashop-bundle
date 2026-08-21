<?php

namespace Bnix\PimcorePrestashopBundle\Sync;

use Bnix\PimcorePrestashopBundle\Entity\ExternalProductReference;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Storage\ExternalProductReferenceStorageInterface;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use Pimcore\Model\Asset;
use Psr\Log\LoggerInterface;

class ProductFileSynchronizer
{
    public function __construct(private readonly PrestashopClientFactory                  $clientFactory,
                                private readonly ExternalProductReferenceStorageInterface $referenceStorage,
                                private readonly StoreRegistry                            $storeRegistry,
                                private readonly LoggerInterface $logger)
    {

    }

    public function synchronize(ExternalProductReference $reference, PrestashopProductData $product, string $storeName, bool $force = false)
    {
        $client = $this->clientFactory->create($storeName);
        $store = $this->storeRegistry->get($storeName);

        $lastImageHash = $reference->getHash3();
        $currentHash = $this->getControlHash($product->files);

        if($lastImageHash == $currentHash && !$force)
        {
            $this->logger->info("Skipping files sync. Same hashes: $currentHash.");
            return;
        }

        $client->clearProductAttachments((int)$reference->getExternalId());

        foreach ($product->files as $fileId)
        {
            $asset = Asset::getById($fileId);
            $stream = $asset->getStream();

            $tempFile = tempnam(sys_get_temp_dir(), 'ps_attachment_');
            file_put_contents($tempFile, stream_get_contents($stream));

            $fileData = $client->uploadAttachment($tempFile, $asset->getFilename(), $asset->getMimeType());
            $client->updateProductAttachment($fileData, $this->asLocalizedString($asset->getFilename(), $store->getLanguages()), $asset->getKey(), (int)$reference->getExternalId());
        }

        $reference->setHash3($currentHash);
        $this->referenceStorage->saveReference($reference);
    }

    private function asLocalizedString(string $string, array $languages): array
    {
        $ret = [];
        foreach ($languages as $lang => $langId) {
            $ret[$langId] = $string;
        }
        return $ret;
    }

    private function getControlHash(array $images): string
    {
        $merged = array_reduce($images, function ($carry, $item) {
            $carry .= Asset::getById($item)?->getCustomSetting('checksum');
            return $carry;
        });

        return hash('sha256', $merged ?? "");
    }
}
