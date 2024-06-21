# PHP 7.0 Compatible Secrets Management Library Yii Module

This Module offers a secure way to manage sensitive information, inspired by the Symfony Secrets feature. 
It's designed for PHP 7.0 compatibility, leveraging modern cryptography with the Sodium extension or `paragonie/sodium_compat`.

## Requirements

- PHP 7.0 or newer
- Sodium PHP extension or `paragonie/sodium_compat`
- Yii 2


### Setting Up

1. **Install Dependencies**: Make sure the Sodium extension is enabled in your PHP environment, or install `paragonie/sodium_compat` if it isn't already.

2. **Configure Yii2 Application**: Modify your Yii2 application configuration to include the Secrets module. Here’s how you can set it up:

    - **Update Composer**:
      Ensure your `composer.json` file includes the necessary libraries and update your project dependencies:
      ```bash
      composer require paragonie/sodium_compat
      ```
      This will install the library required for compatibility with PHP 7.0 if you do not have the Sodium PHP extension.

    - **Modify Config Files**:
      In your Yii2 application config file (usually `config/main.php` or `config/web.php` for web applications), add the following configurations to integrate the Vault component which manages the secrets:
      ```php
      'components' => [
          'cache' => [
              'class' => 'yii\caching\FileCache',
          ],
          'vault' => [
              'class' => 'Re2bit\Yii2Secrets\Vault',
          ],
      ],
      'container' => [
          'definitions' => [
              'Re2bit\Yii2Secrets\Vault' => [
                  'keysDir' => '@app/config/secrets', // adjust the path as needed
              ],
          ],
      ],
      ```

    - **Adjust Entry Points**:
      In the entry point of your application, such as `index.php` for web applications or `yii` script for console applications, initialize the Vault and ConfigDecryptAdapter:
      ```php
      $vault = new Vault(['keysDir' => __DIR__ . '/config/secrets']);
      $configDecryptAdapter = new ConfigDecryptAdapter($vault);
      $configDecryptAdapter->parse($config);
      ```


This setup ensures that your Yii2 application securely manages secrets, leveraging modern cryptography standards, even 
if running under PHP 7.0.

### Using Secrets in Configuration

To include secrets in your configuration, use the `%vault(secretName)%` format. When the configuration is parsed, 
these placeholders will be replaced with their decrypted values.

Example:

```php
$config = [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=mydatabase',
            'username' => '%vault(DB_USERNAME)%',
            'password' => '%vault(DB_PASSWORD)%',
        ],
    ],
];
```

In this example, `%vault(DB_USERNAME)%` and `%vault(DB_PASSWORD)%` will be replaced by their respective decrypted values
during the configuration parsing process.

## License

This package is available under MIT.

## Acknowledgments

This library is based and inspired by the work of:

- Tobias Schultze
- Jérémy Derussé
- Nicolas Grekas

from the Symfony framework. It has been adapted for PHP 7.0 with a focus on using Sodium for encryption.
