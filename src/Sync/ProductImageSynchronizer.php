<?php

namespace Bnix\PimcorePrestashopBundle\Sync;

use Bnix\PimcorePrestashopBundle\Entity\ExternalProductReference;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Repository\ExternalProductReferenceRepository;
use Bnix\PimcorePrestashopBundle\Storage\ExternalProductReferenceStorageInterface;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use http\Exception\RuntimeException;
use Pimcore\Model\Asset;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductImageSynchronizer
{
    public function __construct(private readonly PrestashopClientFactory $clientFactory,
                                private readonly ExternalProductReferenceStorageInterface $referenceStorage,
                                private readonly CacheInterface $cache)
    {

    }

    public function synchronize(ExternalProductReference $reference, PrestashopProductData $product, string $storeName, bool $force = false)
    {
        $client = $this->clientFactory->create($storeName);

        $lastImageHash = $reference->getHash2();
        $currentImageHash = $this->getControlHash($product->images);

        if($lastImageHash == $currentImageHash && !$force)
        {
            return;
        }

        $client->clearProductImages($reference->getExternalId());

        foreach ($product->images as $imId) {
            $tempFile = $this->getValidImageThumbnail($imId);
            $client->uploadProductImage($reference->getExternalId(), $tempFile);
        }

        $reference->setHash2($currentImageHash);
        $this->referenceStorage->saveReference($reference);
    }

    private function getControlHash(array $images): string
    {
        $merged = array_reduce($images, function ($carry, $item) {
            $carry .= Asset\Image::getById($item)->getCustomSetting('checksum');
            return $carry;
        });

        return hash('sha256', $merged ?? "");
    }

    private function getValidImageThumbnail(int $id): string
    {
        $cacheEntryName = "pimcore_prestashop_image_thumbnail_" . $id;

        return $this->cache->get($cacheEntryName, function (ItemInterface $cacheItem) use ($id) {
            $thumbFormat = ["webp_1400", "webp_1300", "webp_1200", "webp_1100"];

            foreach ($thumbFormat as $format) {
                $im = Asset\Image::getById($id);
                $image = $im->getThumbnail("$format");
                $stream = $image->getStream();

                $tempFile = tempnam(sys_get_temp_dir(), 'pim_image_') . ".webp";
                file_put_contents($tempFile, stream_get_contents($stream));

                $size = filesize($tempFile);

                if($size < 2_000_000)
                {
                    $cacheItem->expiresAfter(3600 * 24 * 30); // 30 days
                    return $tempFile;
                }
            }

            throw new PrestashopException("No valid image thumbnail found for image id=$id that is smaller than 2MB");
        });
    }
}
