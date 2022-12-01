<?php

namespace App\Http\Controllers;

use Backpack\PermissionManager\app\Http\Controllers\RoleCrudController as ControllersRoleCrudController;

class RoleCrudController extends ControllersRoleCrudController
{
    public function setup()
    {
        $user = backpack_user();
        if (!$user->isAdmin()) {
            abort(403);
        }
        parent::setup();
    }
}
