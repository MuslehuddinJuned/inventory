<?php

namespace App;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject //, MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url',
    ];

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
    }

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function inventories(){
        return $this->hasMany(Inventory::class);
    }

    public function inventoryissues(){
        return $this->hasMany(Inventoryissue::class);
    }

    public function inventoryreceives(){
        return $this->hasMany(Inventoryreceive::class);
    }

    public function invenrecall(){
        return $this->hasMany(Invenrecall::class);
    }

    public function rechead(){
        return $this->hasMany(Rechead::class);
    }

    public function recdetails(){
        return $this->hasMany(Recdetails::class);
    }

    public function producthead(){
        return $this->hasMany(Producthead::class);
    }

    public function productdetails(){
        return $this->hasMany(Productdetails::class);
    }

    public function wipissue(){
        return $this->hasMany(Wipissue::class);
    }

    public function wipreceive(){
        return $this->hasMany(Wipreceive::class);
    }

    public function polist(){
        return $this->hasMany(Polist::class);
    }

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    public function store(){
        return $this->hasMany(Store::class);
    }
}
