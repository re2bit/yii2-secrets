<?php

namespace Re2bit\Yii2Secrets\Tests;

use Re2bit\Yii2Secrets\controllers\DefaultController;
use Re2bit\Yii2Secrets\SecretsModule;
use Re2bit\Yii2Secrets\Vault;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\base\Module;
use yii\console\Exception;
use yii\helpers\Console;

class SecretsCommandTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function _setUp()
    {
        $this->mockSecretApplication();
    }

    /**
     * Creates controller instance.
     * @return BufferedDefaultController
     */
    protected function createController(Vault $vaultMock)
    {
        $module = $this->getMockBuilder(Module::class)
            ->setMethods(['fake'])
            ->setConstructorArgs(['console'])
            ->getMock();


        return new BufferedDefaultController('secret', $module, $vaultMock);
    }

    /**
     * Emulates running controller action.
     * @param string $actionID id of action to be run.
     * @param mixed[] $actionParams action arguments.
     * @throws InvalidConfigException
     * @return string command output.
     */
    protected function runControllerAction($actionID, Vault $vaultMock, $actionParams = [])
    {
        $controller = $this->createController($vaultMock);
        $action = $controller->createAction($actionID);
        static::assertNotNull($action);
        $action->runWithParams($actionParams);
        return $controller->flushStdOutBuffer();
    }

    /**
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     * @return void
     */
    public function testGeneralHelp()
    {
        Yii::$app->runAction('help', ['secrets/default/']);
        $output = Console::flushStdOutBuffer();

        $this->assertEqualsWithoutLE(/** @lang text */ <<<'STRING'

DESCRIPTION

General Description


SUB-COMMANDS

- secrets/default/generate-keys  The generate-keys command generates a new encryption key.
- secrets/default/list           The list command shows all secrets.
- secrets/default/remove         The remove command removes a given secret by name.
- secrets/default/set            The set command sets a secret under the given name.

To see the detailed information about individual sub-commands, enter:

  bootstrap.php help <sub-command>


STRING
            , $output);
    }

    /**
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     * @return void
     */
    public function testGenerateKeysHelp()
    {
        Yii::$app->runAction('help', ['secrets/default/generate-keys']);
        $output = Console::flushStdOutBuffer();
        $versionString = PHP_VERSION_ID;
        $this->assertEqualsWithoutLE(<<<'STRING'

DESCRIPTION

The generate-keys command generates a new encryption key.

If encryption keys already exist, the command must be called with
the '--rotate' option in order to override those keys and re-encrypt
existing secrets.


USAGE

bootstrap.php secrets/default/generate-keys [rotate] [...options...]

- rotate: boolean, 0 or 1 (defaults to 0)


OPTIONS

--appconfig: string
  custom application configuration file path.
  If not set, default application configuration is used.

--color: boolean, 0 or 1
  whether to enable ANSI color in the output.
  If not set, ANSI color will only be enabled for terminals that support it.

--help, -h: boolean, 0 or 1 (defaults to 0)
  whether to display help information about current command.

--interactive: boolean, 0 or 1 (defaults to 1)
  whether to run the command interactively.

--silent-exit-on-exception: boolean, 0 or 1
  if true - script finish with `ExitCode::OK` in case of exception.
  false - `ExitCode::UNSPECIFIED_ERROR`.
  Default: `YII_ENV_TEST`


STRING
            , $output);
    }

    /**
     * @throws InvalidConfigException
     * @return void
     */
    public function testGenerateKeys()
    {
        $this->mockSecretApplication();

        $vaultMock = $this->getMockBuilder(Vault::class)
            ->setMethods(['generateKeys', 'getLastMessage'])
            ->getMock();

        $vaultMock->method('getLastMessage')->willReturn("Yii2 Vault\n");

        $result = Console::stripAnsiFormat(
            $this->runControllerAction(
                'generate-keys',
                $vaultMock,
                []
            )
        );
        $this->assertEqualsWithoutLE(<<<'STRING'
Yii2 Vault

STRING
            , $result);
    }

    /**
     * @throws InvalidConfigException
     * @return void
     */
    public function testGenerateKeysRotate()
    {
        $this->mockSecretApplication();

        $vaultMock = $this->getMockBuilder(Vault::class)
            ->setMethods(['generateKeys', 'getLastMessage'])
            ->getMock();

        $vaultMock->method('getLastMessage')
            ->willReturn("Yii2 Vault\n");

        $vaultMock->method('generateKeys')
            ->with(true);

        $result = Console::stripAnsiFormat(
            $this->runControllerAction(
                'generate-keys',
                $vaultMock,
                [1]
            )
        );
        $this->assertEqualsWithoutLE(<<<'STRING'
Yii2 Vault

STRING
            , $result);
    }

    /**
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     * @return void
     */
    public function testSetHelp()
    {
        Yii::$app->runAction('help', ['secrets/default/set']);
        $output = Console::flushStdOutBuffer();

        $this->assertEqualsWithoutLE(/** @lang text */ <<<'STRING'

DESCRIPTION

The set command sets a secret under the given name.

There's no command to rename secrets, so you'll need to create a new secret and remove the old one.


USAGE

bootstrap.php secrets/default/set <name> <value> [...options...]

- name (required): string

- value (required): string


OPTIONS

--appconfig: string
  custom application configuration file path.
  If not set, default application configuration is used.

--color: boolean, 0 or 1
  whether to enable ANSI color in the output.
  If not set, ANSI color will only be enabled for terminals that support it.

--help, -h: boolean, 0 or 1 (defaults to 0)
  whether to display help information about current command.

--interactive: boolean, 0 or 1 (defaults to 1)
  whether to run the command interactively.

--silent-exit-on-exception: boolean, 0 or 1
  if true - script finish with `ExitCode::OK` in case of exception.
  false - `ExitCode::UNSPECIFIED_ERROR`.
  Default: `YII_ENV_TEST`


STRING
            , $output);
    }

    /**
     * @throws InvalidConfigException
     * @return void
     */
    public function testSet()
    {
        $this->mockSecretApplication();

        $vaultMock = $this->getMockBuilder(Vault::class)
            ->setMethods(['seal', 'getLastMessage'])
            ->getMock();

        $vaultMock->method('getLastMessage')
            ->willReturn("Yii2 Vault Set\n");

        $vaultMock->method('seal')
            ->with('test', 'test');

        $result = Console::stripAnsiFormat(
            $this->runControllerAction(
                'set',
                $vaultMock,
                ['test', 'test']
            )
        );
        $this->assertEqualsWithoutLE(<<<'STRING'
Yii2 Vault Set

STRING
            , $result);
    }

    /**
     * @throws InvalidRouteException
     * @throws Exception
     * @return void
     */
    public function testListHelp()
    {
        Yii::$app->runAction('help', ['secrets/default/list']);
        $output = Console::flushStdOutBuffer();

        $this->assertEqualsWithoutLE(/** @lang text */ <<<'STRING'

DESCRIPTION

The list command shows all secrets.

Everybody is allowed to list the secrets names with the command "list".
If you have the decryption key you can also reveal the secrets' values by passing the --reveal option:


USAGE

bootstrap.php secrets/default/list [reveal] [...options...]

- reveal: boolean, 0 or 1 (defaults to 0)


OPTIONS

--appconfig: string
  custom application configuration file path.
  If not set, default application configuration is used.

--color: boolean, 0 or 1
  whether to enable ANSI color in the output.
  If not set, ANSI color will only be enabled for terminals that support it.

--help, -h: boolean, 0 or 1 (defaults to 0)
  whether to display help information about current command.

--interactive: boolean, 0 or 1 (defaults to 1)
  whether to run the command interactively.

--silent-exit-on-exception: boolean, 0 or 1
  if true - script finish with `ExitCode::OK` in case of exception.
  false - `ExitCode::UNSPECIFIED_ERROR`.
  Default: `YII_ENV_TEST`


STRING
            , $output);
    }

    /**
     * @throws InvalidConfigException
     * @return void
     */
    public function testList()
    {
        $this->mockSecretApplication();

        $vaultMock = $this->getMockBuilder(Vault::class)
            ->setMethods(['listing', 'getLastMessage'])
            ->getMock();

        $vaultMock->method('listing')->willReturn([
            'SECRET1' => '******',
            'SECRET2' => '******',
        ]);

        $vaultMock->method('getLastMessage')
            ->willReturn("Yii2 Vault Set\n");

        $result = Console::stripAnsiFormat(
            $this->runControllerAction(
                'list',
                $vaultMock
            )
        );
        $this->assertEqualsWithoutLE(<<<'STRING'
╔═════════╤════════╗
║ Name    │ Value  ║
╟─────────┼────────╢
║ SECRET1 │ ****** ║
╟─────────┼────────╢
║ SECRET2 │ ****** ║
╚═════════╧════════╝

STRING
            , $result);
    }

    /**
     * @return void
     */
    protected function mockSecretApplication()
    {
        $this->mockApplication([
            'enableCoreCommands' => false,
            'modules'            => [
                'secrets' => [
                    'class' => SecretsModule::class,
                ],
            ],
        ]);

        Yii::$classMap[Console::class] = "@" . Console::class;
        Yii::setAlias("@" . Console::class, __DIR__ . '/BufferedConsole.php');
    }
}

/**
 * Class BufferedDefaultController
 *
 * This class extends the DefaultController class and provides a buffer for output storage.
 */
class BufferedDefaultController extends DefaultController
{
    /**
     * @var string output buffer.
     */
    private $stdOutBuffer = '';

    /**
     * @param $string
     * @return bool|int
     */
    public function stdout($string)
    {
        $this->stdOutBuffer .= $string;
        return true;
    }

    /**
     * @return string
     */
    public function flushStdOutBuffer()
    {
        $result = $this->stdOutBuffer;
        $this->stdOutBuffer = '';
        return $result;
    }
}
