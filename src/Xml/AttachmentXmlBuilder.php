<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Xml;

use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use Bnix\PimcorePrestashopBundle\Webservice\Response\UploadAttachmentResponse;
use DOMDocument;


final class AttachmentXmlBuilder
{
    public function build(UploadAttachmentResponse $data, array $name, string $fileName, int $productId): string
    {
        $document = new DomDocument('1.0');
        $document->formatOutput = false;

        $prestashop = $document->createElement('prestashop');
        $prestashop->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $document->appendChild($prestashop);
        $attachmentNode = $document->createElement('attachment');
        $prestashop->appendChild($attachmentNode);

        $this->appendValue($document, $attachmentNode, "id", $data->id);
        $this->appendValue($document, $attachmentNode, "file", $data->hash);
        $this->appendValue($document, $attachmentNode, "file_name", $fileName);
        $this->appendValue($document, $attachmentNode, "mime", $data->mime);
        $this->appendLocalizedValue($document, $attachmentNode, 'name', $name);

        $assocationsNode = $document->createElement('associations');
        $attachmentNode->appendChild($assocationsNode);
        $productsNode = $document->createElement('products');
        $assocationsNode->appendChild($productsNode);
        $productNode = $document->createElement('product');
        $productsNode->appendChild($productNode);

        $this->appendValue($document, $productNode, "id", $productId);

        return $document->saveXML();
    }

    private function appendValue(DomDocument $document, \DOMElement $parent, string $name, mixed $value): void
    {
        if($value == null)
        {
            return;
        }

        $element = $document->createElement($name, htmlspecialchars("$value"));

        $parent->appendChild($element);
    }

    private function appendLocalizedValue(DomDocument $document, \DOMElement $parent, string $name, array|null $localizedValue): void
    {
        if($localizedValue == null)
        {
            return;
        }

        $element = $document->createElement($name);

        foreach ($localizedValue as $langId => $localizedName)
        {
            $language = $document->createElement('language', "$localizedName" ?? "");
            $language->setAttribute('id', "$langId");
            $element->appendChild($language);
        }

        $parent->appendChild($element);
    }

}
