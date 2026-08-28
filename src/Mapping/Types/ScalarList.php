<?php

namespace Bnix\PimcorePrestashopBundle\Mapping\Types;

class ScalarList {

    public function __construct(
        public ?array $value, public string $name, public array $languages
    )
    {
        if($value === null) {
            $this->value = [];
        }
    }

    public function concat(Scalar|Localized|ScalarList|Parameters $other)
    {
        if($other == null)
        {
            return $this;
        }

        if($other instanceof Scalar)
        {
            return $this->value[] = $other;
        }

        if($other instanceof Localized)
        {
            throw new \Exception("Incompatible types for field '" . $this->name . "'");
        }

        if($other instanceof ScalarList)
        {
            foreach($other->value as $v)
            {
                $this->value[] = $v;
            }

            return $this;
        }

        if($other instanceof Parameters)
        {
            throw new \Exception("Incompatible types for field '" . $this->name . "'");
        }
    }
}
