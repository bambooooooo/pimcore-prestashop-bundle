<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Xml;

use DOMDocument;

final class ProductFeaturesXmlBuilder
{
    public function build(int $id, array $features): string
    {
        $document = new DomDocument('1.0');
        $document->formatOutput = false;

        $prestashop = $document->createElement('prestashop');
        $prestashop->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $document->appendChild($prestashop);

        $productNode = $document->createElement('product');
        $prestashop->appendChild($productNode);

        $this->appendValue($document, $productNode, 'id', $id);

        $associations = $document->createElement('associations');
        $productNode->appendChild($associations);

        $featuresNode = $document->createElement('product_features');
        $associations->appendChild($featuresNode);

        foreach ($features as $featureId => $featureValueId)
        {
            $featureNode = $document->createElement('product_feature');
            $featuresNode->appendChild($featureNode);

            $this->appendValue($document, $featureNode, 'id', "$featureId");
            $this->appendValue($document, $featureNode, 'id_feature_value', "$featureValueId");
        }

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
}
