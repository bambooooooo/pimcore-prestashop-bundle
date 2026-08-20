<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\MessageHandler;

use Bnix\PimcorePrestashopBundle\Message\PrestashopProductSyncMessage;
use Bnix\PimcorePrestashopBundle\Sync\ProductSynchronizer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProductSyncMessageHandler
{
    public function __construct(private readonly ProductSynchronizer $synchronizer)
    {

    }
    public function __invoke(PrestashopProductSyncMessage $message): void
    {
        $this->synchronizer->synchronize($message->productId, $message->store, $message->force);
    }
}
