<?php
namespace Kernel\Db;

use Kernel\Entity\AbstractEntity;

/**
 * Service class for work with DB tables. Uses reflection.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class DbTableManager {
    private $cacheFolder;
    private $extension;
    private $tables = array();
    public function __construct($cacheFolder, $extension = 'cache')
    {
        $this->cacheFolder = $cacheFolder;
        $this->extension = $extension;
    }

    public function _getMap($classPath, $sourceFile)
    {
        $this->load($classPath, $sourceFile)->getMap();
    }

    protected function load($classPath, $sourceFile)
    {
        if (isset($this->tables[$classPath])) {
            return $this->tables[$classPath];
        }
        $name = preg_replace('/\//', '', $classPath);
        $cacheFile = $this->cacheFolder . DIRECTORY_SEPARATOR . $name .
            '.' .$this->extension;
        if (!file_exists($cacheFile) ||
            filemtime($cacheFile) <= filemtime($sourceFile)) {
            $this->build($classPath, $cacheFile);
        } else {
            $this->loadCache($cacheFile);
        }

        return $this->tables[$classPath];
    }

    protected function build($classPath, $cacheFile)
    {
        $refClass = new \ReflectionClass($classPath);
        $classDoc = $refClass->getDocComment();
        preg_match_all('/@@Table[A-Z][a-zA-Z ]+=[\s\'\"а-яА-Яa-zA-Z]+\n/', $classDoc, $matches);
    }
    protected function loadCache($cacheFileName)
    {
        $table = unserialize(file_get_contents($cacheFileName));
        $this->tables[$table->getNamespace()] = $table;
    }
    protected function getOptions()
    {
        $this->options;
    }

}
