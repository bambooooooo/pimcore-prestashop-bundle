<?php

namespace Bnix\PimcorePrestashopBundle\Command;

use Bnix\PimcorePrestashopBundle\Message\PrestashopProductSyncMessage;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Pimcore\Model\DataObject;

#[AsCommand('prestashop:sync', description: 'Dispatch sync meesage for given store (and given product if provided)')]
class SyncCommand extends AbstractCommand
{
    public function __construct(private readonly MessageBusInterface $bus, private readonly Storeregistry $stores)
    {
        parent::__construct();
    }

    public function configure()
    {
        $this->addArgument('store', InputArgument::REQUIRED);
        $this->addArgument('product_id', InputArgument::OPTIONAL, 'Product ID', 0);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force sync (skip hash checking)');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $store = $this->stores->get((string)$input->getArgument('store'));
        $productId = (int)$input->getArgument('product_id');
        $force = $input->getOption('force') === true;

        if(0 != $productId && DataObject::getById($productId) === null)
        {
            $this->writeError("Product with ID {$productId} not found");
            return Command::FAILURE;
        }

        if($productId === 0)
        {
            $classNames = array_keys($store->getMappings());
            $listing = new DataObject\Listing();

            $listing->setCondition("`className` IN ('" . implode("','", $classNames) . "')");
            $ids = $listing->loadIdList();
            $total = count($ids);

            if($total === 0)
            {
                $this->writeInfo("No objects found for store {$store->getName()}");
                return Command::SUCCESS;
            }

            $this->writeInfo("Syncing {$total} products with store {$store->getName()}...");

            $progressBar = new ProgressBar($output, $total);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% -- %message%');

            $progressBar->setMessage('Syncing products');
            $progressBar->start();

            foreach($ids as $id)
            {
                $progressBar->setMessage("#{$id}");
                $this->bus->dispatch(new PrestashopProductSyncMessage($id, $store->getName(), $force));
                $progressBar->advance();
            }

            $progressBar->finish();

            $output->writeln('');

            return Command::SUCCESS;
        }

        $this->bus->dispatch(new PrestashopProductSyncMessage($productId, $store->getName(), $force));

        return Command::SUCCESS;
    }
}
