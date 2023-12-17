<?php

namespace App\Http\Controllers;

use App\Models\PhotoEditing;
use App\Http\Requests\CropPhotoRequest;
use App\Http\Requests\ResizePhotoRequest;
use App\Http\Requests\ContrastPhotoRequest;
use App\Http\Requests\BrightPhotoRequest;
use App\Http\Requests\OpacityPhotoRequest;
use App\Http\Requests\SharpPhotoRequest;
use App\Http\Requests\RotatePhotoRequest;
use App\Http\Requests\SaturatePhotoRequest;
use App\Http\Requests\Effect3dPhotoRequest;
use App\Http\Requests\FlipPhotoRequest;
use App\Http\Requests\InvertPhotoRequest;
use Illuminate\http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Http\Resources\PhotoEditingResource;
//https://photoeditor-sk.herokuapp.com/api/photo/

class PhotoEditingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$result = PhotoEditingResource::collection(PhotoEditing::all());
        return response()->json("test composer update finally", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PhotoEditing  $photoEditing
     * @return \Illuminate\Http\Response
     */
    public function destroy(PhotoEditing $photoEditing)
    {
        //
    }


    /**
     * crop image function
     */
    public function crop(CropPhotoRequest $request)
    {       
       
        //get all data from request
        $all = $request->all();

        /** @var UploadedFile|string $image */
        //get image from request and it can be uploaded file or url(string)
        $image = $all['image'];

        //remove image from $all variable because we add this variable to photo-editings table
        // in database in data property and we don't need image in it
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        // get width and height values for crop
        $w = $all['w'];
        $h = $all['h'];

        //create instance of intervention\image class with original class
        $image = Image::make($originalPath);

        //create cropped image filename from original filename
        $croppedFilename = $filename . '-cropped.' . $extension;

        //call crop function from package intervention on image
        //and save image with new name in same folder
        if ((isset($all['x']))&&(isset($all['y']))){
            $x = $all['x'];
            $y = $all['y'];
            $image->crop($w, $h,$x,$y)->save($absolutePath . $croppedFilename);
        }
        else{
            $image->crop($w, $h)->save($absolutePath . $croppedFilename);
        }

        //preparing data to save in database $data
        $data = [
            'type' => 'crop',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $croppedFilename,
        ];   

        // create instance of PhotoEditingModel which store data in database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'crop',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $croppedFilename),
        ];
        return $result;

    }

    /**
     * resize image function
     */
    public function resize(ResizePhotoRequest $request)
    {
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        // get width and height values for resizing
        $w = $all['w'];
        $h = $all['h'] ?? false;

        // call functions which calculate new width and height for image
        list($image, $width, $height) = $this->getWidthAndHeight($w, $h, $originalPath);

        //create resized image filename from original filename
        $resizedFilename = $filename . '-resized.' . $extension;

        //call resize function from package intervention on image
        //and save image with new name in same folder
        $image->resize($width, $height)->save($absolutePath . $resizedFilename);

        //preparing data to save in database $data
        $data = [
            'type' => 'resize',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $resizedFilename,
        ];    

        // create instance of PhotoEditingModel which store data in database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'resize',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $resizedFilename),
        ];
        return $result;
        
    }

    /**
     * Function For Calculating New Width and Height
     */
    protected function getWidthAndHeight($w, $h, $originalPath)
    {
        $image = Image::make($originalPath);

        // get original width and height values
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        //check if width is percentage
        if (str_ends_with($w, '%')) {
            // get width ratio example get 50 from 50%
            $ratioW = (float)(str_replace('%', '', $w));
            // get height ratio if exist and if not exist we use width ratio
            $ratioH = $h ? (float)(str_replace('%', '', $h)) : $ratioW;
            // calculate new width and new height
            $newWidth = $originalWidth * $ratioW / 100;
            $newHeight = $originalHeight * $ratioH / 100;
        } 
        // if width is digital
        else {
            //calculate new width
            $newWidth = (float)$w;

            /**
             * $originalWidth  -  $newWidth
             * $originalHeight -  $newHeight
             * -----------------------------
             * $newHeight =  $originalHeight * $newWidth/$originalWidth
             */
            $newHeight = $h ? (float)$h : ($originalHeight * $newWidth / $originalWidth);
        }

        return [$image, $newWidth, $newHeight];
    }

    /**
     * Contrast image function
    */
    public function contrast(ContrastPhotoRequest $request)
    {
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        // get level for contrast
        $level = $all['level'];
        
        //create instance of intervention\image class with original class
        $image = Image::make($originalPath);

        //create contrast image filename from original filename
        $contrastFilename = $filename . '-contrast.' . $extension;

        //call contrast function from package intervention on image
        //and save image with new name in same folder
        $image->contrast($level)->save($absolutePath . $contrastFilename);

        //preparing data to save in database $data
        $data = [
            'type' => 'contrast',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $contrastFilename,
        ]; 

        // create instance of PhotoEditingModel which store data in database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'contrast',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $contrastFilename),
        ];
        return $result;
    }


    /**
     * change image brightness function
     */
    public function bright(BrightPhotoRequest $request)
    {
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        // get level for brightness
        $level = $all['level'];
        
        //create instance of intervention\image class with original class
        $image = Image::make($originalPath);

        //create brightness image filename from original filename
        $brightFilename = $filename . '-bright.' . $extension;

        //call contrast function from package intervention on image
        //and save image with new name in same folder
        $image->brightness($level)->save($absolutePath . $brightFilename);

        //preparing data to save in database $data
        $data = [
            'type' => 'brightness',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $brightFilename,
        ]; 

        // create instance of PhotoEditingModel which store data in database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'brightness',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $brightFilename),
        ];
        return $result;
    }

    /**
     * change image opacity function
     */
    public function opacity(OpacityPhotoRequest $request)
    {
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        // get transparency level for opacity
        $tl = $all['tl'];
        
        $image = Image::make($originalPath);

        //create opacity image filename from original filename
        $opacityFilename = $filename . '-opacity.' . $extension;

        //call opacity function from package intervention on image
        //and save image with new name in same folder
        $image->opacity($tl)->save($absolutePath . $opacityFilename);

        //preparing data to save in database $data
        $data = [
            'type' => 'opacity',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $opacityFilename,
        ];   

       // create instance of PhotoEditingModel which store data in database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'opacity',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $opacityFilename),
        ];
        return $result;
    }

    /**
     * sharpen image function
     */
    public function sharp(SharpPhotoRequest $request)
    {
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        $image = Image::make($originalPath);

        //create sharp image filename from original filename
        $sharpFilename = $filename . '-sharp.' . $extension;

        //if there is amount in request call sharpen function with this amount
        if (isset($all['amount'])) {
            $image->sharpen($all['amount'])->save($absolutePath . $sharpFilename);
        }
        else{
            //call function without amount // default amount=10;
            $image->sharpen()->save($absolutePath . $sharpFilename);
        } 

        //preparing data to save in database $data
        $data = [
            'type' => 'sharpen',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $sharpFilename,
        ]; 

        // create instance of PhotoEditingModel which store data in database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'sharpen',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $sharpFilename),
        ];
        return $result;
    }

    /**
     * rotate image by angle function
     */
    public function rotate(RotatePhotoRequest $request)
    {
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        $image = Image::make($originalPath);

        //create rotate image filename from original filename
        $rotateFilename = $filename . '-rotate.' . $extension;

        //if there is bgcolor in request call rotate function with angle and bgcolor
        if (isset($all['bgcolor'])) {
            $image->rotate($all['angle'],$all['bgcolor'])->save($absolutePath . $rotateFilename);
        }
        else{
            //call function angle only // default bgcolor is white #ffffff rgb(255,255,255)
            $image->rotate($all['angle'])->save($absolutePath . $rotateFilename);
        }        

        //preparing data to save in database $data
        $data = [
            'type' => 'rotate',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $rotateFilename,
        ];        

        // create instance of PhotoEditingModel which store data in database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'rotate',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $rotateFilename),
        ];
        return $result;
    }

    
    public function flip(FlipPhotoRequest $request)
    {      
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);

        // get mode for flip from request or set default value 'h'
        if(isset($all['mode'])) $mode = $all['mode']; else $mode = 'h';
        
        $image = Image::make($originalPath);
        $flipFilename = $filename . '-flipped.' . $extension;

        //call flip function from package intervention with $mode
        $image->flip($mode)->save($absolutePath . $flipFilename);

        //preparing data to save in database $data
        $data = [
            'type' => 'flip',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $flipFilename,
        ];        
        /*
        $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/
        
        //return infos without saving in database
        $result = [
            'type' => 'flip',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $flipFilename),
        ];
        return $result;
    
    }

     /**
      * invert Image color Function
     */
    public function invertColor(InvertPhotoRequest $request)
    {  
        $all = $request->all();
        $image = $all['image'];
        unset($all['image']);

        //create directory where we saved images before and after croped
        list($dir , $absolutePath) = $this->createRandomDir();

        //get image information uploaded file or url images
        list($imageName , $filename , $extension , $originalPath) = $this->getImageFileInfos($image , $absolutePath);
        
        $image = Image::make($originalPath);
        $invertFilename = $filename . '-invert.' . $extension;

        //call invert function from package intervention with $mode
        $image->invert()->save($absolutePath . $invertFilename);

        //preparing data to save in database $data
        $data = [
            'type' => 'invertcolor',
            'data' => json_encode($all),
            'user_id' => null, //$request->user()->id
            'name' => $imageName,
            'path' => $dir . $imageName,
            'output_path' => $dir . $invertFilename,
        ];
    
        // add info to database
       /* $photoEditing = PhotoEditing::create($data);
        return response()->json(new PhotoEditingResource($photoEditing), 200);*/

        //return infos without saving in database
        $result = [
            'type' => 'invertcolor',
            'name' => $imageName,
            'path' => URL::to($dir . $imageName),
            'output_path' => URL::to($dir . $invertFilename),
        ];
        return $result;

    }  

    /**
     * create Random Directory Name To save images in it
     */    
    protected function createRandomDir()
    {
        $dir = 'images/' . Str::random() . '/';
        $absolutePath = public_path($dir);
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }

        return array($dir , $absolutePath);
    }

    /**
     * get Image File Informations
     */
    protected function getImageFileInfos($image , $absolutePath)
    {
        //if image is an uploaded file
        if ($image instanceof UploadedFile) {
            $imageName = $image->getClientOriginalName();
            $filename = pathinfo($imageName, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $originalPath = $absolutePath . $imageName;
            $image->move($absolutePath, $imageName);

        } 
        // if image is an url we get data from $image variable
        else {
            $imageName = pathinfo($image, PATHINFO_BASENAME);
            $filename = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $originalPath = $absolutePath . $imageName;
            copy($image, $originalPath);
        }

        return array($imageName , $filename , $extension , $originalPath);
    }


}



