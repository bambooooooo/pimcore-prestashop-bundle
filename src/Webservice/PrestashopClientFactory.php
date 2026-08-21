<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Webservice;


use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Xml\AttachmentXmlBuilder;
use Bnix\PimcorePrestashopBundle\Xml\ProductXmlBuilder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PrestashopClientFactory
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly StoreRegistry $stores,
        private readonly ProductXmlBuilder $productXmlBuilder,
        private readonly AttachmentXmlBuilder $attachmentXmlBuilder,
    ) {
    }

    public function create(string $store): PrestashopClientInterface
    {
        return new PrestashopClient(
            $this->httpClient,
            $this->stores->get($store),
            $this->productXmlBuilder,
            $this->attachmentXmlBuilder,
        );
    }
}
