<?php

namespace Modules\UserProfile\Dto;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Core\Rules\CardExpirationRule;

class MastercardDto
{
    public string $names;
    public string $card_expiration;
    public string $cvv;
    public string $card_number;
    public mixed $extra;
    public int $user_id;
 

    private function __construct(string $names, string $card_expiration, string $cvv,  string $card_number, mixed $extra)
    {
 
        $this->names = $names;
        $this->card_expiration = $card_expiration;
        $this->cvv = $cvv;
        $this->card_number = $card_number;
        $this->extra = $extra;
    }

    public static function fromArray(array $data): self |string
    {

        $validator = Validator::make($data, [
            
            'cvv' => ['required', 'numeric', 'digits_between:3,4'],
            'names' => ['required', 'string'],
            'card_number' => ['required', 'string'],
            'card_expiration' => ['required', new CardExpirationRule],
            'extra' => ['nullable', 'json'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return new self(
            cvv: $data['cvv'],
            names: $data['names'],
            card_number: $data['card_number'],
            card_expiration: $data['card_expiration'],
            extra: @$data['extra'],
        );
    }
}
