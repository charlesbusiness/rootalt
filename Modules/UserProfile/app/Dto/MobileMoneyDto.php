<?php

namespace Modules\UserProfile\Dto;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Authentication\Rules\PhoneNumberRule;
use Modules\Core\Rules\CardExpirationRule;

class MobileMoneyDto
{
    public string $phone_number;
    public mixed $extra;
    public mixed $user_id;
    public string $country_dial_code;
    public mixed $mobile_money_category_id;


    private function __construct(string $phone_number,   mixed $extra, int $catId, string $country_dial_code)
    {

        $this->phone_number = $phone_number;
        $this->extra = $extra;
        $this->country_dial_code = $country_dial_code;
        $this->mobile_money_category_id = $catId;
    }

    public static function fromArray(array $data): self |string
    {

        $validator = Validator::make($data, [


            'phone_number' => [
                'required',
                new PhoneNumberRule,
                Rule::unique('mobile_money_payment_options', 'phone_number')->where(function ($query) {
                    return $query->where('user_id', '!=', request()->user()->id);
                }),
            ],

            'mobile_money_category_id' => ['required', 'numeric', 'exists:mobile_money_categories,id'],
            'country_dial_code' => ['required', 'exists:countries,phone_code'],
            'extra' => ['nullable', 'json'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return new self(
            phone_number: $data['phone_number'],
            extra: @$data['extra'],
            catId: $data['mobile_money_category_id'],
            country_dial_code: $data['country_dial_code'],
        );
    }
}
