<?php

namespace Re2bit\Yii2Secrets\controllers;

use ErrorException;
use Re2bit\Yii2Secrets\Vault;
use SodiumException;
use yii\console\Controller;
use yii\console\widgets\Table;

/**
 * General Description
 */
class DefaultController extends Controller
{
    /**
     * @var Vault
     */
    private $vault;

    /**
     * @param mixed $id
     * @param mixed$module
     * @param Vault $vault
     * @param mixed[] $config
     */
    public function __construct($id, $module, Vault $vault, $config = [])
    {
        $this->vault = $vault;
        parent::__construct($id, $module, $config);
    }


    /**
     * The generate-keys command generates a new encryption key.
     *
     * If encryption keys already exist, the command must be called with
     * the '--rotate' option in order to override those keys and re-encrypt
     * existing secrets.
     *
     * @param bool $rotate
     * @throws ErrorException
     * @throws SodiumException
     * @return void
     */
    public function actionGenerateKeys(bool $rotate = false)
    {
        $this->vault->generateKeys($rotate);
        $this->stdout(($this->vault->getLastMessage() ?: '') . PHP_EOL);
    }

    /**
     * The set command sets a secret under the given name.
     *
     * There's no command to rename secrets, so you'll need to create a new secret and remove the old one.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function actionSet(string $name, string $value)
    {
        $this->vault->seal($name, $value);
        $this->stdout(($this->vault->getLastMessage() ?: '') . PHP_EOL);
    }

    /**
     * The list command shows all secrets.
     *
     * Everybody is allowed to list the secrets names with the command "list".
     * If you have the decryption key you can also reveal the secrets' values by passing the --reveal option:
     *
     * @return void
     */
    public function actionList(bool $reveal = false)
    {
        $secrets = $this->vault->listing($reveal);
        $table = new Table();
        $table->setHeaders(['Name', 'Value']);
        $secrets = array_map(
            static function ($key, $value) {
                return [$key, $value];
            },
            array_keys($secrets),
            array_values($secrets)
        );

        $table->setRows($secrets);
        $this->stdout($table->run());
    }

    /**
     * The remove command removes a given secret by name.
     *
     * This command allows users to delete a secret by providing the secret's name as a parameter.
     * The secret will be permanently removed from the Vault and cannot be recovered.
     *
     * @param string $name The name of the secret to be removed.
     *
     * @return void
     */
    public function actionRemove(string $name)
    {
        $this->vault->remove($name);
        $this->stdout(($this->vault->getLastMessage() ?: '') . PHP_EOL);
    }
}
