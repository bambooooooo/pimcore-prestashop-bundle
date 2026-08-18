<?php

namespace Bnix\PimcorePrestashopBundle\Command;

use Bnix\PimcorePrestashopBundle\Message\PrestashopProductSyncMessage;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('prestashop:sync', description: 'Dispatch sync meesage for given product in given store')]
class SyncCommand extends AbstractCommand
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
        parent::__construct();
    }

    public function configure()
    {
        $this->addArgument('store', InputArgument::REQUIRED);
        $this->addArgument('product_id', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $store = (string)$input->getArgument('store');
        $productId = (int)$input->getArgument('product_id');

        $this->bus->dispatch(new PrestashopProductSyncMessage($productId, $store));

        return Command::SUCCESS;
    }
}
