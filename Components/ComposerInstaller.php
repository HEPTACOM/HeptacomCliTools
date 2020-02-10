<?php declare(strict_types=1);

namespace HeptacomCliTools\Components;

use InvalidArgumentException;
use SplFileInfo;

abstract class ComposerInstaller
{
    use FindsPhpExecutable;

    /**
     * @param string $composerJson
     * @param mixed  $output
     */
    public static function install($composerJson, &$output)
    {
        if (empty($composerJson)) {
            throw new InvalidArgumentException('composerJson is empty');
        }

        if (!file_exists($composerJson)) {
            throw new InvalidArgumentException('composerJson does not exist');
        }

        exec(static::getPhpCommandline([static::getComposerPath(), 'install', '--no-dev', '-n', '-d', dirname($composerJson)]), $output, $return_var);

        return !$return_var;
    }

    /**
     * @return SplFileInfo
     */
    private static function getComposerPath()
    {
        $composerPath = new SplFileInfo('./composer.phar');

        if (!$composerPath->isFile()) {
            copy('https://getcomposer.org/composer.phar', $composerPath->getPathname());
        }

        return $composerPath;
    }
}
