<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class FlipPhotoRequest extends FormRequest
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
         * mode is optional and his value must be v or h 
         * 
         */
        $rules = [
            'image' => ['required'],
            'mode' => ['regex:/^[v|h]\d*$/'],
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
