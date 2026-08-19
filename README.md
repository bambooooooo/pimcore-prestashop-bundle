# PimcorePrestashopBundle

Customizable pimcore to prestashop product integration

### Installation

```composer require bnix/pimcore-prestashop-bundle```

### Usage

Define configuration in `config/packages/pimcore_prestashop.yaml` for every prestashop shop to integrate

### Example usage

1. Put this minimal config into `config/packages/pimcore_prestashop.yaml`

```
# config/packages/pimcore_prestashop.yaml

bnix_pimcore_prestashop:
    stores:
        dev:
            url: 'http://prestashop'
            host: 'prestashop.local:7777'
            api_key: 'AWVDMSUHIPXEQD7EA9A1GIKDSSLE9D4H'

            languages:
                en: 1

            currencies:
                - EUR

            mappings:
                Product:
                    reference: Id
                    name: Name
                    price: Price
```

2. Run `bin/console prestashop:sync dev`
3. Done!

## Mapping resolvering

Integrations support 3 kinds of mapping:

1. User defined (highest prioryty) - mappers defined by user that extends `AbstractMapper`
2. Class field - acessible by standard getter
3. Fixed value - constant value

Localized fields are fully supported

## Extended Prestahop API support

You can define your custom listener that will be executed before http api reqeust. To do so, please define Listener in
`src/EventListener` and configure it to listen `bnix.prestashop.preSend` event.


