<?php

namespace Re2bit\Yii2Secrets;

use DomainException;
use ErrorException;
use Exception;
use Re2bit\Secrets\SodiumVault;
use Re2bit\Secrets\VaultInterface;
use SodiumException;
use yii\base\Component;

class Vault extends Component
{
    /** @var string */
    public $keysDir;

    /** @var string  */
    public $vaultClass = SodiumVault::class;

    /** @var VaultInterface */
    private $vault;

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        if ($this->vaultClass === null) {
            throw new DomainException('Vault Class is not defined, configure with parameter "vaultClass"');
        }

        if (!class_exists($this->vaultClass)) {
            throw new DomainException('Vault Class is not available');
        }

        if (!in_array(VaultInterface::class, class_implements($this->vaultClass), true)) {
            throw new DomainException('Vault Class does not implement "' . VaultInterface::class . '" Interface');
        }

        $vault = new $this->vaultClass($this->keysDir);

        if (!($vault instanceof VaultInterface)) {
            throw new DomainException('Vault Class does not implement "' . VaultInterface::class . '" Interface');
        }

        $this->vault = $vault;
    }

    /**
     * @param string $name
     * @throws Exception
     * @return string|null
     */
    public function reveal($name)
    {
        return $this->vault->reveal($name);
    }

    /**
     * @param bool $rotate
     * @return bool
     * @throws ErrorException
     * @throws SodiumException
     */
    public function generateKeys($rotate)
    {
       return $this->vault->generateKeys($rotate);
    }

    /**
     * Seal a name-value pair into the vault.
     *
     * @param string $name The name of the pair.
     * @param mixed $value The value of the pair.
     *
     * @return void
     */
    public function seal($name, $value)
    {
        $this->vault->seal($name, $value);
    }

    /**
     * Retrieves the last message from the vault.
     *
     * @return string The last message from the vault.
     */
    public function getLastMessage()
    {
       return $this->vault->getLastMessage();
    }

    /**
     * Lists all the items stored in the vault.
     *
     * @param bool $reveal Whether to reveal the items' details or not.
     *
     * @return array Returns an array containing the listed items.
     */
    public function listing(bool $reveal = false)
    {
        return $this->vault->listing($reveal);
    }

    /**
     * Removes an item from the vault.
     *
     * @param string $name The name of the item to be removed.
     * @return void
     */
    public function remove(string $name)
    {
        $this->vault->remove($name);
    }
}
