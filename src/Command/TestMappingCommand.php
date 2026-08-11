<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Command;

use Bnix\PimcorePrestashopBundle\Prestashop\ProductMapper;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\ProductSet;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'prestashop:test-mapping', description: 'Test mapping for given shop - data object pair'
)]
final class TestMappingCommand extends Command
{
    public function __construct(
        private readonly ProductMapper $mapper,
        private readonly StoreRegistry $configuration,
    ) {
        parent::__construct();
    }

    public function configure()
    {
        $this->addArgument('store', InputArgument::REQUIRED, 'Store name');
        $this->addArgument('object_id', InputArgument::REQUIRED, 'Id of DataObject valid for given store');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        $store = (string)$input->getArgument('store');
        $objectId = (int)$input->getArgument('object_id');

        $obj = DataObject::getById($objectId);

        $this->mapper->map(
            $obj,
            $this->configuration->get($store)
        );

        return Command::SUCCESS;
    }
}
