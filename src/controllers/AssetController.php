<?php

namespace SamIT\Yii2\StaticAssets\controllers;

use SamIT\Yii2\StaticAssets\helpers\AssetHelper;
use SamIT\Yii2\StaticAssets\Module;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\web\AssetBundle;
use yii\web\AssetManager;

/**
 * Class AssetController
 * @package SamIT\Yii2\StaticAssets\controllers
 * @property Module $module
 */
class AssetController extends Controller
{
    /** @var string $defaultBundle */
    public $defaultBundle;

    /** @var string $baseUrl */
    public $baseUrl;

    /** @var array List of fnmatch patterns with file names to skip. */
    public $excludedPatterns = [];

    /**
     * Initialize controller with module configuration by default
     */
    public function init()
    {
        parent::init();

        $this->defaultBundle = $this->module->defaultBundle;
        $this->baseUrl = $this->module->baseUrl;
        $this->excludedPatterns = $this->module->excludedPatterns;
    }

    /**
     * @param string $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            [
                'defaultBundle',
                'baseUrl',
                'excludedPatterns'
            ]
        );
    }

    /**
     * @param $path
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPublish($path)
    {
        $this->stdout("Publishing default bundle to webroot...\n", Console::FG_CYAN);

        if (isset($this->defaultBundle)) {
            $class = $this->defaultBundle;
            /** @var AssetBundle $bundle */
            $bundle = new $class;
            $bundle->publish($this->getAssetManager($path));
            $this->stdout("Copying {$bundle->basePath} to {$path}/default...\n", Console::FG_CYAN);
            echo "$bundle->basePath";

            \passthru("ls -la {$bundle->basePath}");
            FileHelper::copyDirectory($bundle->basePath, "$path/default");

            $this->stdout("OK\n", Console::FG_GREEN);
        } else {
            @\mkdir("$path/default");
        }

        $assetManager = $this->getAssetManager($path);
        $this->stdout("Publishing application assets... ", Console::FG_CYAN);
        AssetHelper::publishAssets($assetManager, \Yii::getAlias('@app'), $this->excludedPatterns);
        $this->stdout("OK\n", Console::FG_GREEN);

        $this->stdout("Publishing vendor assets... ", Console::FG_CYAN);
        AssetHelper::publishAssets($assetManager, \Yii::getAlias('@vendor'), $this->excludedPatterns);
        $this->stdout("OK\n", Console::FG_GREEN);
    }

    /**
     * @param $fullPath
     * @return AssetManager
     * @throws \yii\base\InvalidConfigException
     */
    protected function getAssetManager($fullPath): AssetManager
    {
        $this->stdout("Creating asset path: $fullPath... ", Console::FG_CYAN);

        if (!\is_dir($fullPath)) {
            @\mkdir($fullPath, 0777, true);
        }

        $this->stdout("OK\n", Console::FG_GREEN);

        // Override some configuration.
        $assetManagerConfig = $this->module->getComponents()['assetManager'];
        $assetManagerConfig['basePath'] = $fullPath;
        $assetManagerConfig['baseUrl'] = $this->baseUrl;
        $this->module->set('assetManager', $assetManagerConfig);

        return $this->module->get('assetManager');
    }
}