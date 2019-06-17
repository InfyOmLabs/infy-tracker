<?php namespace App\Traits;

/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 *
 * User: Ajay Makwana
 * Email: ajay.makwana@infyom.com
 * Date: 5/1/2019
 * Time: 11:18 AM
 */

use App\Exceptions\ApiOperationFailedException;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Log;
use Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait ImageTrait
 * @package App\Traits
 */
trait ImageTrait
{
    /**
     * @param string $file
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
     * @param UploadedFile $file
     * @param string $path
     *
     * @return string
     * @throws ApiOperationFailedException
     *
     */
    public static function makeImage($file, $path)
    {
        try {
            $fileName = '';
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension(); // getting image extension
                if (!in_array(strtolower($extension), ['jpg', 'gif', 'png', 'jpeg'])) {
                    throw  new ApiOperationFailedException('invalid image', Response::HTTP_BAD_REQUEST);
                }
                $date = Carbon::now()->format('Y-m-d');
                $fileName = $date . '_' . uniqid() . '.' . $extension;
                Storage::putFileAs($path, $file, $fileName, 'public');
            }

            return $fileName;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiOperationFailedException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $path
     * @return string
     */
    public function imageUrl($path)
    {
        return $this->urlEncoding(Storage::url($path));
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    function urlEncoding($url)
    {
        $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '%5C');
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "/");

        return str_replace($entities, $replacements, urlencode($url));
    }

    /**
     * @param UploadedFile $file
     * @param $path
     *
     * @return string
     * @throws ApiOperationFailedException
     *
     */
    public static function uploadVideo($file, $path)
    {
        try {
            $fileName = '';
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension(); // getting image extension
                if (!in_array(strtolower($extension), ['mp4', 'mov', 'ogg', 'qt'])) {
                    throw  new ApiOperationFailedException('invalid Video', Response::HTTP_BAD_REQUEST);
                }
                $date = Carbon::now()->format('Y-m-d');
                $fileName = $date . '_' . uniqid() . '.' . $extension;
                Storage::putFileAs($path, $file, $fileName, 'public');
            }

            return $fileName;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiOperationFailedException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param UploadedFile $file
     * @param string $path
     *
     * @return string
     * @throws ApiOperationFailedException
     *
     */
    public static function makeAttachment($file, $path)
    {
        try {
            $fileName = '';
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension(); // getting image extension
                if (!in_array(strtolower($extension), ['doc', 'docx', 'pdf', 'zip'])) {
                    throw  new ApiOperationFailedException('invalid Attachment', Response::HTTP_BAD_REQUEST);
                }
                $date = Carbon::now()->format('Y-m-d');
                $fileName = $date . '_' . uniqid() . '.' . $extension;
                Storage::putFileAs($path, $file, $fileName, 'public');
            }

            return $fileName;
        } catch (Exception $e) {
            throw new ApiOperationFailedException($e->getMessage(), $e->getCode());
        }
    }
}
