<?php
namespace Kernel\View;
use Kernel\Exceptions\InvalidViewFileException;
use Kernel\Exceptions\InvalidViewFileDefinitionException;

/**
 * @author Alexey Korolev <koroleffas@gmail.com>
 *         Date: 21.01.13
 *         Time: 23:44
 */
class View
{
    const DEFAULT_EXTENSION = "phtml";

    private $_variables = array();

    private $_extension;
    private $_kernelFolder;
    private $_viewsFolder;

    public function __construct($kernelFolder, $extension = null, $viewsFolder = "Views")
    {
        if (!empty($extension)) {
            $this->_extension = trim($extension, " .");
        } else {
            $this->_extension = self::DEFAULT_EXTENSION;
        }
        $this->_kernelFolder = $kernelFolder . DIRECTORY_SEPARATOR . 'source';
        $this->_viewsFolder = ucfirst(strtolower($viewsFolder));
    }

    public function __get($name)
    {
        if (isset($this->_variables[$name])) {
            return $this->_variables[$name];
        } else {
            return null;
        }
    }

    public function __set($name, $value)
    {
        $this->_variables[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_variables[$name]);
    }

    public function __unset($name)
    {
        unset($this->_variables[$name]);
    }

    public function getVariablesArray()
    {
        return $this->_variables;
    }

    public function render($template)
    {
        $path = $this->_getViewFilePath($template);
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidViewFileException($path);
        }

        ob_start();
        extract($this->getVariablesArray());
        include $path;
        return ob_get_clean();
    }

    private function _getViewFilePath($template)
    {
        if (is_array($template)) {
            $controllerClassName = get_class($template[0]);
            $parts = explode('\\', $controllerClassName);
            $moduleFullName = $parts[0];
            $controllerPartIndex = count($parts) -1;
            $controllerName = strtolower(substr($parts[$controllerPartIndex], 0, strlen($parts[$controllerPartIndex]) - 10));
            $actionName = strtolower(substr($template[1], 0, strlen($template[1]) - 6));
        } elseif(is_string($template)) {
            $parts = explode('::', $template);
            $moduleFullName = ucfirst($parts[0]) . 'Module';
            $controllerName = strtolower($parts[1]);
            $actionName = strtolower($parts[2]);
        } else {
            throw new InvalidViewFileDefinitionException();
        }

        $pathParts = array(
            $this->_kernelFolder,
            $moduleFullName,
            $this->_viewsFolder,
            $controllerName,
            $actionName
        );
        $path = implode(DIRECTORY_SEPARATOR, $pathParts) . '.' . $this->_extension;
        return $path;
    }
}
