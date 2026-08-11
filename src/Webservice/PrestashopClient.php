<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Webservice;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use Bnix\PimcorePrestashopBundle\Exception\AuthenticationException;
use Bnix\PimcorePrestashopBundle\Exception\NetworkException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PrestashopClient implements PrestashopClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly StoreConfiguration $configuration,
    ) {
    }


    public function get(
        string $resource,
        array $parameters = []
    ): string {

        return $this->request(
            'GET',
            $resource,
            [
                'query' => $parameters,
            ]
        );
    }


    public function post(
        string $resource,
        string $xml
    ): string {

        return $this->request(
            'POST',
            $resource,
            [
                'body' => $xml,
            ]
        );
    }


    public function put(
        string $resource,
        string $xml
    ): string {

        return $this->request(
            'PUT',
            $resource,
            [
                'body' => $xml,
            ]
        );
    }


    public function delete(
        string $resource,
        array $parameters = []
    ): void {

        $this->request(
            'DELETE',
            $resource,
            [
                'query' => $parameters,
            ]
        );
    }


    private function request(
        string $method,
        string $resource,
        array $options
    ): string {

        try {

            $response = $this->client->request(
                $method,
                $this->buildUrl($resource),
                array_merge(
                    [
                        'auth_basic' => [
                            $this->configuration->getApiKey(),
                            '',
                        ],
                        'headers' => [
                            'Accept' => 'application/xml',
                            'Host' => $this->configuration->getHost(),
                        ],
                    ],
                    $options
                )
            );


            $statusCode = $response->getStatusCode();


            if ($statusCode === 401 || $statusCode === 403) {

                throw new AuthenticationException(
                    sprintf(
                        'Authentication failed for store "%s".',
                        $this->configuration->getName()
                    )
                );
            }


            if ($statusCode >= 400) {

                throw new NetworkException(
                    sprintf(
                        'PrestaShop returned HTTP %d.',
                        $statusCode
                    )
                );
            }


            return $response->getContent();


        } catch (AuthenticationException|NetworkException $e) {

            throw $e;

        } catch (\Throwable $e) {

            throw new NetworkException(
                $e->getMessage(),
                0,
                $e
            );
        }
    }


    private function buildUrl(string $resource): string
    {
        return sprintf(
            '%s/api/%s',
            rtrim(
                $this->configuration->getUrl(),
                '/'
            ),
            ltrim(
                $resource,
                '/'
            )
        );
    }
}
