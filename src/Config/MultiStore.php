<?php

namespace Bnix\PimcorePrestashopBundle\Config;

class MultiStore
{
    /**
     * @param int $id
     * @param string $name
     * @param array $languages
     * @param array[] $mappings
     */
    public function __construct(public readonly int    $id,
                                public readonly string $name,
                                public readonly array  $languages,
                                public readonly array  $mappings = [])
    {

    }

    public function getMappings()
    {
        return $this->mappings;
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
}
