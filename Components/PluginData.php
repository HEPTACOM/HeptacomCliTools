<?php declare(strict_types=1);

namespace HeptacomCliTools\Components;

use CallbackFilterIterator;
use DOMDocument;
use DOMXPath;
use Iterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class PluginData
{
    const BLACKLIST = [
        'node_modules',
        'composer.phar',
        'composer.lock',
    ];

    const WHITELIST = [
        '.swNoEncryption',
    ];

    /**
     * @var SplFileInfo
     */
    private $directory;

    /**
     * @var string
     */
    private $version = null;

    public function __construct(SplFileInfo $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return SplFileInfo
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getDirectory()->getBasename();
    }

    /**
     * @return SplFileInfo
     */
    public function getBootstrap()
    {
        return new SplFileInfo($this->getDirectory()->getPathname() . DIRECTORY_SEPARATOR . $this->getName() . '.php');
    }

    /**
     * @return SplFileInfo
     */
    public function getPluginXml()
    {
        return new SplFileInfo($this->getDirectory()->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        if (empty($this->version)) {
            $pluginXmlDocument = new DOMDocument();
            $pluginXmlDocument->validateOnParse = false;
            $pluginXmlDocument->load($this->getPluginXml()->getPathname());
            $element = (new DOMXPath($pluginXmlDocument))->query('//plugin/version');

            if ($element === false || $element->length == 0 || empty($element->item(0)->nodeValue)) {
                return $this->version = 'undefined';
            }

            $this->version = $element->item(0)->nodeValue;
        }

        return $this->version;
    }

    /**
     * @return Iterator
     */
    public function getFiles()
    {
        $files = new RecursiveDirectoryIterator($this->getDirectory());

        $filesFilter = new RecursiveCallbackFilterIterator($files, function (SplFileInfo $item, $key, $iterator) {
            if (in_array($item->getFilename(), static::WHITELIST)) {
                return true;
            }

            if (in_array($item->getFilename(), static::BLACKLIST)) {
                return false;
            }

            // hide "hidden" files
            return (bool) (strncmp($item->getFilename(), '.', 1) !== 0);
        });

        return new RecursiveIteratorIterator($filesFilter);
    }

    /**
     * @return Iterator
     */
    public function getComposerFiles()
    {
        return new CallbackFilterIterator($this->getFiles(), function (SplFileInfo $item, $key, $iterator) {
            return strcasecmp($item->getFilename(), 'composer.json') === 0
                && stripos($item->getPath(), DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) === false;
        });
    }
}
