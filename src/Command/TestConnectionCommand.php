<?php

namespace Bnix\PimcorePrestashopBundle\Command;

use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('prestashop:test', description: 'Test connection with prestashop')]
class TestConnectionCommand extends AbstractCommand
{
    public function __construct(private readonly PrestashopClientFactory $clientFactory)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('store', InputArgument::REQUIRED, 'Configured store name');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $store = (string) $input->getArgument('store');

        try {
            $client = $this->clientFactory->create($store);

            $output->writeln("<info>Checking connection with {$store}</info>");

            $response = $client->get('');
            $length = strlen($response);

            $output->writeln("<info>✅ Connection successful</info>");
            $output->writeln("<info>Response length: {$length} bytes</info>");

            return Command::SUCCESS;
        }
        catch (PrestashopException $e)
        {
            $output->writeln("<error>❌ Connection failed</error>");
            $output->writeln("{$e->getMessage()}");
        }

        return Command::FAILURE;
    }
}
