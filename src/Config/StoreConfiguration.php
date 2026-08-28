<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Config;

use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopFieldDefinition;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopSchemaDefinition;

final class StoreConfiguration
{
    /**
     * @param string $name
     * @param string $url
     * @param string $host
     * @param string $apiKey
     * @param string[] $languages
     * @param string[] $currencies
     * @param array<string, MultiStore> $multistore
     * @param array<string, array<string,string>> $mappings
     * @param array $excludedParameters
     */
    public function __construct(
        private readonly string                      $name,
        private readonly string                      $url,
        private readonly string                      $host,
        private readonly string                      $apiKey,
        private readonly array                       $languages,
        private readonly array                       $currencies,
        private readonly array                       $multistore,
        private readonly array                       $mappings,
        private readonly array                       $excludedParameters
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
     * Default language id (first in the config)
     *
     * @return int
     */
    public function getDefaultLanguage(): int
    {
        return (int)$this->languages[array_key_first($this->languages)];
    }


    /**
     * @return string[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }


    /**
     * @return array<string, MultiStore>
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

    public function getExcludedParameters(): array
    {
        return $this->excludedParameters;
    }
}
