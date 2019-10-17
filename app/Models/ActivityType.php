<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ActivityType.
 *
 * @property int $id
 * @property string $name
 * @property int|null $created_by
 * @property int $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TimeEntry[] $timeEntries
 * @property-read int|null $time_entries_count
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ActivityType onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActivityType whereDeletedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ActivityType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ActivityType withoutTrashed()
 */
class ActivityType extends Model
{
    use SoftDeletes;
    public $table = 'activity_types';

    public $fillable = [
        'name',
        'created_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'name'       => 'string',
        'created_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    const ACTIVITY_TYPES = [
        'Development',
        'Management',
        'Code Review',
        'Testing',
        'Documentation',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:activity_types,name',
    ];

    public static $messages = [
        'name.unique' => 'Activity type with same name already exist',
    ];

    /**
     * @return BelongsTo
     */
    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany
     */
    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class, 'activity_type_id');
    }
}
