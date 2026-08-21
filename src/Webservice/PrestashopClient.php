<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Webservice;

use Bnix\PimcorePrestashopBundle\Config\StoreConfiguration;
use Bnix\PimcorePrestashopBundle\Exception\AuthenticationException;
use Bnix\PimcorePrestashopBundle\Exception\ParsingException;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Exception\PrestashopNotFoundException;
use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Webservice\Response\UploadAttachmentResponse;
use Bnix\PimcorePrestashopBundle\Xml\AttachmentXmlBuilder;
use Bnix\PimcorePrestashopBundle\Xml\FeatureValueXmlBuilder;
use Bnix\PimcorePrestashopBundle\Xml\FeatureXmlBuilder;
use Bnix\PimcorePrestashopBundle\Xml\ProductFeaturesXmlBuilder;
use Bnix\PimcorePrestashopBundle\Xml\ProductXmlBuilder;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PrestashopClient implements PrestashopClientInterface
{
    public function __construct(
        private readonly HttpClientInterface  $client,
        private readonly StoreConfiguration   $store,
        private readonly ProductXmlBuilder    $productXmlBuilder,
        private readonly AttachmentXmlBuilder $attachmentXmlBuilder,
        private readonly FeatureXmlBuilder    $featureXmlBuilder,
        private readonly FeatureValueXmlBuilder $featureValueXmlBuilder,
        private readonly ProductFeaturesXmlBuilder $productFeaturesXmlBuilder,
        private readonly CacheInterface       $cache,
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

    public function uploadProductImage(int $externalId, string $imagePath)
    {
        $response = $this->request('POST', 'images/products/' . $externalId, [
            'body' => [
                'image' => fopen($imagePath, 'r')
            ]
        ]);

        assert($this->extractImageId($response) > 0);
    }

    public function clearProductImages(int $externalId)
    {
        $ids = $this->getProductImages($externalId);

        foreach($ids as $imageId)
        {
            $this->delete('images/products/' . $externalId . "/" . $imageId);
        }
    }

    public function clearProductAttachments(int $externalId)
    {
        $ids = $this->getProductAttachments($externalId);
        foreach ($ids as $attachmentId)
        {
            $this->removeAttachment($attachmentId);
        }
    }

    private function removeAttachment(int $attachmentId)
    {
        $this->delete('attachments/' . $attachmentId);
    }

    public function getProductIdByReference(string $reference, $referenceField = 'reference'): int|null
    {
        $response = $this->get('products', [
            "filter[$referenceField]" => "[$reference]",
            'display' => '[id]',
            'limit' => 1
        ]);

        $document = simplexml_load_string($response);

        $id = (string)($document->products->product->id ?? '');

        if ($id === '') {
            return null;
        }

        return (int)$id;
    }

    private function getProductImages(int $externalId): array
    {
        try
        {
            $response = $this->get('images/products/' . $externalId);

            return $this->extractProductImagesIds($response);
        }
        catch (PrestashopNotFoundException $ex)
        {
            // prestashop throws 404 when product has no images
        }

        return [];
    }

    private function getProductAttachments(int $externalId): array
    {
        $product = $this->get('products/' . $externalId, [
            'limit' => 1
        ]);

        return $this->extractProductAttachmentsIds($product);
    }

    public function getPsFeatureId(string $featureName, array $languages): int
    {
        return $this->cache->get($this->normalizeCacheEntryKey("ps_feature_id_" . $featureName), function(ItemInterface $item) use ($featureName, $languages) {

            $res = $this->get('product_features', [
                'filter[name]' => $featureName,
            ]);

            $id = $this->extractProductFeatureId($res);

            if($id != null)
            {
                $item->expiresAfter(60 * 60 * 24);
                return $id;
            }

            $id = $this->addFeature($featureName, $languages);
            $item->expiresAfter(60 * 60 * 24);
            return $id;
        });
    }

    private function addFeature(string $featureName, array $languages): int
    {
        $xml = $this->featureXmlBuilder->build($featureName, $languages);
        $res = $this->post('product_features', $xml);

        return $this->extractProductFeatureId($res);
    }

    private function extractProductFeatureId($xml):int|null
    {
        $document = simplexml_load_string($xml);
        $id = $document->product_feature->id;

        if($id == null)
        {
            return null;
        }

        return (int)$id;
    }

    public function getPsFeatureValueId(int $featureId, string $featureValue, array $languages): int
    {
        return $this->cache->get($this->normalizeCacheEntryKey("ps_feature_value_id_" . $featureId . "_" . $featureValue),
            function(ItemInterface $item) use ($featureValue, $featureId, $languages){

            $res = $this->get('product_feature_values', [
                'filter[id_feature]' => $featureId,
                'filter[value]' => str_replace(" ", "%20", $featureValue),
            ]);

            $id = $this->extractProductFeatureValueId($res);

            if($id != null)
            {
                $item->expiresAfter(60 * 60 * 24);
                return $id;
            }

            $id = $this->addFeatureValue($featureId, $featureValue, $languages);
            $item->expiresAfter(60 * 60 * 24);

            return $id;
        });
    }

    private function addFeatureValue(int $featureId, string $featureValue, array $languages): int
    {
        $xml = $this->featureValueXmlBuilder->build($featureId, $featureValue, $languages);
        $res = $this->post('product_feature_values', $xml);

        return $this->extractProductFeatureValueId($res);
    }

    private function extractProductFeatureValueId($xml):int|null
    {
        $document = simplexml_load_string($xml);
        $id = $document->product_feature_value->id;

        if($id == null)
        {
            return null;
        }

        return (int)$id;
    }

    private function normalizeCacheEntryKey(string $key): string
    {
        return str_replace(
            ['{', '}', '(', ')', '/', '\\', '@', ':'],
            '',
            $key
        );
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
            'PATCH',
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
            throw new PrestashopNotFoundException(
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

    private function extractProductImagesIds(string $xml): array
    {
        $document = simplexml_load_string($xml);
        $ids = [];

        foreach ($document->image->declination ?? [] as $image) {
            $ids[] = (int)$image['id'];
        }

        return $ids;
    }

    private function extractProductAttachmentsIds(string $xml): array
    {
        $document = simplexml_load_string($xml);
        $ids = [];

        foreach ($document->product->associations->attachments->attachment ?? [] as $att) {
            $ids[] = (int)($att->id);
        }

        return $ids;
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

    private function extractImageId(string $xml): int
    {
        $document = simplexml_load_string($xml);
        $id = (string)($document->image->id ?? '');

        if ($id === '') {
            throw new \RuntimeException(
                'PrestaShop image creation response does not contain a image ID.',
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

    public function uploadAttachment(string $filePath, string $name, string $mimeType): UploadAttachmentResponse
    {
        $res = $this->request("POST", "attachments/file", [
            'body' => [
                'file' => fopen($filePath, 'r'),
            ]
        ]);

        $document = simplexml_load_string($res);
        $id = (int)$document->attachment->id;
        $hash = (string)$document->attachment->file;

        return new UploadAttachmentResponse($id, $hash, $mimeType);
    }

    public function updateProductAttachment(UploadAttachmentResponse $data, array $name, string $filename, int $productId): void
    {
        $xml = $this->attachmentXmlBuilder->build($data, $name, $filename, $productId);
        $this->put('attachments/' . $data->id, $xml);
    }

    public function updateProductFeatures(int $id, array $features)
    {
        $xml = $this->productFeaturesXmlBuilder->build($id, $features);
        $this->patch('products/' . $id, $xml);
    }
}
