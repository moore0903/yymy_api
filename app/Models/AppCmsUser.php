<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2018-5-4
 * Time: 10:31
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AppCmsUser extends Authenticatable
{
    use Notifiable;

    protected $table='app_cms_user';

    protected $guarded=[];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    use SoftDeletes;
}