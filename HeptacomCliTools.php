<?php declare(strict_types=1);

namespace HeptacomCliTools;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight_Event_EventArgs;
use Shopware\Components\Plugin;
use Shopware_Controllers_Backend_PluginManager;

class HeptacomCliTools extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Console_Add_Command' => 'addCommands',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_PluginManager' => 'addDownloadButton',
        ];
    }

    /**
     * @return ArrayCollection
     */
    public function addCommands(Enlight_Event_EventArgs $args)
    {
        return new ArrayCollection([
            new Commands\BuildPluginCommand(),
            new Commands\PackPluginCommand(),
            new Commands\ValidatePluginCommand(),
            new Commands\BuildThemeCommand(),
            new Commands\InstallDependenciesCommand(),
        ]);
    }

    public function addDownloadButton(Enlight_Event_EventArgs $args)
    {
        /** @var Shopware_Controllers_Backend_PluginManager $controller */
        $controller = $args->get('subject');

        if ($controller->Request()->getActionName() !== 'load') {
            return;
        }

        $controller->View()->addTemplateDir(implode(DIRECTORY_SEPARATOR, [$this->getPath(), 'Resources', 'views']));
        $controller->View()->extendsTemplate(implode(DIRECTORY_SEPARATOR, ['backend', 'heptacom_cli_tools', 'view', 'list', 'download-button.js']));
    }
}
