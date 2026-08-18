<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Sync;

use Bnix\PimcorePrestashopBundle\Event\PrestashopPreSendEvent;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\ExportPolicy\ExportPolicyInterface;
use Bnix\PimcorePrestashopBundle\Prestashop\ProductMapper;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Storage\ExternalProductReferenceStorageInterface;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\Note;

class ProductSynchronizer
{
    public function __construct(private readonly ExternalProductReferenceStorageInterface $productReferenceStorage,
                                private readonly PrestashopClientFactory                  $clientFactory,
                                private readonly StoreRegistry                            $storeRegistry,
                                private readonly ProductMapper                            $productMapper,
                                private readonly ExportPolicyInterface                    $exportPolicy)
    {}

    public function synchronize(int $objectId, string $storeName): void
    {
        $store = $this->storeRegistry->get($storeName);
        $systemName = 'prestashop_' . $store->getName();
        $obj = DataObject::getById($objectId);

        $supported = $this->exportPolicy->supports($obj);

        if(!$supported)
        {
            return;
        }

        $prestashopProduct = $this->productMapper->map($obj, $store);
        $hash = $prestashopProduct->getHash();

        $externalReference = $this->productReferenceStorage->find($objectId, $systemName);
        $storeClient = $this->clientFactory->create($storeName);

        if($externalReference === null)
        {
            try
            {
                $id = (string)$storeClient->createProduct($prestashopProduct);

                $this->productReferenceStorage->save($objectId, $systemName, $id, $prestashopProduct->getHash());
                $this->createAndSaveSuccessNote($obj, $storeName, "Product created with id=$id");
            }
            catch (PrestashopException $exception)
            {
                $this->createAndSaveErrorNote($obj, $storeName, $exception->getMessage());
            }
        }
        else
        {
            $extId = $externalReference->getExternalId();

            if($hash == $prestashopProduct->getHash())
            {
                return;
            }

            $storeClient->updateProduct($prestashopProduct, (int)$extId);

            $this->productReferenceStorage->save($objectId, $systemName, $extId, $hash);
        }
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
