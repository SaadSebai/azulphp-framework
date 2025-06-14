<?php

namespace Azulphp\Routing;

use Azulphp\Core\App;
use Azulphp\Core\Config;
use Azulphp\Exceptions\ValidationException;
use Azulphp\Routing\Requests\FormRequest;
use Azulphp\Routing\Response\ResponseHandler;
use Azulphp\Routing\Response\ResponseStatus;
use Azulphp\Session;
use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;

/**
 * Manage the routing of the app.
 *
 * @method self get(string $uri, string $controller, string $method)
 * @method self post(string $uri, string $controller, string $method)
 * @method self put(string $uri, string $controller, string $method)
 * @method self patch(string $uri, string $controller, string $method)
 * @method self delete(string $uri, string $controller, string $method)
 */
class Router
{
    const string HOME = '/';

    /**
     * List of all routes.
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * List of global middlewares.
     *
     * @var string[]
     */
    protected $globalMiddlewares = [];

    protected array $methods = [
        'get', 'post', 'put', 'patch', 'delete'
    ];

    public function __construct(protected ?string $web, protected ?string $api)
    {
        $this->globalMiddlewares = array_keys((require base_path('config/middlewares.php'))['global']) ?? [];
    }

    /**
     * Create get, post, put, patch and delete route methods.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return $this
     */
    public function __call(string $method, array $arguments)
    {
        if (!in_array(strtolower($method), $this->methods, true)) {
            throw new BadMethodCallException("Method $method is not supported.");
        }

        if (count($arguments) < 3) {
            throw new InvalidArgumentException("Method $method requires URI, controller, and function name.");
        }

        return $this->add(strtoupper($method), $arguments[0], $arguments[1], $arguments[2]);
    }

    /**
     * Add new route to the routes lise.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  string  $controller
     * @param  string  $function
     * @return $this
     */
    public function add(string $method,string  $uri, string $controller, string $function): static
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'function' => $function,
            'method' => $method,
            'middleware' => []
        ];

        return $this;
    }

    /**
     * Set the middleware of the route.
     *
     * @param  $key
     * @return $this
     */
    public function middleware($key): static
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    /**
     * Load the controller and inject the necessary params in it.
     *
     * @return mixed|void
     * @throws ReflectionException
     * @throws \JsonException
     */
    public function route()
    {
        $uri = $this->getUri();
        $method = $this->getMethod();

        foreach ($this->routes as $route) {
            $matches = [];

            if ($this->matchUri($route['uri'], $uri, $matches) && $route['method'] === strtoupper($method)) {
                foreach ($this->globalMiddlewares as $middleware)
                {
                    Middleware::resolve($middleware, 'global');
                }

                foreach ($route['middleware'] as $middleware)
                {
                    Middleware::resolve($middleware);
                }

                // Injected Requests
                $requests = $this->paramsInject($route['controller'], $route['function'], $matches, FormRequest::class);

                $response = (new $route['controller'])->{$route['function']}(...$requests, ...$matches);

                return ResponseHandler::handle($response);
            }
        }

        abort();
    }

    /**
     * Return to the previous route.
     *
     * @return string
     */
    public function previousUrl(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? self::HOME;
    }

    /**
     * Create a table of the URI variables.
     *
     * @param  string  $routeUri
     * @param  string  $requestUri
     * @param  array  $matches
     * @return bool
     */
    private function matchUri(string $routeUri, string $requestUri, array &$matches): bool
    {
        preg_match_all('#\{(\w+)}#', $routeUri, $paramNames);
        $paramNames = $paramNames[1];

        $pattern = preg_replace('#\{(\w+)}#', '([\w-]+)', $routeUri);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $requestUri, $result))
        {
            array_shift($result);

            $matches = array_combine($paramNames, $result);
            return true;
        }

        return false;
    }

    /**
     * Create instance of the classes that we need to inject in the controller's method
     *
     * @param  string  $controller
     * @param  string  $method
     * @param  array  $args
     * @param  string|null  $parent
     * @return array
     * @throws ReflectionException
     */
    private function paramsInject(string $controller, string $method, array $args = [], ?string $parent = null): array
    {
        $instances = [];

        $refMethod = new ReflectionMethod($controller, $method);
        $params = $refMethod->getParameters();

        foreach ($params as $param) {
            $injectedType = $param->getType()->getName();
            if (is_subclass_of($injectedType, $parent))
                $instances[] = new $injectedType($args);
        }

        return $instances;
    }

    /**
     * Get the current route.
     *
     * @return false|array|int|string|null
     */
    public static function currentRoute(): false|array|int|string|null
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Get the route with query params.
     *
     * @param  string|null  $path
     * @param  array  $params
     * @return string
     */
    public static function routeWithCurrentParams(string $path = null, array $params = []): string
    {
        $current_route = static::currentRoute();
        $path ??= $current_route;

        if ($path === static::currentRoute())
            $params += $_GET;

        return $path .'?'. http_build_query($params);
    }

    /**
     * Require the necessary routes.
     *
     * @return $this
     */
    public function requireRoutes(): static
    {
        $router = $this;

        if ($this->web) require_once $this->web;
        if ($this->api) require_once $this->api;

        return $this;
    }

    /**
     * Get the current URI.
     *
     * @return mixed|string
     */
    public function getUri()
    {
        return parse_url($_SERVER['REQUEST_URI'])['path'] ?? '';
    }

    /**
     * Get the used method.
     *
     * @return mixed
     */
    public function getMethod(): mixed
    {
        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Redirect to route.
     *
     * @param  string  $path
     * @return void
     */
    public function redirect(string $path): void
    {
        header("location: {$path}");
        exit();
    }

    /**
     * Redirect to the previous route.
     *
     * @return void
     */
    public function back(): void
    {
        $this->redirect($this->previousUrl());
    }

    /**
     * Handle the request in the index.
     *
     * @return void
     * @throws ReflectionException
     */
    public function handleRequest(): void
    {
        Session::start();

        $this->requireRoutes();

        try {
            $this->route();
        }
        catch (ValidationException $exception) {
            Session::flash('errors', $exception->errors);
            Session::flash('old', $exception->old);

            $this->back();
        }
        catch (Exception $exception) {
            $config = App::resolve(Config::class);

            if($config->getConfig('production')) abort(ResponseStatus::SERVER_ERROR);
            else throw $exception;
        }

        register_shutdown_function(function () {
            Session::unflash();
        });
    }
}
