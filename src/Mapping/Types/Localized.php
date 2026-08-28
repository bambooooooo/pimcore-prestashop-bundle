<?php

namespace Bnix\PimcorePrestashopBundle\Mapping\Types;

class Localized {
    public function __construct(
        public ?array $value, public string $name, public array $languages
    )
    {
        if(!$value)
        {
            foreach ($languages as $lang => $langId)
            {
                $this->value[$langId] = '';
            }
        }
    }

    public function concat(Scalar|Localized|ScalarList|Parameters|null $other): Scalar|Localized|ScalarList|Parameters
    {
        if($other == null)
        {
            return $this;
        }

        if($other instanceof Scalar)
        {
            foreach($this->value as $langId => $localizedValue)
            {
                $this->value[$langId] = $localizedValue . $other->value;
            }

            return $this;
        }

        if($other instanceof Localized)
        {
            foreach($this->value as $langId => $localizedValue)
            {
                $this->value[$langId] = $localizedValue . $other->value[$langId];
            }

            return $this;
        }

        if($other instanceof ScalarList)
        {

        }

        if($other instanceof Parameters)
        {

        }
    }
}
