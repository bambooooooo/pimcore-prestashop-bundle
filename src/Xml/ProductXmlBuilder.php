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

        $this->appendValue($document, $productNode, "id", $id);

        $this->appendValue($document, $productNode, "state", 1); // MANDATORY
        $this->appendValue($document, $productNode, "new", 1);
        $this->appendValue($document, $productNode, "reference", $product->reference);
        $this->appendValue($document, $productNode, "supplier_reference", $product->supplierReference);
        $this->appendValue($document, $productNode, "width", $product->width);
        $this->appendValue($document, $productNode, "height", $product->height);
        $this->appendValue($document, $productNode, "depth", $product->depth);
        $this->appendValue($document, $productNode, "weight", $product->mass);
        $this->appendValue($document, $productNode, "ean13", $product->ean);
        $this->appendValue($document, $productNode, "isbn", $product->isbn);
        $this->appendValue($document, $productNode, "upc", $product->upc);
        $this->appendValue($document, $productNode, "mpn", $product->mpn);
        $this->appendValue($document, $productNode, 'price', $product->price);
        $this->appendLocalizedValue($document, $productNode, 'description', $product->description);
        $this->appendLocalizedValue($document, $productNode,'description_short', $product->descriptionShort);
        $this->appendValue($document, $productNode, "id_manufacturer", $product->id_manufacturer);
        $this->appendValue($document, $productNode, "id_supplier", $product->id_supplier);
        $this->appendValue($document, $productNode, "id_category_default", $product->id_category_default);
        $this->appendValue($document, $productNode, "id_tax_rules_group", $product->id_tax_rules_group);
        $this->appendValue($document, $productNode, "type", $product->type ?? 1);
        $this->appendValue($document, $productNode, "location", $product->location);
        $this->appendBoolValue($document, $productNode, "quantity_discount", $product->quantity_discount);
        $this->appendValue($document, $productNode, "additional_delivery_times", $product->additional_delivery_times);
        $this->appendValue($document, $productNode, "delivery_in_stock", $product->delivery_in_stock);
        $this->appendValue($document, $productNode, "delivery_out_stock", $product->delivery_out_of_stock);
        $this->appendValue($document, $productNode, "product_type", $product->type ?? "standard");
        $this->appendBoolValue($document, $productNode, "on_sale", $product->on_sale);
        $this->appendBoolValue($document, $productNode, "online_only", $product->online_only);
        $this->appendValue($document, $productNode, "ecotax", $product->ecotax);
        $this->appendValue($document, $productNode, "minimal_quantity", $product->minimum_quantity);
        $this->appendValue($document, $productNode, "low_stock_threshold", $product->low_stock_threshold);
        $this->appendBoolValue($document, $productNode, "low_stock_alert", $product->low_stock_alert);
        $this->appendValue($document, $productNode, "unity", $product->unity);
        $this->appendValue($document, $productNode, "unit_price_ratio", $product->unit_price_ratio);
        $this->appendValue($document, $productNode, 'additional_shipping_cost', $product->additional_shipping_cost);
        $this->appendBoolValue($document, $productNode, 'active', $product->active);
        $this->appendValue($document, $productNode, 'redirect_type', $product->redirect_type);
        $this->appendValue($document, $productNode, 'id_type_redirected', $product->id_type_redirected);
        $this->appendBoolValue($document, $productNode, 'available_for_order', $product->available_for_order);
        $this->appendValue($document, $productNode, 'available_date', $product->available_date);
        $this->appendBoolValue($document, $productNode, 'show_condition', $product->show_condition);
        $this->appendValue($document, $productNode, 'condition', $product->condition);
        $this->appendBoolValue($document, $productNode, 'show_price', $product->show_price);
        $this->appendBoolValue($document, $productNode, 'indexed', $product->indexed);
        $this->appendValue($document, $productNode, 'visibility', $product->visibility);
        $this->appendBoolValue($document, $productNode, 'advanced_stock_management', $product->advanced_stock_management);
        $this->appendValue($document, $productNode, 'pack_stock_type', $product->stock_pack_type);
        $this->appendLocalizedValue($document, $productNode, 'meta_description', $product->metaDescription);
        $this->appendLocalizedValue($document, $productNode, 'meta_keywords', $product->meta_keywords);
        $this->appendLocalizedValue($document, $productNode, 'meta_title', $product->metaTitle);
        $this->appendLocalizedValue($document, $productNode, 'link_rewrite', $product->linkRewrite);
        $this->appendLocalizedValue($document, $productNode,'available_now', $product->available_now);
        $this->appendLocalizedValue($document, $productNode,'available_later', $product->available_later);
        $this->appendLocalizedValue($document, $productNode, 'name', $product->name);

        return $document->saveXML();
    }

    private function appendBoolValue(DomDocument $document, \DOMElement $parent, string $name, mixed $value): void
    {
        if($value === null)
        {
            return;
        }

        $v = ($value === '0' || $value === 0 || $value === false) ? "0" : "1";

        $element = $document->createElement($name, $v);

        $parent->appendChild($element);
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
