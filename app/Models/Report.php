<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Report.
 *
 * @property int $id
 * @property string $name
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
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
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects
 * @property-read mixed $formatted_date
 * @property-read \App\Models\User $user
 * @property-read int|null $projects_count
 */
class Report extends Model
{
    public $table = 'reports';
    protected $appends = ['formatted_date'];

    public $fillable = [
        'name',
        'owner_id',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'name'       => 'string',
        'owner_id'   => 'integer',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name'       => 'required',
        'start_date' => 'required',
        'end_date'   => 'required',
    ];

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'report_filters', 'report_id', 'param_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $startOfMonth = Carbon::parse($startDate)->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::parse($endDate)->endOfMonth()->format('Y-m-d');

        if ($startDate == $endDate) {
            return Carbon::parse($this->start_date)->format('jS M Y');
        } elseif ($startDate->format('Y-m-d') == $startOfMonth && $endDate->format('Y-m-d') == $endOfMonth) {
            return $startDate->format('M Y');
        } elseif ($startDate->month == $endDate->month) {
            return $startDate->format('jS').' - '.$endDate->format('jS M Y');
        } elseif ($startDate->month != $endDate->month) {
            return  $startDate->format('jS M').' - '.$endDate->format('jS M Y');
        }
    }
}
