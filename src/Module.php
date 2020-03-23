<?php

namespace SamIT\Yii2\StaticAssets;

use yii\base\InvalidConfigException;
use yii\console\Application;
use yii\web\AssetBundle;
use yii\web\AssetManager;

/**
 * Class Module
 * @package SamIT\Yii2\StaticAssets
 * @property AssetManager $assetManager
 */
class Module extends \yii\base\Module
{
    /**
     * @var string The base URL for the assets. This can include a hostname.
     */
    public $baseUrl;

    /**
     * @var string The class of the default asset bundle. This will be used to look for files like /favicon.ico
     */
    public $defaultBundle;


    /** @var array List of fnmatch patterns with file names to skip. */
    public $excludedPatterns = [];


    public function init()
    {
        parent::init();
        $assetManagerConfig = $this->getComponents(true)['assetManager']
            ?? $this->module->getComponents(true)['assetManager']
            ?? [];
        $assetManagerConfig['hashCallback'] = self::hashCallback();
        if ($this->module instanceof Application) {
            if (!isset(\Yii::$aliases['@webroot'])) {
                \Yii::setAlias('@webroot', \sys_get_temp_dir());
            }
            $assetManagerConfig['basePath'] = \sys_get_temp_dir();
        }
        $this->set('assetManager', $assetManagerConfig);

    }

    public static function hashCallback(): \Closure
    {
        return function($path) {

            $dir = \is_file($path) ? \dirname($path) : $path;
            $relativePath = \strtr($dir, [
                \realpath(\Yii::getAlias('@app')) => 'app',
                \realpath(\Yii::getAlias('@vendor')) => 'vendor'
            ]);
            return \strtr(\trim($relativePath, '/'), ['/' => '_']);
        };
    }


}