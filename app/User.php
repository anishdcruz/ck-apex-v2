<?php

namespace App;

// use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Support\Search;

class User extends Authenticatable
{
    // use Notifiable;
    use Search;

    protected $search = [
        'name', 'title', 'telephone', 'extension', 'mobile_number',
        'email'
    ];

    protected $columns = [
        'id', 'name', 'title', 'telephone', 'extension', 'mobile_number',
        'email', 'is_admin', 'is_active', 'created_at'
    ];

    protected $fillable = [
        'name', 'title', 'telephone', 'extension', 'mobile_number',
        'email', 'password', 'email_signature'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
