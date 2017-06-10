<?php

namespace HeptacomCliTools;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight_Event_EventArgs;
use Shopware\Components\Plugin;
use HeptacomCliTools\Commands;

class HeptacomCliTools extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Console_Add_Command' => 'addCommands'
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     * @return ArrayCollection
     */
    public function addCommands(Enlight_Event_EventArgs $args)
    {
        return new ArrayCollection([
            new Commands\BuildPluginCommand(),
            new Commands\PackPluginCommand(),
            new Commands\ValidatePluginCommand(),
            new Commands\BuildThemeCommand(),
        ]);
    }
}
