<?php

namespace Re2bit\Yii2Secrets\Tests;

use Re2bit\Secrets\SodiumVault;
use Re2bit\Yii2Secrets\Vault;
use ReflectionClass;
use yii\console\Application;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\web\Session;

class TestCase extends PhpUnitCompatibilityLayer
{
    /**
     * @return void
     */
    protected function _setUp()
    {
        $this->mockApplication();
    }

    /**
     * @return void
     */
    protected function _tearDown()
    {
        $this->destroyApplication();
    }

    /**
     * @param array<string, mixed> $config
     * @param string $appClass
     * @return void
     */
    protected function mockApplication($config = [], $appClass = Application::class)
    {
        new $appClass(ArrayHelper::merge([
            'id'         => 'testapp',
            'basePath'   => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => Connection::class,
                    'dsn'   => 'sqlite::memory:',
                ],
                'session' => [
                    'class' => Session::class,
                ],
                'vault' => [
                    'class'      => Vault::class,
                    'vaultClass' => SodiumVault::class,
                    'keysDir'    => __DIR__ . '/secretKeys',
                ],
            ],
        ], $config));
    }

    /**
     * @return string
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    /**
     * @return void
     */
    protected function destroyApplication()
    {
        $reflectionClass = new ReflectionClass('Yii');
        $property = $reflectionClass->getProperty('app');
        $property->setAccessible(true);
        $property->setValue(null, null);

    }

    /**
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @return void
     */
    protected function assertEqualsWithoutLE($expected, $actual, $message = '')
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        $this->assertEquals($expected, $actual, $message);
    }
}
