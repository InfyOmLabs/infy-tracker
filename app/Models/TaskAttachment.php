<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    public $table = 'task_attachments';

    public $fillable = [
        'task_id',
        'file'
    ];

    /**
     * @param $value
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getFileAttribute($value)
    {
        if(isset($value) && !empty($value)){
            return url('attachments').'/'.$value;
        }
        return url('assets').'/img/default_image.png';
    }
}
