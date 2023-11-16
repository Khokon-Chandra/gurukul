<?php

namespace App\Http\Requests\Api\Notification;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class NotificationFilterRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject'               => 'nullable',
            'from_date'             => 'nullable|date',
            'to_date'               => 'nullable|date',
            'date_range'            => 'nullable|string|max:50',
            'searchable_date_range' => 'nullable'
        ];
    }



    protected function prepareForValidation()
    {
        $dateRange = null;
        $dates     = explode(' to ' ,$this->date_range) ?? false;
        
        if($this->from_date && $this->to_date){
            $dateRange = [
                Carbon::parse($this->from_date)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($this->to_date)->endOfDay()->format('Y-m-d H:i:s'),
            ];
        }

        if($this->date_range && $dates){
           
            $dateRange = [
                Carbon::parse($dates[0])->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($dates[1])->endOfDay()->format('Y-m-d H:i:s'),
            ];
        }


        $this->merge([
            'searchable_date_range' => $dateRange
        ]);
    }
}
