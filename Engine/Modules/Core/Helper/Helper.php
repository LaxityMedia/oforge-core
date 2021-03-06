<?php

namespace Oforge\Engine\Modules\Core\Helper;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\InvalidClassException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Helper {
    protected static $omitFolders = [".", "..", "vendor", "var"];

    /**
     * Get all directories recursive based on the defined path, except the folders to omit
     *
     * @param string $path
     *
     * @return array
     */
    public static function getAllDirs(string $path) {
        $result = [];
        $tmp    = scandir($path);

        foreach ($tmp as $dir) {
            $v = $path . DIRECTORY_SEPARATOR . $dir;

            if (is_dir($v) && !in_array($dir, self::$omitFolders)) {
                array_push($result, $v);
                $result = array_merge($result, Helper::getAllDirs($v));
            }
        }

        return $result;
    }

    /**
     * get all Bootstrap.php files inside a defined path
     *
     * @param string $path
     *
     * @return array
     */
    public static function getBootstrapFiles(string $path) {
        $result = [];

        $cacheFile = ROOT_PATH . Statics::CACHE_DIR . DIRECTORY_SEPARATOR . basename($path) . ".cache";

        if (file_exists($cacheFile) && Oforge()->Settings()->get("mode") != "development") {
            $result = unserialize(file_get_contents($cacheFile));
        } else {
            $result = self::findFilesInPath($path, "bootstrap.php");
            file_put_contents($cacheFile, serialize($result));
        }

        return $result;
    }

    /**
     * get all template files inside a defined path
     *
     * @param string $path
     *
     * @return array
     */
    public static function getTemplateFiles(string $path) {

        $cacheFile = ROOT_PATH . Statics::CACHE_DIR . DIRECTORY_SEPARATOR . basename($path) . ".cache";

        if (file_exists($cacheFile) && Oforge()->Settings()->get("mode") != "development") {
            $result = unserialize(file_get_contents($cacheFile));
        } else {
            $result = self::findFilesInPath($path, "template.php");
            file_put_contents($cacheFile, serialize($result));
        }

        return $result;
    }

    /**
     * search for filename inside a path
     *
     * @param $path string
     * @param $fileName string
     *
     * @return array
     */
    private static function findFilesInPath($path, $fileName) {
        $result = [];

        $path = realpath($path);
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $f) {
            if (strtolower($f->getFileName()) == $fileName) {

                $classpath = str_replace($path . DIRECTORY_SEPARATOR, "", $f->getPath());
                $classpath = str_replace(".php", "", $classpath);

                $result[$classpath] = $f->getPath() . DIRECTORY_SEPARATOR . $f->getFileName();
            }
        }

        return $result;
    }

    /**
     * get the instance of a module/plugin based on the bootstrap file
     *
     * @param $pluginName
     *
     * @return mixed
     * @throws InvalidClassException
     */
    public static function getBootstrapInstance($pluginName) : AbstractBootstrap {
        $className = $pluginName . "\\Bootstrap";

        if (is_subclass_of($className, AbstractBootstrap::class)) {
            return new $className();
        }
        throw new InvalidClassException($className, AbstractBootstrap::class);
    }

    /**
     * @return string
     */
    public static function generateGuid() {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535));
    }

    /**
     * Get some metadata from a file.
     * If the file is a psr4 conform class, the function finds the namespace and classname and returns them as an array.
     * Otherwise null.
     *
     * @param string $fileName
     *
     * @return array|null
     */
    public static function getFileMeta(string $fileName) {

        $file = fopen($fileName, 'r');
        $class = '';
        $namespace = '';
        $buffer = '';
        $i = 0;
        $fileMeta = [];

        while (!$class) {
            if (feof($file)) break;

            $buffer .= fread($file, 512);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false) continue;

            for (;$i < count($tokens); $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j=$i+1;$j<count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1;$j<count($tokens);$j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }

        if (StringHelper::startsWith($namespace, "\\")) {
            $namespace = ltrim($namespace, "\\");
        }

        if (strlen($class) > 0) {
            $fileMeta['class_name'] = $class;
        }

        if (strlen($namespace) > 0) {
            $fileMeta['namespace'] = $namespace;
        }

        if (sizeof($fileMeta) < 2) {
            return null;
        }

        return $fileMeta;
    }
}
