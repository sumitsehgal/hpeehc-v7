<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'hasMapView'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->orWhere('username', $identifier)->first();
    }

    public function saveAssets($request) {
        $this->assets()->delete();

        $role = $request->role;

        if(isset($request->{$role})) {
            $assets = $request->{$role};
            foreach($assets as $asset) {
                $assetM = Asset::create([
                    'user_id' => $this->id,
                    'object_id' => $asset,
                    'object_type' => $role
                ]);
            }
        }

    }


    public function assets() {
        return $this->hasMany('App\Asset', 'user_id');
    }

    public function realAssets() {
        return $this->assets()->whereIn('object_type', $this->getRoleNames());
    }

    public function realAssetsByRole($role) {
        return $this->realAssets()->where('object_type', $role);
    }
}
