<?php

namespace App\Models;

use Eloquent as Model;

/**
 * App\Models\ActivityType
 *
 * @property int $id
 * @property string $name
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ActivityType extends Model
{
    public $table = 'activity_types';

    public $fillable = [
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:activity_types,name'
    ];

    public static $messages = [
        'name.unique' => 'Activity type with same name already exist',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
