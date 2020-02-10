<?php declare(strict_types=1);

namespace HeptacomCliTools\Commands;

use HeptacomCliTools\Components\PluginData;
use HeptacomCliTools\Components\PluginLinter;
use Shopware\Commands\ShopwareCommand;
use SplFileInfo;
use stdClass;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidatePluginCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this->setName('ksk:plugin:validate')
            ->setDescription('Validates a given plugin.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The name of the plugin you want to validate.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginDirectory = Shopware()->DocPath() . implode(DIRECTORY_SEPARATOR, ['custom', 'plugins', $input->getArgument('plugin')]);

        /** @var stdClass $state */
        $state = new stdClass();
        $state->progressBar = null;

        $plugin = new PluginData(new SplFileInfo($pluginDirectory));

        $output->writeln('Linting plugin...');
        PluginLinter::lint(
            $plugin,
            function ($count) use ($output, $state) {
                $state->progressBar = new ProgressBar($output, $count);
            }, function () use ($state) {
                $state->progressBar->advance();
            }
        );
        $state->progressBar->finish();
        $output->writeln(['', 'All PHP files linted successfully.']);
    }
}
