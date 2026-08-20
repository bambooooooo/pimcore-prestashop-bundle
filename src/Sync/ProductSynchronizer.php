<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Sync;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use Bnix\PimcorePrestashopBundle\Entity\ExternalProductReference;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopNotFoundException;
use Bnix\PimcorePrestashopBundle\ExportPolicy\ExportPolicyInterface;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Prestashop\ProductMapper;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Storage\ExternalProductReferenceStorageInterface;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\Note;
use Psr\Log\LoggerInterface;

class ProductSynchronizer
{
    public function __construct(private readonly ExternalProductReferenceStorageInterface $productReferenceStorage,
                                private readonly PrestashopClientFactory                  $clientFactory,
                                private readonly StoreRegistry                            $storeRegistry,
                                private readonly ProductMapper                            $productMapper,
                                private readonly ProductImageSynchronizer                 $productImageSynchronizer,
                                private readonly ExportPolicyInterface                    $exportPolicy,
                                private readonly LoggerInterface                          $logger,)
    {}

    public function synchronize(int $objectId, string $storeName, bool $force = false): void
    {
        $obj = DataObject::getById($objectId);

        if(!$this->exportPolicy->supports($obj))
        {
            return;
        }

        $store = $this->storeRegistry->get($storeName);
        $product = $this->productMapper->map($obj, $store);
        $hash = $product->getHash();
        $client = $this->clientFactory->create($storeName);

        try
        {
            $externalReference = $this->synchronizeProduct($obj, $store, $hash, $client, $product, $force);
            $this->productImageSynchronizer->synchronize($externalReference, $product, $storeName, $force);
        }
        catch (PrestashopException $exception)
        {
            $this->createAndSaveErrorNote($obj, $storeName, $exception->getMessage());
            $this->logger->error("[$storeName] {$exception->getMessage()}");
        }
    }

    private function synchronizeProduct(DataObject $obj,
                                        StoreConfiguration $store,
                                        string $hash,
                                        PrestashopClientInterface $storeClient,
                                        PrestashopProductData $prestashopProduct,
                                        bool $force): ExternalProductReference
    {
        $externalReference = $this->productReferenceStorage->find($obj->getId(), $store->getName());

        if($externalReference !== null)
        {
            $this->logger->notice("Updating #{$obj->getId()} with external id={$externalReference->getExternalId()}");
            return $this->updateExistingProduct($externalReference, $obj, $store, $hash, $storeClient, $prestashopProduct, $force);
        }

        return $this->createOrAttachProduct($obj, $store, $storeClient, $prestashopProduct);
    }

    private function updateExistingProduct(ExternalProductReference  $reference,
                                           DataObject $obj,
                                           StoreConfiguration $store,
                                           string $hash,
                                           PrestashopClientInterface $client,
                                           PrestashopProductData     $product,
                                           bool $force): ExternalProductReference
    {
        $hashChanged = $reference->getHash() !== $hash;

        if(!$force && !$hashChanged)
        {
            return $reference;
        }

        try
        {
            $client->updateProduct($product, (int)$reference->getExternalId());
        }
        catch (PrestashopNotFoundException)
        {
            $this->productReferenceStorage->delete($obj->getId(), $store->getName());

            return $this->createProduct($obj, $store, $client, $product);
        }

        $reference->setHash($hash);
        $this->productReferenceStorage->saveReference($reference);

        return $reference;
    }

    private function createOrAttachProduct(DataObject $obj,
                                           StoreConfiguration $store,
                                           PrestashopClientInterface $client,
                                           PrestashopProductData $product): ExternalProductReference
    {
        $existingId = $client->getProductIdByReference($product->reference);

        if($existingId !== null)
        {
            $reference = new ExternalProductReference(
                $obj->getId(),
                $store->getName(),
                (string) $existingId,
                $product->getHash(),
            );

            $client->updateProduct(
                $product,
                $existingId,
            );

            $this->productReferenceStorage->saveReference($reference);

            $this->createAndSaveSuccessNote($obj, $store->getName(), "Product updated with id=$existingId");

            return $reference;
        }

        return $this->createProduct($obj, $store, $client, $product);
    }

    private function createProduct(DataObject $obj,
                                   StoreConfiguration $store,
                                   PrestashopClientInterface $client,
                                   PrestashopProductData $product): ExternalProductReference
    {
        $id = (string)$client->createProduct($product);

        $reference = new ExternalProductReference(
            $obj->getId(),
            $store->getName(),
            $id,
            $product->getHash(),
        );

        $this->productReferenceStorage->saveReference($reference);

        $this->createAndSaveSuccessNote($obj, $store->getName(), "Product created with id=$id");

        return $reference;
    }

    private function createAndSaveSuccessNote(DataObject $obj, string $storeName, string $message): void
    {
        $note = new Note();
        $note->setElement($obj);
        $note->setDate(time());
        $note->setType('success');
        $note->setTitle("[prestashop][$storeName] ok");
        $note->setDescription($message);
        $note->setUser(0);

        $note->save();
    }

    private function createAndSaveErrorNote(DataObject $obj, string $storeName, string $message): void
    {
        $note = new Note();
        $note->setElement($obj);
        $note->setDate(time());
        $note->setType('error');
        $note->setTitle("$storeName: error");
        $note->setDescription($message);
        $note->setUser(0);

        $note->save();
    }
}
