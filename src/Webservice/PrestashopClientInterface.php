<?php

namespace Bnix\PimcorePrestashopBundle\Webservice;

interface PrestashopClientInterface
{
    public function get(
        string $resource,
        array $parameters = []
    ): string;


    public function post(
        string $resource,
        string $xml
    ): string;


    public function put(
        string $resource,
        string $xml
    ): string;


    public function delete(
        string $resource,
        array $parameters = []
    ): void;
}
