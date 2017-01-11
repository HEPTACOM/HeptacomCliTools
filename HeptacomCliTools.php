<?php

namespace HeptacomCliTools;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight_Event_EventArgs;
use Shopware\Components\Plugin;
use HeptacomCliTools\Commands\BuildPluginCommand;

class HeptacomCliTools extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Console_Add_Command' => 'addCommands'
        ];
    }

    public function addCommands(Enlight_Event_EventArgs $args)
    {
        return new ArrayCollection([
            new BuildPluginCommand(),
        ]);
    }
}
