<?php

namespace App\Http\Controllers;

use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as ControllersUserCrudController;

class UserCrudController extends ControllersUserCrudController
{
    public function setup()
    {
        $user = backpack_user();
        if (!$user->hasRole('Admin')) {
            abort(403);
        }
        parent::setup();
    }
}
