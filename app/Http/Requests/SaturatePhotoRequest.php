<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class SaturatePhotoRequest extends FormRequest
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
         * level is required and must be digit between 0 ,200
         * 100 keep saturation value without change
         * 0 reduce the saturation 100% get grayscale
         * 200 increase the  saturation 100%
         */
        $rules = [
            'image' => ['required'],
            'level' => ['required', 'numeric', 'min:0','max:200'],
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
