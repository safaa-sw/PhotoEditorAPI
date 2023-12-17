<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class RotatePhotoRequest extends FormRequest
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
         * angle is required and must be digit between -360 ,+360 
         *
         * bgcolor is optional but must be rgb color format
         *        
         */
        $rules = [
            'image' => ['required'],
            'angle' => ['required', 'numeric', 'min:-360','max:360'],
            'bgcolor' => 'regex:/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/',
            ////'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})\b$/', // for hex color
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
