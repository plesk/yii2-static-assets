<?php

use Codeception\Test\Unit;

class ModuleBootstrapTest extends Unit
{
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testModuleLoaded()
    {
        $modules = \Yii::$app->getModules();
        $this->assertNotEmpty($modules);
    }
}