<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Webservice;


use Bnix\PimcorePrestashopBundle\Config\PrestashopConfiguration;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use PrestashopClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
