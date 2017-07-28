<?php

namespace HeptacomCliTools\Commands;

use HeptacomCliTools\Components\PluginData;
use HeptacomCliTools\Components\PluginPacker;
use SplFileInfo;
use stdClass;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

/**
 * Class PackPluginCommand
 * @package HeptacomCliTools\Commands
 */
class PackPluginCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this->setName('ksk:plugin:pack')
            ->setDescription('Builds a zip archive for a plugin using the new plugin structure.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The name of the plugin you want to pack.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputDirectory = Shopware()->DocPath() . implode(DIRECTORY_SEPARATOR, ['KskBuilds', 'plugins']);
        $pluginDirectory = Shopware()->DocPath() . implode(DIRECTORY_SEPARATOR, ['custom', 'plugins', $input->getArgument('plugin')]);

        /** @var stdClass $state */
        $state = new stdClass();
        $state->progressBar = null;

        $plugin = new PluginData(new SplFileInfo($pluginDirectory));

        $archive = PluginPacker::pack(
            $plugin,
            new SplFileInfo($outputDirectory),
            function ($count) use($output, $state) {
                $output->writeln('Creating archive...');
                $state->progressBar = new ProgressBar($output, $count);
            },
            function () use ($state) {
                $state->progressBar->advance();
            },
            function ($message) use ($output, $state) {
                $state->progressBar->finish();
                $output->writeln(array_merge([''], $message));
            }
        );

        $output->writeln([
            'Plugin packed successfully.',
            "Location: $archive",
        ]);
    }
}
