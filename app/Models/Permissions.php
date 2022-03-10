<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Permissions extends Model
{
    use HasFactory;

    //Relationships
    public function roles(){
        return $this->belongsToMany(Permissions::class, 'roles_permissions', 'permission_id', 'role_id');
    }

    public function hasPermission(String $requestedPermission){
        $user = Auth::user();
        $requestedPermissionId = Permissions::where('name', $requestedPermission)->first()->id;

        return !count(Permissions::where('permission_id', $requestedPermissionId)->get());
    }
}
