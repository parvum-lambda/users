<?php

namespace App\Api\V1\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class UsersRequest extends BaseFormRequest
{
    public function store() : array
    {
        return [
            'name'  => 'required|string|min:3|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(static function (Builder $query) {
                    $query->whereNull('deleted_at');
                })
            ],
            'document' => [
                'string',
                Rule::unique('users')->where(static function (Builder $query) {
                    $query->whereNull('deleted_at');
                })
            ]
        ];
    }
}
