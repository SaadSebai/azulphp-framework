<?php

namespace Azulphp\Requests;

use Azulphp\Validator;

trait Paginatable
{
    /**
     * If true, the pagination rules will be added to the validated rules.
     *
     * @var bool
     */
    protected bool $pagination = false;

    /**
     * Pagination rules.
     *
     * @return array
     */
    protected function paginationRules(): array
    {
        return $this->pagination ?
            ['page' => $this->validatePage()]
            : [];
    }

    /**
     * Pagination validation.
     *
     * @return callable
     */
    protected function validatePage(): callable
    {
        return function (string $attribute, mixed $value)
        {
            if (
                $value
                && ($error = Validator::int($attribute, $value))
            )
            {
                $this->errors[] = $error;
            }
        };
    }
}