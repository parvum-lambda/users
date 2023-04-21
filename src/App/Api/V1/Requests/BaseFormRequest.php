<?php

namespace App\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        $action = $this->route()->getActionMethod() . 'Authorize';

        if (! method_exists($this, $action)) {
            return true;
        }

        return $this->{$action}();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        $action = $this->route()->getActionMethod();

        if (! method_exists($this, $action)) {
            return [];
        }

        return $this->{$action}();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function validationData() : array
    {
        $action = $this->route()->getActionMethod() . 'Data';

        if (! method_exists($this, $action)) {
            return $this->all();
        }

        return $this->{$action}();
    }
}
