<?php

namespace HeptacomCliTools\Components;

use DOMDocument;
use DOMXPath;
use Iterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class PluginData
 * @package HeptacomCliTools\Components
 */
class PluginData
{
    const BLACKLIST = [
        'node_modules',
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

    /**
     * PluginData constructor.
     * @param SplFileInfo $directory
     */
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
}