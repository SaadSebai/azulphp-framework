<?php

namespace Azulphp\Lapis;

use Exception;

class LapisCompiler
{
    /**
     * @throws Exception
     */
    public function compile(string $template): string
    {
        // <l-layout>...</l-layout>
        $this->compileLayout($template);
        // <l-php>...</l-php>
        $this->compilePhp($template);

        // l-include('...')
        $this->compileInclude($template);

        // Variables
        $this->compileVar($template);

        // l-if, l-elseif, l-else, l-endif
        $this->compileConditions($template);

        // l-foreach, l-endforeach
        $this->compileForeach($template);

        return $template;
    }

    protected function compileVar(string &$template): void
    {
        // Match {{ ... }} not preceded by /
        $template = preg_replace('/(?<!\/)\{\{\s*(.+?)\s*\}\}/', '<?php echo htmlspecialchars($1); ?>', $template);

        // Restore escaped /{{ ... }} to {{ ... }}
        $template = preg_replace('/\/\{\{\s*(.+?)\s*\}\}/', '{{ $1 }}', $template);
    }

    protected function compileConditions(string &$template): void
    {
        $template = preg_replace('/(?<!\/)l-if\s*\((.+?)\)/', '<?php if ($1): ?>', $template);
        $template = preg_replace('/(?<!\/)l-elseif\s*\((.+?)\)/', '<?php elseif ($1): ?>', $template);
        $template = preg_replace('/(?<!\/)l-else\b/', '<?php else: ?>', $template);
        $template = preg_replace('/(?<!\/)l-endif\b/', '<?php endif; ?>', $template);

        // Restore escaped /l-if, etc. to plain text
        $template = preg_replace('/\/(l-if\s*\(.+?\))/', '$1', $template);
        $template = preg_replace('/\/(l-elseif\s*\(.+?\))/', '$1', $template);
        $template = preg_replace('/\/(l-else\b)/', '$1', $template);
        $template = preg_replace('/\/(l-endif\b)/', '$1', $template);
    }

    protected function compileForeach(string &$template): void
    {
        $template = preg_replace('/(?<!\/)l-foreach\s*\((.+)\)/', '<?php foreach ($1): ?>', $template);
        $template = preg_replace('/(?<!\/)l-endforeach\b/', '<?php endforeach; ?>', $template);

        // Restore escaped
        $template = preg_replace('/\/(l-foreach\s*\(.+?\))/', '$1', $template);
        $template = preg_replace('/\/(l-endforeach\b)/', '$1', $template);
    }

    /**
     * @throws Exception
     */
    protected function compileLayout(string &$template): void
    {
        $pattern = '/<l-t:([\w\.\-]+)>(.*?)<\/l-t:\1>/s';

        $template = preg_replace_callback($pattern, function ($matches) {
            $layoutName = $matches[1];
            $slotContent = $matches[2];

            $relativePath = str_replace('.', DIRECTORY_SEPARATOR, $layoutName);
            $layoutPath = base_path("resources/views/{$relativePath}.lapis.php");

            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout file not found: {$layoutName}.lapis.php");
            }

            $layoutTemplate = file_get_contents($layoutPath);

            // Only inject slot content — don't compile layout for PHP, variables, etc.
            return str_replace('<l-slot />', $slotContent, $layoutTemplate);
        }, $template);
    }

    protected function compilePhp(string &$template): void
    {
        // Handle <l-php> ... </l-php>
        $template = preg_replace_callback('/(?<!\/)<l-php>(.*?)<\/l-php>/s', function ($matches) {
            return "<?php\n" . trim($matches[1]) . "\n?>";
        }, $template);

        // Restore escaped syntax
        $template = preg_replace('/\/<l-php>/', '<l-php>', $template);
        $template = preg_replace('/\/<\/l-php>/', '</l-php>', $template);
    }

    protected function compileInclude(string &$template): void
    {
        $template = preg_replace_callback(
            '/(?<!\/)l-include\s*\(\s*[\'"]([\w\.\-\/]+)[\'"]\s*\)/',
            function ($matches) {
                $partialPath = str_replace('.', DIRECTORY_SEPARATOR, $matches[1]);
                $fullPath = base_path("resources/views/{$partialPath}.lapis.php");

                if (!file_exists($fullPath)) {
                    throw new \Exception("Included view not found: {$matches[1]}.lapis.php");
                }

                return file_get_contents($fullPath);
            },
            $template
        );

        // Restore escaped directive: /l-include(...)
        $template = preg_replace('/\/l-include\s*\((.*?)\)/', 'l-include($1)', $template);
    }
}