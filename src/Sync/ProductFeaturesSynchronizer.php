<?php

namespace Bnix\PimcorePrestashopBundle\Sync;

use Bnix\PimcorePrestashopBundle\Entity\ExternalProductReference;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Storage\ExternalProductReferenceStorageInterface;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use Psr\Log\LoggerInterface;

class ProductFeaturesSynchronizer
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

        $product->parameters = array_diff_key($product->parameters, array_flip($store->getExcludedParameters()));

        $lastImageHash = $reference->getHash4();
        $currentHash = $this->getControlHash($product->parameters);

        if($lastImageHash == $currentHash && !$force)
        {
            $this->logger->info("Skipping files sync. Same hashes: $currentHash.");
            return;
        }

        $features = [];

        foreach($product->parameters as $parameter => $value)
        {
            $parameterId = $client->getPsFeatureId($parameter, $store->getLanguages());
            $valueId = $client->getPsFeatureValueId($parameterId, $value, $store->getLanguages());

            $features[$parameterId] = $valueId;
        }

        $client->updateProductFeatures((int)$reference->getExternalId(), $features);

        $reference->setHash4($currentHash);
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

    private function getControlHash(array $parameters): string
    {
        $merged = array_reduce(array_keys($parameters), function ($carry, $item) use ($parameters) {
            $carry .= "{$item}~{$parameters[$item]}~";
            return $carry;
        });

        return hash('sha256', $merged ?? "");
    }
}
