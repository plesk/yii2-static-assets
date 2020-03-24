<?php

use Codeception\Test\Unit;

class ModuleBootstrapTest extends Unit
{
    public function testModuleLoaded()
    {
        $modules = Yii::$app->getModules();
        $this->assertNotEmpty($modules);
    }
}