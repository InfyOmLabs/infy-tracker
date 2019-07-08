<?php

namespace App\Models;

use Eloquent as Model;

/**
 * App\Models\Report
 *
 * @property int $id
 * @property string $name
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Report whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Report extends Model
{

    public $table = 'reports';

    public $fillable = [
        'name',
        'owner_id',
        'start_date',
        'end_date'
    ];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'owner_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    /**
     * Validation rules
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
    ];
}
