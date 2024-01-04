<?php

namespace App\Rules\Announcement;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultipleActiveStatusNotAllow implements ValidationRule
{


    public function __construct(private $announcements){
        
    }
    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail):void
    {
        $count = [];

        foreach($this->announcements as $item){   
                   
            if($item['status'] ?? 1 == 1){
                $count[
                    $item['department_id'] ?? 1
                ][] = 1;
            }
        }

        foreach($count as $item){
            if(count($item) > 1){
                $fail('Multiple active statuses are not allowed. Only an announcement can active');
            }
        }

        
    }


    
}
