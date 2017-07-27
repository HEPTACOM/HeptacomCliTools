<?php

use HeptacomCliTools\Components\PluginData;
use Shopware\Components\Console\Application;
use Shopware\Components\CSRFWhitelistAware;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Shopware_Controllers_Backend_HeptacomCliTools extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * @var Application
     */
    private $application;

    /**
     * Returns a list with actions which should not be validated for CSRF protection
     *
     * @return string[]
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'pluginBuild',
        ];
    }

    public function preDispatch()
    {
        $this->Front()->setParam('noViewRenderer', true);
    }

    public function init()
    {
        $this->application = new Application(Shopware()->Container()->get('kernel'));
        $this->application->setAutoExit(false);
    }

    public function pluginBuildAction()
    {
        /** @var string $plugin */
        $plugin = $this->Request()->getParam('plugin');

        if ($plugin === null) {
            return;
        }

        $output = new BufferedOutput();

        $this->application->run(
            new ArrayInput(['command' => 'heptacom:plugin:build', 'plugin' => $plugin]),
            $output
        );

        $pluginDirectory = Shopware()->DocPath() . implode(DIRECTORY_SEPARATOR, ['custom', 'plugins', $plugin]);
        $pluginData = new PluginData(new SplFileInfo($pluginDirectory));

        $filename = $pluginData->getName() . '_' . $pluginData->getVersion() . '.zip';
        $zipFile = Shopware()->DocPath() . implode(DIRECTORY_SEPARATOR, ['HeptacomBuilds', 'plugins', $filename]);

        if (!file_exists($zipFile)) {
            return;
        }

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo file_get_contents($zipFile);
    }
}
