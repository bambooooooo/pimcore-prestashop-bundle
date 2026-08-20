<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Message;

final class PrestashopProductSyncMessage
{
    public function __construct(public int $productId, public string $store, public bool $force = false)
    {

    }
}
