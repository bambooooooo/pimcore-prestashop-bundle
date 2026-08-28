<?php

namespace Bnix\PimcorePrestashopBundle\Mapping\Types;

use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;

class Parameters {

    public function __construct(
        public ?array $value, public string $name, public array $languages
    )
    {
        if($value == null)
        {
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
            $this->value[$other->name] = $other->value;
            return $this;
        }

        if($other instanceof Localized)
        {
            $cls = get_class($other);
            throw new PrestashopException("Incompatible types for field '" . $this->name . "' ({$cls})");
        }

        if($other instanceof ScalarList)
        {
            $cls = get_class($other);
            throw new PrestashopException("Incompatible types for field '" . $this->name . "' ({$cls})");
        }

        if($other instanceof Parameters)
        {
            foreach ($other->value as $key => $parameter)
            {
                $this->value[$key] = $parameter;
            }

            return $this;
        }
    }
}
