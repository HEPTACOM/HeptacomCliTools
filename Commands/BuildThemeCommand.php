<?php declare(strict_types=1);

namespace HeptacomCliTools\Commands;

use Shopware\Commands\ShopwareCommand;
use SplFileInfo;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildThemeCommand extends ShopwareCommand
{
    /**
     * @var SplFileInfo
     */
    protected $themeDir;

    protected function configure()
    {
        $this->setName('ksk:theme:build')
            ->setDescription('Builds a zip file for a theme using the new template structure.')
            ->addArgument(
                'theme',
                InputArgument::REQUIRED,
                'The name of the theme you want to build.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $themeName = $input->getArgument('theme');

        $this->releaseDir = new SplFileInfo(Shopware()->DocPath() .
            implode(DIRECTORY_SEPARATOR, ['HeptacomBuilds', 'themes']));
        $this->themeDir = new SplFileInfo(Shopware()->DocPath() .
            implode(DIRECTORY_SEPARATOR, ['themes', 'Frontend', $themeName]));

        $output->writeln([
            'Theme successfully built.',
            'Name: ' . $this->themeDir->getBasename(),
        ]);
    }
}
