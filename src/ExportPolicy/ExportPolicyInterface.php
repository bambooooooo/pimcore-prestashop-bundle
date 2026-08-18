<?php

namespace Bnix\PimcorePrestashopBundle\ExportPolicy;

interface ExportPolicyInterface
{
    /**
     * Checking if object should be taken into integration
     *
     * @param object $object
     * @return bool
     */
    public function supports(object $object): bool;
}
