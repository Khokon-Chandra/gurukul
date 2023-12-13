<?php

namespace App\Rules\Announcement;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultipleActiveStatusNotAllow implements ValidationRule
{


    public function __construct(private $announcements){}
    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail):void
    {
        $count = 0;

        foreach($this->announcements as $item){
            if($item['status'] == 1){
                $count++;
            }
        }

        if($count >=2){
            $fail('Multiple active status not allowed. Only an announcement can active');
        }
    }


    
}
