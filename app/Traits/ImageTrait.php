<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 11-07-2019
 * Time: 05:15 PM.
 */

namespace App\Traits;

use App\Exceptions\ApiOperationFailedException;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
use Image;
use Log;

/**
 * Trait ImageTrait.
 */
trait ImageTrait
{
    /**
     * @param string $file
     *
     * @return bool
     */
    public static function deleteImage($file)
    {
        if (Storage::exists($file)) {
            Storage::delete($file);

            return true;
        }

        return false;
    }

    /**
     * @param $file
     * @param $path
     * @param array $options
     * @return string
     * @throws ApiOperationFailedException
     */
    public static function makeImage($file, $path, $options = [])
    {
        try {
            $fileName = '';
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension(); // getting image extension
                if (!in_array(strtolower($extension), ['jpg', 'gif', 'png', 'jpeg'])) {
                    throw  new ApiOperationFailedException('invalid image', Response::HTTP_BAD_REQUEST);
                }
                $date = Carbon::now()->format('Y-m-d');
                $fileName = $date.'_'.uniqid().'.'.$extension;
                if (!empty($options)) {
                    $imageThumb = Image::make($file->getRealPath())->fit($options['width'], $options['height']);
                    $imageThumb = $imageThumb->stream();
                    Storage::put($path.DIRECTORY_SEPARATOR.$fileName, $imageThumb->__toString());
                } else {
                    Storage::putFileAs($path, $file, $fileName, 'public');
                }
            }
            return $fileName;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiOperationFailedException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $path
     * @return mixed
     */
    public function imageUrl($path)
    {
        return $this->urlEncoding(Storage::url($path));
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function urlEncoding($url)
    {
        $entities = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '%5C'];
        $replacements = ['!', '*', "'", '(', ')', ';', ':', '@', '&', '=', '+', '$', ',', '/', '?', '%', '#', '[', ']', '/'];

        return str_replace($entities, $replacements, urlencode($url));
    }

    /**
     * @param $file
     * @param $path
     * @return string
     * @throws ApiOperationFailedException
     */
    public static function makeAttachment($file, $path)
    {
        try {
            $fileName = '';
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension(); // getting image extension

                if (!in_array(strtolower($extension), ['xls', 'pdf', 'doc', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'])) {
                    throw  new ApiOperationFailedException('invalid Attachment', Response::HTTP_BAD_REQUEST);
                }
                $originalName = $file->getClientOriginalName();
                $date = Carbon::now()->format('Y-m-d');
                $originalName = sha1($originalName.time());
                $fileName = $date.'_'.uniqid().'_'.$originalName.'.'.$extension;
                $contents = file_get_contents($file->getRealPath());
                Storage::put($path.DIRECTORY_SEPARATOR.$fileName, $contents);
            }

            return $fileName;
        } catch (Exception $e) {
            throw new ApiOperationFailedException($e->getMessage(), $e->getCode());
        }
    }
}
