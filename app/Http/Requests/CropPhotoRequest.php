<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class CropPhotoRequest extends FormRequest
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
         * width is required and must be digits 
         *
         * height is required and must be digits 
         * 
         * x and y Coordinate is optional to determine top-left corner
         * 
         */
        $rules = [
            'image' => ['required'],
            'w' => ['required', 'regex:/^[1-9]\d*$/'],
            'h' => ['required', 'regex:/^[1-9]\d*$/'],
            'x' => ['regex:/^[0-9]\d*$/'],
            'y' => ['regex:/^[0-9]\d*$/'],
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
