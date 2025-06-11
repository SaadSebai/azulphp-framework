<?php

namespace Azulphp;

use Azulphp\Requests\FormRequest;
use BadMethodCallException;
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

    public function __construct()
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
     * @param  $uri
     * @param  $method
     * @return mixed|void
     * @throws ReflectionException
     */
    public function route($uri, $method)
    {
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

                return (new $route['controller'])->{$route['function']}(...$requests, ...$matches);
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
        return $_SERVER['HTTP_REFERER'] ?? static::home();
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
     * Redirect to the home page.
     *
     * @return string
     */
    public static function home(): string
    {
        return Session::has('user') ? '/home' : '/';
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
}
