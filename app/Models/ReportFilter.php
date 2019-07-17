<?php
/**
 * Created by PhpStorm.
 * User: Shailesh-InfyOm
 * Date: 08-07-2019
 * Time: 05:22 PM.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ReportFilter.
 *
 * @property int $id
 * @property int $report_id
 * @property string $param_type
 * @property int $param_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter whereParamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter whereParamType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter ofParamType($paramType)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportFilter ofReport($reportId)
 */
class ReportFilter extends Model
{
    public $table = 'report_filters';

    public $fillable = [
        'report_id',
        'param_type',
        'param_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'param_type' => 'string',
        'owner_id'   => 'integer',
        'param_id'   => 'integer',
    ];

    /**
     * @param Builder $query
     * @param string $paramType
     *
     * @return Builder
     */
    public function scopeOfParamType(Builder $query, $paramType)
    {
        return $query->where('param_type', $paramType);
    }

    /**
     * @param Builder $query
     * @param int $reportId
     *
     * @return Builder
     */
    public function scopeOfReport(Builder $query, $reportId)
    {
        return $query->where('report_id', $reportId);
    }
}
