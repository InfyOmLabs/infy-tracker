<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder as BuilderAlias;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Client.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $website
 * @property int $deleted_by
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $createdUser
 *
 * @method static BuilderAlias|Client newModelQuery()
 * @method static BuilderAlias|Client newQuery()
 * @method static BuilderAlias|Client query()
 * @method static BuilderAlias|Client whereCreatedAt($value)
 * @method static BuilderAlias|Client whereCreatedBy($value)
 * @method static BuilderAlias|Client whereEmail($value)
 * @method static BuilderAlias|Client whereId($value)
 * @method static BuilderAlias|Client whereName($value)
 * @method static BuilderAlias|Client whereUpdatedAt($value)
 * @method static BuilderAlias|Client whereWebsite($value)
 * @mixin Eloquent
 *
 * @property Carbon|null $deleted_at
 * @property-read Collection|Project[] $projects
 * @property-read int|null $projects_count
 *
 * @method static bool|null forceDelete()
 * @method static Builder|Client onlyTrashed()
 * @method static bool|null restore()
 * @method static BuilderAlias|Client whereDeletedAt($value)
 * @method static BuilderAlias|Client whereDeletedBy($value)
 * @method static Builder|Client withTrashed()
 * @method static Builder|Client withoutTrashed()
 *
 * @property int|null $department_id
 * @property-read \App\Models\Department|null $department
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereDepartmentId($value)
 */
class Client extends Model
{
    use softDeletes;
    public $table = 'clients';

    public $fillable = [
        'name',
        'email',
        'website',
        'department_id',
        'created_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'name'          => 'string',
        'email'         => 'string',
        'website'       => 'string',
        'deleted_by'    => 'integer',
        'department_id' => 'integer',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name'          => 'required|unique:clients,name',
        'email'         => 'nullable|email|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/',
        'website'       => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'department_id' => 'required|integer',
    ];

    public static $editRules = [
        'email'         => 'nullable|email|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/',
        'website'       => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'department_id' => 'required|integer',
    ];

    public static $messages = [
        'website.regex'          => 'Please enter valid url.',
        'email.regex'            => 'Please enter valid email.',
        'department_id.required' => 'Please select valid department.',
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
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
