<?php

use Azulphp\Routing\Response\ResponseStatus;
use Azulphp\Routing\Router;
use Azulphp\Session;

function dd(mixed $value): void
{
    static $called = false;
    if ($called) {
        // Prevent recursive calls
        return;
    }
    $called = true;

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? null;

    echo "<pre style='background:#f5f5f5;padding:10px;border-left:5px solid #ccc;'>";

    if ($backtrace) {
        $class = $backtrace['class'] ?? '';
        $line = $backtrace['line'] ?? '';
        echo htmlspecialchars("$class line $line\n\n");
    }

    ob_start();
    var_dump($value);
    $dump = ob_get_clean();

    echo htmlspecialchars($dump);

    echo "</pre>";

    exit;
}

function old(string $key, ?string $default = ''): string
{
    $value = Session::get('old')[$key] ?? $default;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function abort($code = ResponseStatus::NOT_FOUND): void
{
    http_response_code($code);

    echo view(path: "default/{$code}")->response();

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

require_once __DIR__.'/../Routing/Response/helpers/view_helper.php';
require_once __DIR__.'/../Core/helpers/app_helper.php';