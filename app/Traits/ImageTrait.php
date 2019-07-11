<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 11-07-2019
 * Time: 05:15 PM
 */

namespace App\Traits;

use App\Exceptions\ApiOperationFailedException;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Storage;
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
     * @param string $path
     * @return string
     * @internal param $type
     * @internal param bool $full
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
     * @param string $path
     *
     * @throws ApiOperationFailedException
     *
     * @return string
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
                $originalName = sha1($originalName . time());
                $fileName = $date . '_' . uniqid() . '_' . $originalName . '.' . $extension;
                $contents = file_get_contents($file->getRealPath());
                Storage::put($path . DIRECTORY_SEPARATOR . $fileName, $contents);
            }

            return $fileName;
        } catch (Exception $e) {
            throw new ApiOperationFailedException($e->getMessage(), $e->getCode());
        }
    }
}
