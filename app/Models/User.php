<?php

namespace App\Models;

use App\Notifications\MailResetPasswordNotification;
use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property bool $set_password
 * @property int $is_email_verified
 * @property int $is_active
 * @property string|null $activation_code
 * @property int|null $created_by
 * @property string|null $remember_token
 * @property int $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdUser
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSetPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 *
 * @property string $image_path
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereImagePath($value)
 * @mixin \Eloquent
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User withRole($role)
 *
 * @property-read mixed $img_avatar
 * @property int|null $salary
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read int|null $projects_count
 * @property-read int|null $roles_count
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSalary($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withoutTrashed()
 */
class User extends Authenticatable
{
    use Notifiable;
    use ImageTrait;
    use softDeletes;
    use HasRoles;
    use ImageTrait {
        deleteImage as traitDeleteImage;
    }

    public $table = 'users';
    const IMAGE_PATH = 'users';
    protected $appends = ['img_avatar'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'created_by',
        'is_email_verified',
        'activation_code',
        'is_active',
        'image_path',
        'deleted_by',
        'salary',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'set_password'      => 'boolean',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name'                  => 'required|unique:users,name',
        'email'                 => 'required|email|unique:users,email|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/',
        'phone'                 => 'nullable|numeric|digits:10',
        'role_id'               => 'required',
        'password'              => 'nullable|min:6|required_with:password_confirmation|same:password_confirmation',
        'password_confirmation' => 'nullable|min:6',
    ];

    public static $messages = [
        'phone.digits'     => 'The phone number must be 10 digits long.',
        'email.regex'      => 'Please enter valid email.',
        'photo.mimes'      => 'The profile image must be a file of type: jpeg, jpg, png.',
        'role_id.required' => 'Please select user role.',
    ];

    public static $setPasswordRules = [
        'user_id'               => 'required',
        'password'              => 'min:6|required_with:password_confirmation|same:password_confirmation',
        'password_confirmation' => 'min:6',
    ];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function createdUser()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    /**
     * @return string
     */
    public function getImgAvatarAttribute()
    {
        $userAvtar = $this->getOriginal('image_path');
        if (isset($userAvtar) && !empty($userAvtar)) {
            return $this->imageUrl(self::IMAGE_PATH.DIRECTORY_SEPARATOR.$userAvtar);
        }

        return getUserImageInitial($this->id, $this->name);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getImagePathAttribute($value)
    {
        if (!empty($value)) {
            return $this->imageUrl(self::IMAGE_PATH.DIRECTORY_SEPARATOR.$value);
        }

        return getUserImageInitial($this->id, $this->name);
    }

    /**
     * @return bool
     */
    public function deleteImage()
    {
        $image = $this->getOriginal('image_path');
        if (empty($image)) {
            return true;
        }

        return $this->traitDeleteImage(self::IMAGE_PATH.DIRECTORY_SEPARATOR.$image);
    }
}
