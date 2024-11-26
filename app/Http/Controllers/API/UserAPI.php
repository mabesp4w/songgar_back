<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class UserAPI extends Controller
{
    function show($id)
    {
        $user = User::find($id);
        $showPassword = $user->getAttribute('show_password');

        // Mengubah user ke array dan menambahkan show_password
        $userArray = $user->toArray();
        $userArray['show_password'] = $showPassword;

        return new CrudResource('success', 'Data User', $userArray);
    }
}
