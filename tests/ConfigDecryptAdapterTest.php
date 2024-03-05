<?php

namespace Re2bit\Yii2Secrets\Tests;

use Re2bit\Yii2Secrets\ConfigDecryptAdapter;
use Re2bit\Yii2Secrets\Vault;

class ConfigDecryptAdapterTest extends TestCase
{
    /**
     * @throws \Exception
     * @return void
     */
    public function testParseConfig()
    {
        $vaultMock = $this->createMock(Vault::class);
        $vaultMock->method('reveal')->with('test123')->willReturn('decoded-test123');
        $configAdapter = new ConfigDecryptAdapter($vaultMock);


        $config = [
            'components' => [
                'name' => [
                    'secretPw' => '%vault(test123)%',
                ],
            ],
        ];

        $configAdapter->parse($config);

        static::assertEquals('decoded-test123', $config['components']['name']['secretPw']);
    }
}
