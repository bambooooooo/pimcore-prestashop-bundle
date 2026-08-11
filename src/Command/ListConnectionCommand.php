<?php

namespace Bnix\PimcorePrestashopBundle\Command;

use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Registry\StoreRegistry;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('prestashop:list', description: 'List configured prestashop connections')]
class ListConnectionCommand extends AbstractCommand
{
    public function __construct(private readonly StoreRegistry $stores)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $stores = $this->stores->all();

        if(!$stores)
        {
            $output->writeln('<info>No prestashop connections found</info>');
            return Command::SUCCESS;
        }

        $t = new Table($output);
        $t->setHeaders(['name', 'url', 'host']);

        foreach($stores as $store)
        {
            $t->addRow([$store->getName(), $store->getUrl(), $store->getHost()]);
        }

        $t->render();

        return Command::SUCCESS;
    }
}
