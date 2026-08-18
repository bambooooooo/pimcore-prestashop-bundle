<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Xml;

use Bnix\PimcorePrestashopBundle\Prestashop\PrestashopProductData;
use DOMDocument;


final class ProductXmlBuilder
{
    public function build(PrestashopProductData $product, int $id = null): string
    {
        $document = new DomDocument('1.0');
        $document->formatOutput = false;

        $prestashop = $document->createElement('prestashop');
        $prestashop->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $document->appendChild($prestashop);
        $productNode = $document->createElement('product');
        $prestashop->appendChild($productNode);

        if($id !== null)
        {
            $this->appendValue($document, $productNode, "id", $id);
        }

        $this->appendValue($document, $productNode, "state", 1); // MANDATORY
        $this->appendValue($document, $productNode, "new", 1);
        $this->appendValue($document, $productNode, "reference", ($product->referencePrefix ? $product->referencePrefix . "_" : '') . $product->reference);
        $this->appendValue($document, $productNode, "supplier_reference", $product->reference);
        $this->appendValue($document, $productNode, "width", $product->width);
        $this->appendValue($document, $productNode, "height", $product->height);
        $this->appendValue($document, $productNode, "depth", $product->depth);
        $this->appendValue($document, $productNode, "weight", $product->mass);
        $this->appendValue($document, $productNode, "ean13", $product->ean);
        $this->appendValue($document, $productNode, "isbn", $product->isbn);
        $this->appendValue($document, $productNode, "upc", $product->upc);
        $this->appendValue($document, $productNode, "mpn", $product->mpn);
        $this->appendValue($document, $productNode, 'price', $product->price);

//        $this->appendLocalizedValue($document, $productNode, 'description', $product->description);
//        $this->appendLocalizedValue($document, $productNode,'description_short', $product->descriptionShort);

//        $this->appendValue($document, $productNode, "id_manufacturer", 1);
//        $this->appendValue($document, $productNode, "id_supplier", 1);
//        $this->appendValue($document, $productNode, "id_category_default", 0);
//        $this->appendValue($document, $productNode, "cache_default_attribute", 1);
//        $this->appendValue($document, $productNode, "id_default_image", null);
//        $this->appendValue($document, $productNode, "id_default_combination", null);
//        $this->appendValue($document, $productNode, "id_tax_rules_group", 1);
//        $this->appendValue($document, $productNode, "type", 1);
//        $this->appendValue($document, $productNode, "id_shop_default", 1);
//        $this->appendValue($document, $productNode, "location", null);
//        $this->appendValue($document, $productNode, "quantity_discount", 0);
//        $this->appendValue($document, $productNode, "cache_is_pack", 0);
//        $this->appendValue($document, $productNode, "cache_has_attachments", 0);
//        $this->appendValue($document, $productNode, "is_virtual", 0);
//        $this->appendValue($document, $productNode, "additional_delivery_times", null);
//        $this->appendValue($document, $productNode, "delivery_in_stock", null);
//        $this->appendValue($document, $productNode, "delivery_out_stock", null);
//        $this->appendValue($document, $productNode, "product_type", "standard");
//        $this->appendValue($document, $productNode, "on_sale", 0);
//        $this->appendValue($document, $productNode, "online_only", 0);
//        $this->appendValue($document, $productNode, "ecotax", 0);
//        $this->appendValue($document, $productNode, "minimal_quantity", 0);
//        $this->appendValue($document, $productNode, "low_stock_threshold", 0);
//        $this->appendValue($document, $productNode, "low_stock_alert", 0);
//        $this->appendValue($document, $productNode, "unity", null);
//        $this->appendValue($document, $productNode, "unit_price_ratio", null);
//        $this->appendValue($document, $productNode, 'additional_shipping_cost', null);
//        $this->appendValue($document, $productNode, 'customizable', null);
//        $this->appendValue($document, $productNode, 'text_fields', null);
//        $this->appendValue($document, $productNode, 'uploadable_files', null);
//        $this->appendValue($document, $productNode, 'active', 1); // DO NOT APPEND
//        $this->appendValue($document, $productNode, 'redirect_type', null);
//        $this->appendValue($document, $productNode, 'id_type_redirected', null);
//        $this->appendValue($document, $productNode, 'available_for_order', 1);
//        $this->appendValue($document, $productNode, 'available_date', null);
//        $this->appendValue($document, $productNode, 'show_condition', null);
//        $this->appendValue($document, $productNode, 'condition', null);
//        $this->appendValue($document, $productNode, 'show_price', 1);
//        $this->appendValue($document, $productNode, 'indexed', null);
//        $this->appendValue($document, $productNode, 'visibility', 'both');
//        $this->appendValue($document, $productNode, 'advanced_stock_management', null);
//        $this->appendValue($document, $productNode, 'pack_stock_type', null);
//        $this->appendValue($document, $productNode, 'meta_description', null);
//        $this->appendValue($document, $productNode, 'meta_keywords', null);
//        $this->appendValue($document, $productNode, 'meta_title', null);
//        $this->appendValue($document, $productNode, 'link_rewrite', null);
//        $this->appendValue($document, $productNode,'available_now', null);
//        $this->appendValue($document, $productNode,'available_later', null);
//        $this->appendValue($document, $productNode, 'associations', null);


        $this->appendLocalizedValue($document, $productNode, 'name', $product->name);

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
            $language = $document->createElement('language', $localizedName ?? "");
            $language->setAttribute('id', "$langId");
            $element->appendChild($language);
        }

        $parent->appendChild($element);
    }

}
