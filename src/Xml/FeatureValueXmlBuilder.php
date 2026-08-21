<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Xml;

use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use DOMDocument;
use Symfony\Contracts\Translation\TranslatorInterface;


final class FeatureValueXmlBuilder
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function build(int $featureId, string $featureValue, array $languages): string
    {
        $document = new DomDocument('1.0', 'UTF-8');
        $document->formatOutput = false;

        $prestashop = $document->createElement('prestashop');
        $prestashop->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $document->appendChild($prestashop);

        $featureNode = $document->createElement('product_feature_value');
        $prestashop->appendChild($featureNode);

        $this->appendValue($document, $featureNode, 'id_feature', "$featureId");
        $this->addLocalizedValue($document, $featureNode, 'value', $featureValue, $languages);

        return $document->saveXML();
    }

    private function appendValue(DomDocument $document, \DOMElement $parent, string $name, string $value): void
    {
        if($value == null)
        {
            return;
        }

        $element = $document->createElement($name, htmlspecialchars($value));

        $parent->appendChild($element);
    }

    private function addLocalizedValue(DomDocument $document, \DOMElement $parent, string $name, string $featureName, array $languages): void
    {
        $element = $document->createElement($name);

        foreach ($languages as $lang => $langId)
        {
            $language = $document->createElement('language', $this->translator->trans($featureName, locale: $lang));
            $language->setAttribute('id', "$langId");
            $element->appendChild($language);
        }

        $parent->appendChild($element);
    }
}
