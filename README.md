# HeptacomCliTools for Shopware

The HeptacomCliTools are some custom commands for Shopware bundeled
into a plugin. Right now it features a way to build custom made
plugins into an upload-ready zip-file. Also a second command is
under development to enable a similar build process for custom
themes.

The goal of those two commands is to speed up the build process
of plugins and themes. However there are probably more commands
to come that we find useful for developers.

## Available Commands

```heptacom:plugin:validate <plugin>```

This lints all php files. The plugin must follow the new
plugin structure and has to be located in `custom/plugins/`. Also
it has to have a valid plugin.xml file.

```heptacom:plugin:pack <plugin>```

This creates a zip file of the given plugin. The plugin must follow
the new plugin structure and has to be located in `custom/plugins/`.
Also it has to have a valid plugin.xml file. The built zip file will
be located in `HeptacomBuilds/plugins/`.

```heptacom:plugin:build <plugin>```

This lints all php files and creates a zip file of the given plugin.
The plugin must follow the new plugin structure and has to be located
in `custom/plugins/`. Also it has to have a valid plugin.xml file.
The built zip file will be located in `HeptacomBuilds/plugins/`.

[![HEPTACOM-Icon](https://www.heptacom.de/fileadmin/templates/pages/heptacom/_resources/img/favicon/favicon-16x16.png) HEPTACOM Website](https://www.heptacom.de)
