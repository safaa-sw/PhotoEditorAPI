<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class Effect3dPhotoRequest extends FormRequest
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
         * gray is optional and must be true or false
         * default value gray=false
         * 
         * determine light source position by azimuth and elevation and its optional
         * 
         * azimuth is measured in degrees off the x axis 
         * should be angle between -360, 360//default value = 45
         * 
         * elevation is measured in pixels above the Z axis
         * should be positive number //default value = 20
         *
         */
        $rules = [
          'image' => ['required'],
          'gray' => ['bool'],
          'azimuth' => ['numeric', 'min:-360','max:360'],
          'elev' => ['numeric', 'min:0'],
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
