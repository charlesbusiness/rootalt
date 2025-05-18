<?php

namespace Modules\UserProfile\Dto;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Authentication\Rules\PhoneNumberRule;

class LiberiaTipmeDto
{
    public string $name;
    public mixed $extra;
    public mixed $user_id;
    private function __construct(string $name, mixed $extra)
    {

        $this->name = $name;
        $this->extra = $extra;
    }

    public static function fromArray(array $data): self |string
    {

        $validator = Validator::make($data, [


            'name' => ['required', 'string'],

            'extra' => ['nullable', 'json'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return new self(
            name: $data['name'],
            extra: @$data['extra'],
        );
    }
}
