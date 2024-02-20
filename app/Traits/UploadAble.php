<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\UploadImage;
use App\Jobs\DeleteImages;

use InterventionImage;

/**
* Trait UploadAble
* @package App\Traits
*/
trait UploadAble
{
    /**
    * @param UploadedFile $file
    * @param null $folder
    * @param string $disk
    * @param null $filename
    * @return false|string
    */
    public function uploadFile(UploadedFile $file, $folder = null, $disk = 'public', $filename = null) {
        $name = !is_null($filename) ? $filename : Str::random(25);
        $extension = $file->getClientOriginalExtension();
        $path = $file->storePubliclyAs(
            $folder,
            $name . "." . $extension,
            $disk
        );
        return $path;
    }

    /**
    * @param UploadedFile $file
    * @param null $folder
    * @param string $disk
    * @param null $filename
    * @return false|string
    */
    public function uploadImage(UploadedFile $file, $folder = null, $disk = 'public', $filename = null) {
        $name = !is_null($filename) ? $filename : Str::random(25);
        try{
            $imageUpload = InterventionImage::make($file);
            if ($imageUpload->height() > 1650) {
                $imageUpload->heighten(1650, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->orientate();
            }
            // $imageUpload->encode('jpg', 60);
            // $fileName = $name . "." . $file->getClientOriginalExtension();
            // $filePath = "/$folder/$fileName";

            // Storage::disk($disk)->put($filePath, $imageUpload->stream(), 'public');
            $binaryData = $imageUpload->encode('jpg', 60)->getEncoded();
            $encodedImage = base64_encode($binaryData);
            $fileName = $name . "." . $file->getClientOriginalExtension();
            $filePath = "/$folder/$fileName";
            UploadImage::dispatchSync($disk, $filePath, $encodedImage);
        } catch (\Exception $e) {
            var_dump('Caught exception: ',  $e->getMessage(), "\n");
        }
        return $filePath;
    }

    /**
    * @param UploadedFile $file
    * @param null $folder
    * @param string $disk
    * @param null $filename
    * @return false|string
    */
    public function uploadThumbnail(UploadedFile $file, $folder = null, $disk = 'public', $filename = null) {
        $name = !is_null($filename) ? $filename : Str::random(25);
        $imageUpload = InterventionImage::make($file);
        if ($imageUpload->height() > 170) {
            $imageUpload->resize(null, 170, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->orientate();
        }
        $imageUpload->encode('jpg', 60);
        $fileName = $name . "." . $file->getClientOriginalExtension();
        $filePath = "/$folder/$fileName";

        Storage::disk($disk)->put($filePath, $imageUpload->stream(), 'public');

        return $filePath;
    }

    /**
    * @param null $path
    * @param string $disk
    */
    public function deleteFile($path = '', $disk = 'public') {
        if (strpos($path, '/') === 0) {
            $path = ltrim($path, '/');
        }
        DeleteImages::dispatch($disk, $path);
    }

    /**
    * @param null $path
    * @param string $disk
    */
    public function fileExists($path = '', $disk = 'public') {
        if (strpos($path, '/') === 0) {
            $path = ltrim($path, '/');
        }
        return Storage::disk($disk)->exists($path);
    }
}
