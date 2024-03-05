<?php

namespace Re2bit\Yii2Secrets;

use Exception;

/**
 * Class ConfigDecryptAdapter
 *
 * Provides functionality for parsing and decrypting encrypted configuration values within a Yii application.
 * The class iterates over the configuration array, identifying strings that match a specific encrypted pattern.
 * These strings are then decrypted using the Vault service. The class is designed to work with Yii2 framework
 * and integrates seamlessly into its configuration handling process.
 *
 * Usage involves passing the Yii configuration array to this class, which then processes each element,
 * decrypting values as necessary and returning a configuration array with plaintext values.
 */
class ConfigDecryptAdapter
{
    const VAULT_REGEX = '/%vault\(([^)]+)\)%/';

    /**
     * Vault instance used for decrypting the encrypted configuration values.
     *
     * @var Vault
     */
    private $vault;

    /**
     * The enclosure character or string that identifies encrypted strings in the config.
     *
     * @var string
     */
    private $enclosure = '%';

    /**
     * Constructor for ConfigDecryptAdapter.
     *
     * Initializes the adapter with a Vault instance to provide decryption functionality for
     * encrypted configuration values.
     *
     * @param Vault $vault An instance of Vault used for the decryption process.
     */
    public function __construct(Vault $vault)
    {
        $this->vault = $vault;
    }


    /**
     * Parses and decrypts the Yii configuration array.
     *
     * Iterates recursively over each element of the configuration array, identifying and decrypting
     * any encrypted strings that are enclosed with a specific pattern. The method modifies the array in place,
     * replacing encrypted strings with their decrypted counterparts.
     *
     * @param array<string, mixed> $config The Yii configuration array to be parsed and decrypted.
     * @throws Exception Throws an exception if decryption fails or any other unforeseen error occurs.
     * @return void
     */
    public function parse(&$config)
    {
        array_walk_recursive($config, function (&$item, $key) {
            if (!$this->shouldParseItem($item)) {
                return;
            }

            $regex = static::VAULT_REGEX;

            if (preg_match($regex, $item, $matches)) {
                $item = $this->vault->reveal($matches[1]);
            }
        });
    }

    /**
     * Checks if a given item should be parsed.
     *
     * Determines whether the provided item is a string enclosed within the specified enclosure character(s).
     * This helps identify encrypted strings in the configuration array.
     *
     * @param mixed $item The item to check.
     * @return bool True if the item should be parsed, false otherwise.
     */
    private function shouldParseItem($item)
    {
        if (!is_string($item) || strlen($item) === 0) {
            return false;
        }

        // Check if the string starts and ends with the specified enclosure
        return strpos($item, $this->enclosure) === 0 && $item[strlen($item) - 1] === $this->enclosure;
    }
}
