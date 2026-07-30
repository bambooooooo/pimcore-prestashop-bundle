<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Config;

final class StoreConfiguration
{
    /**
     * @param string[] $languages
     * @param string[] $currencies
     * @param array<string, array{
     *     shop_id:int,
     *     shop_group_id:int
     * }> $multistore
     * @param array<string, array<string,string>> $mappings
     */
    public function __construct(
        private string $name,
        private string $url,
        private string $host,
        private string $apiKey,
        private array $languages,
        private array $currencies,
        private array $multistore,
        private array $mappings,
    ) {
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHost(): string
    {
        return $this->host;
    }


    public function getApiKey(): string
    {
        return $this->apiKey;
    }


    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }


    /**
     * @return string[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }


    /**
     * @return array<string,array{
     *     shop_id:int,
     *     shop_group_id:int
     * }>
     */
    public function getMultistore(): array
    {
        return $this->multistore;
    }


    /**
     * @return array<string,array<string,string>>
     */
    public function getMappings(): array
    {
        return $this->mappings;
    }
}
