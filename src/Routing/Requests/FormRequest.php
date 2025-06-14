<?php

namespace Azulphp\Routing\Requests;

use Azulphp\Exceptions\ValidationException;
use Azulphp\Helpers\Csrf;
use http\Exception\RuntimeException;
use ReflectionClass;

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
    public function validated(): mixed
    {
        $this->validated = [];

        $this->setRules();

        foreach ($this->rules as $key => $param_rules) {
            $validator = new Validator(name: $key, value: $this->data[$key] ?? null);
            $errors = $validator->validate($param_rules);

            if (!empty($errors)) $this->errors[$key] = $errors;
            $this->validated[$key] = $validator->value;
        }

        $this->handleErrors();

        return $this->toDto();
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

    public function toDto()
    {
        $reflection = new ReflectionClass($this);

        $attributes = $reflection->getAttributes(FormDto::class);
        if (empty($attributes)) {
            return $this->validated;
        }

        /** @var FormDto $dto */
        $dto = $attributes[0]->newInstance();

        return new ($dto->class)(...$this->validated);
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