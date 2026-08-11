<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Webservice;


use Symfony\Contracts\HttpClient\HttpClientInterface;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientInterface;

final class PrestashopClientFactory
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly StoreRegistry $stores
    ) {
    }


    public function create(string $store): PrestashopClientInterface
    {
        return new PrestashopClient(
            $this->httpClient,
            $this->stores->get($store)
        );
    }
}
