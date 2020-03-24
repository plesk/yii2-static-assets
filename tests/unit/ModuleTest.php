<?php

class ModuleTest extends \Codeception\Test\Unit
{
    /**
     * @var \SamIT\Yii2\StaticAssets\Module
     */
    protected $module;

    public function _before()
    {
        parent::_before();

        $this->module = Yii::$app->getModule('staticAssets');
        $this->assertInstanceOf(\SamIT\Yii2\StaticAssets\Module::class, $this->module);
    }

}
