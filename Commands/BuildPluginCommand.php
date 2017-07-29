<?php

namespace HeptacomCliTools\Commands;

use Shopware\Components\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

/**
 * Class BuildPluginCommand
 * @package HeptacomCliTools\Commands
 */
class BuildPluginCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this->setName('ksk:plugin:build')
            ->setDescription('Builds a zip archive for a plugin using the new plugin structure.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The name of the plugin you want to build.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = new Application(Shopware()->Container()->get('kernel'));
        $application->setAutoExit(false);

        $application->run(
            new ArrayInput(array_merge($input->getArguments(), ['command' => (new InstallDependenciesCommand())->getName()])),
            $output
        );
        $application->run(
            new ArrayInput(array_merge($input->getArguments(), ['command' => (new ValidatePluginCommand())->getName()])),
            $output
        );
        $application->run(
            new ArrayInput(array_merge($input->getArguments(), ['command' => (new PackPluginCommand())->getName()])),
            $output
        );
    }
}
