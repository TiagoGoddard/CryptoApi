<?php namespace TiagoGoddard\Cryptoapi;

use TiagoGoddard\Cryptoapi\Commands\GenerateKeyPairCommand;
use TiagoGoddard\Cryptoapi\Commands\TestCommand;
use TiagoGoddard\Cryptoapi\Cryptography\DecryptedInput;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Whoops\Example\Exception;

class CryptoapiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('tiagogoddard/cryptoapi');
	}

    /**
     * Register the service provider.
     *
     * @throws \InvalidArgumentException
     * @return void
     */
	public function register()
	{
        $this->registerKeyGenerators();

        $this->registerCommands();

        $this->registerCryptography();
	}

    protected function registerKeyGenerators() {
        \App::bind(
            'cryptoapi.generator.php',
            'TiagoGoddard\\Cryptoapi\\KeyGenerators\\PhpKeyGenerator'
        );

        \App::bind(
            'cryptoapi.generator.openssl',
            'TiagoGoddard\\Cryptoapi\\KeyGenerators\\OpenSslKeyGenerator'
        );
    }

    protected function registerCommands() {
        $this->app['cryptoapi.generatekeys'] = $this->app->share(function($app) {
            return new GenerateKeyPairCommand();
        });

        $this->commands('cryptoapi.generatekeys');
    }

    protected function registerCryptography() {
        $this->app['CryptographyInterface'] = $this->app->share(function() {
           return new Cryptography\RsaAesCryptography;
        });

        $this->app['decryptedinput'] = $this->app->share(function() {
            return new DecryptedInput();
        });

        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('DecryptedInput', 'TiagoGoddard\Cryptoapi\Facades\DecryptedInput');
        $aliasLoader->alias('RsaAesControllerTrait', 'TiagoGoddard\Cryptoapi\Traits\RsaAesControllerTrait');
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
            'cryptoapi.generator.php',
            'cryptoapi.generator.openssl',
            'cryptoapi.generatekeys',
            'CryptographyInterface',
            'decryptedinput',
        );
	}

}
