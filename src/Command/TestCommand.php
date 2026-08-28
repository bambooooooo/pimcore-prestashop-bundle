<?php

namespace Bnix\PimcorePrestashopBundle\Command;

use Bnix\PimcorePrestashopBundle\Exception\PrestashopException;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientFactory;
use Bnix\PimcorePrestashopBundle\Webservice\PrestashopClientInterface;
use Pimcore\Console\AbstractCommand;
use Pimcore\Tool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('prestashop:test', description: 'Test connection with prestashop')]
class TestCommand extends AbstractCommand
{
    private readonly array $requiredPrivileges;

    public function __construct(private readonly PrestashopClientFactory    $clientFactory)
    {
        $this->requiredPrivileges = [
            'products' => ['get', 'post', 'patch', 'delete'],
            'product_features' => ['get', 'post', 'patch', 'delete'],
            'images' => ['get', 'post', 'patch', 'delete'],
        ];

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('store', InputArgument::REQUIRED, 'Configured store name');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $store = (string) $input->getArgument('store');
        $test = (bool) $input->getOption('sync');

        try {
            $client = $this->clientFactory->create($store);

            $this->checkPing($client, $store);
            $this->checkPermissions($client);
            $this->checkLanguages($client, $store);

            return Command::SUCCESS;
        }
        catch (PrestashopException $e)
        {
            $output->writeln("<error>❌ Configuration failed</error>");
            $output->writeln("<error>{$e->getMessage()}</error>");
        }

        return Command::FAILURE;
    }

    private function checkLanguages(PrestashopClientInterface $client, string $store)
    {
        $this->writeInfo("Fetching languages...");

        $pimcoreLanguages = Tool::getValidLanguages();
        $prestashopLanguages = array_keys($client->getSupportedLanguages());

        $diff = array_diff($prestashopLanguages, $pimcoreLanguages);

        if($diff)
        {
            throw new PrestashopException("Unsupported languages: " . implode(', ', $diff));
        }
    }

    private function checkPing(PrestashopClientInterface $client, string $store)
    {
        $this->writeInfo("Checking connection with {$store}...");
        $client->get('');
        $this->writeInfo("  ✅ Connection successful.");
    }

    private function checkPermissions(PrestashopClientInterface $client): void
    {
        $this->writeInfo("Checking apikey privileges...");

        $privileges = $client->getPrivileges();

        foreach ($this->requiredPrivileges as $resource => $actions) {
            foreach ($actions as $action) {
                if (!in_array($action, $privileges[$resource])) {
                    throw new PrestashopException("Missing privilege {$resource}->{$action}");
                }
            }
        }

        $this->writeInfo("  ✅ Privileges granted");
    }
}
