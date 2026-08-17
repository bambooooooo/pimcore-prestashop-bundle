<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Webservice;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use Bnix\PimcorePrestashopBundle\Exception\AuthenticationException;
use Bnix\PimcorePrestashopBundle\Exception\NetworkException;
use Bnix\PimcorePrestashopBundle\Exception\ParsingException;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Xml\ProductXmlBuilder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PrestashopClient implements PrestashopClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly StoreConfiguration  $store,
        private readonly ProductXmlBuilder   $productXmlBuilder,
    ) {
    }

    public function createProduct(PrestashopProductData $product): int
    {
        $xml = $this->productXmlBuilder->build($product);
        $response = $this->post('products', $xml);

        return $this->extractProductId($response);
    }

    public function updateProduct(PrestashopProductData $product, int $externalId)
    {
        $xml = $this->productXmlBuilder->build($product, $externalId);
        $response = $this->put('products/' . $externalId, $xml);

        assert($externalId == $this->extractProductId($response));
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


    private function post(
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


    private function put(
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

    private function patch(
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


    private function delete(
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
                            $this->store->getApiKey(),
                            '',
                        ],
                        'headers' => [
                            'Accept' => 'application/xml',
                            'Host' => $this->store->getHost(),
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
                        $this->store->getName()
                    )
                );
            }

            if($statusCode === 404)
            {
                throw new PrestashopException(
                    sprintf(
                        'Entity not found in store "%s".',
                        $this->store->getName()
                    )
                );
            }


            if ($statusCode >= 400) {

                try
                {
                    $errorsDetails = $this->extractErrors($response->getContent(false));

                    throw new PrestashopException(
                        sprintf('Prestashop validation errors: %s', implode(', ', $errorsDetails)),
                    );
                }
                catch (ParsingException)
                {
                    throw new PrestashopException(
                        sprintf(
                            'PrestaShop returned HTTP %d: %s',
                            $statusCode,
                            $response->getContent(false)
                        )
                    );
                }
            }

            return $response->getContent();


        } catch (AuthenticationException|NetworkException $e) {

            throw $e;

        } catch (\Throwable $e) {

            throw new PrestashopException(
                $e->getMessage(),
                0,
                $e
            );
        }
    }

    private function extractErrors(string $xml): array
    {
        try
        {
            $doc = simplexml_load_string($xml);
            $errors = [];

            foreach ($doc->errors->error ?? [] as $error) {
                $errors[] = $error->code . ': ' . $error->message;
            }

            return $errors;
        }
        catch (\Throwable)
        {
            throw new ParsingException();
        }
    }

    private function extractProductId(string $xml): int
    {
        $document = simplexml_load_string($xml);
        $id = (string)($document->product->id ?? '');

        if ($id === '') {
            throw new \RuntimeException(
                'PrestaShop product creation response does not contain a product ID.',
            );
        }

        return (int)$id;
    }

    private function buildUrl(string $resource): string
    {
        return sprintf(
            '%s/api/%s',
            rtrim(
                $this->store->getUrl(),
                '/'
            ),
            ltrim(
                $resource,
                '/'
            )
        );
    }
}
