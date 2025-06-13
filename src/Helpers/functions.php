<?php

use Azulphp\Helpers\ResponseStatus;
use Azulphp\Routing\Router;
use Azulphp\Session;

function dd(mixed $value): void
{
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? null;

    echo "<pre style='background:#f5f5f5;padding:10px;border-left:5px solid #ccc;'>";

    if ($backtrace) {
        $class = $backtrace['class'] ?? '';
        $line = $backtrace['line'] ?? '';

        echo "$class line $line\n\n";
    }

    var_dump($value);
    echo "</pre>";

    die();
}

function base_path(string $path): string
{
    return BASE_PATH . $path;
}

function view(string $path, array $attributes = [], ?string $layout = null): bool
{
    extract($attributes);

    if ($layout)
    {
        ob_start();
        require base_path("views/{$path}.view.php");
        $slot = ob_get_clean();

        require base_path("views/layouts/$layout.php");
    }
    else
    {
        require base_path("views/{$path}.view.php");
    }

    return true;
}

function partial(string $path): void
{
    require base_path("views/$path.php");
}

function old(string $key, ?string $default = ''): string
{
    $value = Session::get('old')[$key] ?? $default;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function abort($code = ResponseStatus::NOT_FOUND): void
{
    http_response_code($code);

    view(path: "default/{$code}");

    die();
}

function routeWithCurrentParams(string $path = null, array $params = []): string
{
    return Router::routeWithCurrentParams($path, $params);
}

function currentRoute(): false|array|int|string|null
{
    return Router::currentRoute();
}

function phpTypes(): array
{
    return [
        'int', 'integer',
        'float', 'double',
        'string',
        'bool', 'boolean',
        'array',
        'object',
        'callable',
        'iterable',
        'mixed',
        'void',
        'null',
        'resource',
        'never',
        'false', 'true',
    ];
}

function valideObjectType(string $type): bool
{
    return class_exists($type) || interface_exists($type);
}

/**
 * Check if the type is a valid php type or class/interface
 *
 * @param  string  $type
 * @return bool
 */
function valideVarType(string $type): bool
{
    return in_array($type, phpTypes()) || valideObjectType($type);
}