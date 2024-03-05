<?php

namespace Re2bit\Yii2Secrets\Tests;

use Re2bit\Yii2Secrets\Vault;
use Yii;
use yii\base\InvalidConfigException;

class ExtensionTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     * @return void
     */
    public function testVaultServiceIsAvailableThroughDi()
    {
        $vault = Yii::$app->get('vault');
        static::assertInstanceOf(Vault::class, $vault);
    }
}
