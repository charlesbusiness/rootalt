<?php

namespace Modules\Authentication\Dto;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Authentication\Rules\PasswordRule;
use Modules\Authentication\Rules\PhoneNumberRule;

class UserDto
{
    public string $password;
    public string $email;
    public string $phone;
    public string $firstName;
    public string $lastName;
    public string $dob;
    public string $username;
    private function __construct(
        string $email,
        string $phone,
        string $password,
        string $lastName,
        string $firstName,
        string $dob,
        string $username
    ) {
        $this->email = $email;
        $this->phone = $phone;
        $this->password = $password;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->dob = $dob;
        $this->username = $username;
    }

    public static function fromArray(array $data): self |string
    {

        $validator = Validator::make($data, [
            'username' => ['required', 'string', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', new PasswordRule],
            'phone' => ['required', 'unique:users,phone', 'string', new PhoneNumberRule],
            'dob' => ['required', 'date_format:Y-m-d']
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return new self(

            email: $data['email'],
            phone: $data['phone'],
            password: $data['password'],
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            dob: $data['dob'],
            username: $data['username'],
        );
    }
}
