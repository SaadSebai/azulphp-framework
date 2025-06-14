<?php

namespace Azulphp\Core;

use Azulphp\Routing\Router;

class AppBuilder
{
    protected Container $container;
    protected array $providers;
    protected Router $router;

    public function __construct()
    {
        $this->container = new Container();
        Env::set(BASE_PATH);
    }

    /**
     * Create the service providers.
     *
     * @param  array<string>  $providers
     * @return static
     */
    public function setProviders(array $providers): static
    {
        foreach ($providers as $provider)
        {
            $this->providers[] = new $provider($this->container);
        }

        return $this;
    }

    /**
     * Create the router instance.
     *
     * @param  string|null  $web
     * @param  string|null  $api
     * @return $this
     */
    public function setRouting(?string $web = null, ?string $api = null): static
    {
        $this->router = new Router($web, $api);

        return $this;
    }

    /**
     * Lunch the app and set the container.
     *
     * @return $this
     */
    public function create(): static
    {
        $this->luchProviders();
        App::setContainer($this->container);

        return $this;
    }

    /**
     * Call the register and boot methods in the service providers.
     *
     * @return void
     */
    public function luchProviders(): void
    {
        foreach ($this->providers as $provider)
        {
            $provider->register();
        }

        foreach ($this->providers as $provider)
        {
            $provider->boot();
        }
    }

    /**
     * Get the Router instance.
     *
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}