<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Xml;

use DOMDocument;
use Symfony\Contracts\Translation\TranslatorInterface;


final class FeatureXmlBuilder
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function build(string $featureName, array $languages): string
    {
        $document = new DomDocument('1.0');
        $document->formatOutput = false;

        $prestashop = $document->createElement('prestashop');
        $prestashop->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $document->appendChild($prestashop);

        $featureNode = $document->createElement('product_feature');
        $prestashop->appendChild($featureNode);

        $this->addLocalizedValue($document, $featureNode, 'name', $featureName, $languages);

        return $document->saveXML();
    }

    private function addLocalizedValue(DomDocument $document, \DOMElement $parent, string $name, string $featureName, array $languages): void
    {
        $element = $document->createElement($name);

        foreach ($languages as $lang => $langId)
        {
            $language = $document->createElement('language', $this->translator->trans($featureName, locale: $lang) );
            $language->setAttribute('id', htmlspecialchars("$langId"));
            $element->appendChild($language);
        }

        $parent->appendChild($element);
    }
}
