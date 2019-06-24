<?php

namespace App\Models;

use Eloquent as Model;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property string $name
 * @property int|null $client_id
 * @property string $description
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\User|null $createdUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $prefix
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project wherePrefix($value)
 */
class Project extends Model
{
    const TEAM_ARR = ['1' => 'Backend', '2' => 'Frontend', '3' => 'Mobile', '4' => 'QA'];

    public $table = 'projects';

    public $fillable = [
        'name',
        'team',
        'description',
        'client_id',
        'created_by',
        'prefix'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'name'        => 'string',
        'team'        => 'integer',
        'description' => 'string',
        'client_id'   => 'integer',
        'created_by'  => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:projects,name',
        'client_id' => 'required',
        'prefix' => 'required|alpha_num|min:6|unique:projects,prefix',
    ];

    public static $messages = [
        'name.unique' => 'Project with same name already exist.',
    ];

    /**
     * @param $value
     */
    public function setPrefixAttribute($value)
    {
        $this->attributes['prefix'] = strtoupper($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
