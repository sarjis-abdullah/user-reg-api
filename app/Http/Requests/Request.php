<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    /**
     * Check if is there any unknown fields
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $allowableFields = array_merge($this->rules(), [
                'page' => 'number',
                'per_page' => 'number',
                'order_by' => 'string',
                'order_direction' => 'in:asc,desc',
                'include' => 'string',
                'detailed' => 'string',
                'items' => 'numeric'
            ]);

            foreach ($this->all() as $key => $value) {
                if (!array_key_exists($key, $allowableFields)) {

                    // if it is a IndexRequest return invalid filter message
                    if (strpos(get_called_class(), 'IndexRequest') !== false) {
                        $validator->errors()->add($key, 'Invalid filter.');
                    } else {
                        $validator->errors()->add($key, "Field does not exist.");
                    }
                }
            }
        });
    }

}
