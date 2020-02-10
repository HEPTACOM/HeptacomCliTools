# CLI tools for shopware
#### Development utility to support shopware 5 plugin development

![Packagist Version](https://img.shields.io/packagist/v/heptacom/shopware-heptacom-cli-tools?style=flat-square)
![PHP from Packagist](https://img.shields.io/packagist/php-v/heptacom/shopware-heptacom-cli-tools?style=flat-square)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](./LICENSE.md)

![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/heptacom/HeptacomCliTools?style=flat-square)
[![GitHub issues](https://img.shields.io/github/issues/HEPTACOM/HeptacomCliTools?style=flat-square)](https://github.com/HEPTACOM/HeptacomCliTools/issues)
[![GitHub forks](https://img.shields.io/github/forks/HEPTACOM/HeptacomCliTools?style=flat-square)](https://github.com/HEPTACOM/HeptacomCliTools/network)
[![GitHub stars](https://img.shields.io/github/stars/HEPTACOM/HeptacomCliTools?style=flat-square)](https://github.com/HEPTACOM/HeptacomCliTools/stargazers)
![GitHub watchers](https://img.shields.io/github/watchers/heptacom/HeptacomCliTools?style=flat-square)
![Packagist](https://img.shields.io/packagist/dt/heptacom/shopware-heptacom-cli-tools?style=flat-square)

![GitHub contributors](https://img.shields.io/github/contributors/heptacom/HeptacomCliTools?style=flat-square)
![GitHub commit activity](https://img.shields.io/github/commit-activity/y/heptacom/HeptacomCliTools?style=flat-square)

The HeptacomCliTools are some custom commands for Shopware bundeled
into a plugin. Right now it features a way to build custom made
plugins into an upload-ready zip-file. Also a second command is
under development to enable a similar build process for custom
themes.

The goal of those commands is to speed up the build process of
plugins and themes. However there are probably more commands to come
that we find useful for developers.

## Available Commands

```ksk:plugin:dependencies <plugin>```

This installs all dependencies of the plugin. Currently supported
dependencies are composer packages.

```ksk:plugin:validate <plugin>```

This lints all php files. The plugin must follow the new
plugin structure and has to be located in `custom/plugins/`. Also
it has to have a valid plugin.xml file.

```ksk:plugin:pack <plugin>```

This creates a zip file of the given plugin. The plugin must follow
the new plugin structure and has to be located in `custom/plugins/`.
Also it has to have a valid plugin.xml file. The built zip file will
be located in `KskBuilds/plugins/`.

```ksk:plugin:build <plugin>```

This installs all dependencies, lints all php files and creates a zip
file of the given plugin. The plugin must follow the new plugin
structure and has to be located in `custom/plugins/`. Also it has to
have a valid plugin.xml file. The built zip file will be located in
`KskBuilds/plugins/`.

## Changes

View the [CHANGELOG](CHANGELOG.md) file attached to this project.

## License

See [LICENSE.md](./LICENSE.md)
