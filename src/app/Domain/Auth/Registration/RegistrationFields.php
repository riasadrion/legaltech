<?php

namespace App\Domain\Auth\Registration;

class RegistrationFields
{
    private static array $fields = [];

    public static function add(string $key, array $definition): void
    {
        static::$fields[$key] = array_merge([
            'key' => $key,
            'label' => '',
            'type' => 'text',
            'required' => false,
            'rules' => [],
            'sort' => 100,
        ], $definition, ['key' => $key]);
    }

    public static function all(): array
    {
        $fields = array_values(static::$fields);
        usort($fields, fn ($a, $b) => $a['sort'] <=> $b['sort']);

        return $fields;
    }

    public static function validationRules(): array
    {
        $rules = [];
        foreach (static::$fields as $key => $field) {
            if (! empty($field['rules'])) {
                $rules[$key] = $field['rules'];
            } elseif ($field['required']) {
                $rules[$key] = ['required'];
            }
        }

        return $rules;
    }

    public static function reset(): void
    {
        static::$fields = [];
    }
}
