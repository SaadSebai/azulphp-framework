<?php

namespace Azulphp\Requests;

use Azulphp\Exceptions\ValidationException;
use Azulphp\Helpers\Csrf;

abstract class FormRequest
{
    use Paginatable;

    public string $method;
    public array $errors = [];
    protected array $validated = [];
    protected array $rules = [];
    protected array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = array_merge($_GET, $_POST, $data);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->validateCsrf();
    }

    /**
     * Return the validated data only.
     *
     * @return  array
     * @throws ValidationException
     */
    public function validated(): array
    {
        $this->validated = [];

        $this->setRules();

        foreach ($this->rules as $key => $rule) {
            if (array_key_exists($key, $this->data))
            {
                $rule($key, $this->data[$key]);
                $this->validated[$key] = $this->data[$key];
            }
        }

        $this->handleErrors();

        return $this->validated;
    }

    /**
     * Handle the validation errors.
     *
     * @throws ValidationException
     */
    protected function handleErrors(): void
    {
        if (!empty($this->errors))
        {
            throw ValidationException::throw($this->errors, $this->validated);
        }
    }

    /**
     * Validate CSRF token.
     *
     * @return void
     */
    protected function validateCsrf(): void
    {
        if ($this->method !== 'GET' && !Csrf::verify($this->data['_csrf'] ?? null))
        {
            die('Invalid CSRF token.');
        }
    }

    /**
     * Set the request rules.
     *
     * @return void
     */
    protected function setRules(): void
    {
        $this->rules = $this->rules() + $this->paginationRules();
    }

    /**
     * The rules you want to apply to the input.
     *
     * Rules expect the key to be the attribute and the value to be a callable that accepte 2 params ($attribute, $value).
     * @example ['name' => function (string $attribute, mixed $value) { ...logic here }]
     *
     * @return  array
     */
    abstract protected function rules(): array;
}