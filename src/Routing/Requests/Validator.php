<?php

namespace Azulphp\Routing\Requests;

use DateTime;

class Validator
{
    const int UNSIGNED_INT_MAX = 4294967295;

    public function __construct(public string $name, public mixed $value)
    {
    }

    /**
     * Validate the value and return errors.
     *
     * @param  callable  $rules
     * @return array
     */
    public function validate(callable $rules): array
    {
        $errors = [];

        foreach ($rules($this) as $rule)
        {
            if ($error = $rule)
            {
                $errors[] = $error;
                break;
            }
        }

        return $errors;
    }

    public function required(): ?string
    {
        $message ??= "$this->name is required";

        return $this->value === null || $this->value === '' ? $message : null;
    }

    public function string(int $min = 1, int $max = 255, ?string $message = null): ?string
    {
        if (is_string($this->value))
        {
            $this->value = trim($this->value);

            if (strlen($this->value) < $min || strlen($this->value) > $max)
                return $message ?? "$this->name is out of range.";
        }
        else
        {
            return $message ?? "$this->name is not a valid string.";
        }

        $this->value = (string) $this->value;
        return null;
    }

    public function email(?string $message = null): ?string
    {
        $message ??= "$this->name must be a valid email address.";

        return !filter_var($this->value, FILTER_VALIDATE_EMAIL)
            ? $message
            : null;
    }

    public function exists(string $repository, string $method, ?string $message = null): ?string
    {
        $message ??= "$this->name doesn't exists.";

        return ! (new $repository)->{$method}($this->value)
            ? $message
            : null;
    }

    public function phone(?string $message = null): ?string
    {
        $message ??= "$this->name must be a valid phone number.";

        $pattern = '/^\+?[1-9]\d{9,14}$/';

        return !preg_match($pattern, $this->value) ? $message : null;
    }

    public function int(int $min = 0, int $max = self::UNSIGNED_INT_MAX, ?string $message = null): ?string
    {
        if (filter_var($this->value, FILTER_VALIDATE_INT) !== false)
        {
            $this->value = (int) $this->value;

            if (strlen($this->value) < $min || strlen($this->value) > $max)
                return $message ?? "$this->name is out of range.";
        }
        else
        {
            return $message ?? "$this->name is not a valid integer.";
        }

        $this->value = (int) $this->value;
        return null;
    }

    public function numeric(int $min = 0, int $max = self::UNSIGNED_INT_MAX, ?string $message = null): ?string
    {
        if (is_numeric($this->value))
        {
            $this->value = (int) $this->value;

            if (strlen($this->value) < $min || strlen($this->value) > $max)
                return $message ?? "$this->name is out of range.";
        }
        else
        {
            return $message ?? "$this->name is not a valid integer.";
        }

        return null;
    }

    public function inEnum(string $enum, ?string $message = null): ?string
    {
        $message ??= "$this->name must be a valid value.";

        return !in_array($this->value, array_column($enum::cases(), 'value'), true)
            ? $message
            : null;
    }

    public function date(string $format = 'Y-m-d', ?string $message = null): ?string
    {
        $message ??= "$this->name must be a valid date.";

        $errors = false;

        if ($date = DateTime::createFromFormat($format, $this->value)){
            $errors = $date::getLastErrors();
        }

        return !$date || ($errors && array_sum($errors)) || $date->format($format) !== $this->value
            ? $message
            : null;
    }

    public function afterDate(string $beforeAttribute, string $beforeValue, string $format = 'Y-m-d', ?string $message = null): ?string
    {
        $message ??= "$this->name must after $beforeAttribute.";

        $date = DateTime::createFromFormat($format, $this->value);
        $beforeDate = DateTime::createFromFormat($format, $beforeValue);

        return !$date || !$beforeDate || $beforeDate > $date
            ? $message
            : null;
    }
}
