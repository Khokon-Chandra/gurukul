<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * stores custom mapped request rules method to individual route methods
     */
    protected array $routeRequest = [];

    protected array $rules = [];

    protected array $prepareForValidationRules = [];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $route  = $this->getPath();
        $method = $this->getRequestMethod();

        $requestRuleMethod = $this->routeRequest["$route|$method"]['rules'] ?? 'default';

        if (method_exists($this, $requestRuleMethod)) {
            $this->{$requestRuleMethod}();
        }

        return $this->rules;
    }

    public function getPath(): string
    {
        return $this->route()->uri();
    }

    public function getRequestMethod(): string
    {
        //synchronize laravel request masking
        $method = $this->get('_method', null);

        if ($method === null) {
            $method = $this->method();
        }

        return strtolower($method);
    }


    protected function prepareForValidation(): void
    {
        $route = $this->getPath();
        $method = $this->getRequestMethod(); 
        $prepareForValidationMethod = $this->routeRequest["$route|$method"]['prepareForValidation'] ?? 'default';

        if (method_exists($this, $prepareForValidationMethod)) {
            $this->{$prepareForValidationMethod}();
        }

        $this->merge($this->prepareForValidationRules);
    }
}
