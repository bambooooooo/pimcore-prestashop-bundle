<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Enum;

enum ProductField: string
{
    case REFERENCE = 'reference';
    case SUPPLIER_REFERENCE = 'supplier_reference';

    case NAME = 'name';

    case DESCRIPTION = 'description';

    case DESCRIPTION_SHORT = 'description_short';

    case META_TITLE = 'meta_title';

    case META_DESCRIPTION = 'meta_description';

    case LINK_REWRITE = 'link_rewrite';

    case PRICE = 'price';

    case WHOLESALE_PRICE = 'wholesale_price';

    case ACTIVE = 'active';

    case EAN13 = 'ean13';

    case ISBN = 'isbn';

    case UPC = 'upc';

    case MPN = 'mpn';

    case WIDTH = 'width';

    case HEIGHT = 'height';

    case DEPTH = 'depth';

    case WEIGHT = 'weight';

    case IMAGE = 'image';

    case IMAGES = 'images';
}
