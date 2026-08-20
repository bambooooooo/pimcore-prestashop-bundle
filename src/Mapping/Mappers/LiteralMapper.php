<?php

namespace Bnix\PimcorePrestashopBundle\Mapping\Mappers;

use Bnix\PimcorePrestashopBundle\Mapping\MapperInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsTaggedItem(priority: -100)]
class LiteralMapper implements MapperInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {

    }

    public function map(DataObject $object, string $field, array $languages = null, bool $isLocalized = false): mixed
    {
        if(str_starts_with($field, '~') && str_ends_with($field, '~'))
        {
            $text = substr($field, 1, -1);
            $ret = [];
            foreach ($languages as $language => $langId)
            {
                $ret[$langId] = $this->translator->trans($text, locale: $language);
            }

            return $ret;
        }
        return $field;
    }

    public function supports(string $fieldOrMapper, Data|null $definition, DataObject $product): bool
    {
        return true;
    }
}
