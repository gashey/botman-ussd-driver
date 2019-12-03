<?php

namespace Gashey\BotmanUssdDriver;

use BotMan\BotMan\Drivers\DriverManager;
use Gashey\BotmanUssdDriver\UssdDriver;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;


class UssdServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->isRunningInBotManStudio()) {
            $this->loadDrivers();
            $this->publishes([
                __DIR__ . '/../config/ussd.php' => config_path('botman/ussd.php'),
            ]);
            $this->mergeConfigFrom(__DIR__ . '/../config/ussd.php', 'botman.ussd');
        }
    }
    /**
     * Load BotMan drivers.
     */
    protected function loadDrivers()
    {
        DriverManager::loadDriver(UssdDriver::class);
    }
    /**
     * @return bool
     */
    protected function isRunningInBotManStudio()
    {
        return class_exists(StudioServiceProvider::class);
    }
}
