<?php

namespace Bnix\PimcorePrestashopBundle\Mapping\Types;

class Scalar {
    public function __construct(
        public ?string $value, public string $name, public array $languages
    )
    {
    }

    public function concat(Scalar|Localized|ScalarList|Parameters|null $other): Scalar|Localized|ScalarList|Parameters
    {
        if($other->value == null)
        {
            return $this;
        }

        if($other instanceof Scalar)
        {
            $ret = new Scalar(($this->value ?? "") . $other->value, $this->name, $this->languages);
            return $ret;
        }

        if($other instanceof Localized)
        {
            foreach($other->value as $langId => $localizedValue)
            {
                $other->value[$langId] = $this->value . $localizedValue;
            }

            return $other;
        }

        if($other instanceof ScalarList)
        {
            $other->value[] = $this->value;
            return $other;
        }

        if($other instanceof Parameters)
        {
            $other->value[$this->name] = $this->value;
        }

        return $other;
    }
}
