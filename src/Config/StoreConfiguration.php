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
        private readonly string $name,
        private readonly string $url,
        private readonly string $host,
        private readonly string $apiKey,
        private readonly array  $languages,
        private readonly array  $currencies,
        private readonly array  $multistore,
        private readonly array  $mappings,
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
     * @return array<string, int>
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * Default language (first in the config)
     *
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        return $this->languages[0];
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
