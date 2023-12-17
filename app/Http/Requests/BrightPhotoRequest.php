<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class BrightPhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /**
         *
         * image is required
         * imgae can be uploaded file or url
         * if it is url we must insure that it is valide url
         * 
         * level is required and must be digit between -100 ,+100 
         *
         */
        $rules = [
            'image' => ['required'],
            'level' => ['required', 'numeric', 'min:-100','max:100'],
        ];

        $all = $this->all();
        if (isset($all['image']) && $all['image'] instanceof UploadedFile) {
            $rules['image'][] = 'image';
        } else {
            $rules['image'][] = 'url';
        }
        
        return $rules;
    }
}
