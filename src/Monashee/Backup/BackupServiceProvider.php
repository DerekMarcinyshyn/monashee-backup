<?php namespace Monashee\Backup;

use Illuminate\Support\ServiceProvider;

class BackupServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('monashee/backup');

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerCommands();
        $this->registerEventListeners();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
            'command.monashee.backup'
        );
	}

    /**
     * Register the events
     */
    private function registerEventListeners()
    {
        \Event::listen('MonasheeBackupError', function($message) {
            echo "\033[0;31m".$message."\033[0m\n";
        });

        \Event::listen('MonasheeBackupInfo', function($message) {
            echo "\033[0;36m".$message."\033[0m\n\n";
        });
    }

    /**
     * Register the commands
     */
    private function registerCommands()
    {
        // monashee:backup
        $this->app['command.monashee.backup'] = $this->app->share(function() {
            return $this->app->make('Monashee\Backup\Commands\MonasheeBackup');
        });

        $this->commands('command.monashee.backup');
    }
}
