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

    public function setupListOperation()
    {
        parent::setupListOperation();
        $roleId = request()->input('role');

        if ($roleId) {
            $this->crud->addClause('whereHas', 'roles', function ($query) use ($roleId) {
                $query->where('role_id', '=', $roleId);
            });
        }

        $this->crud->addButtonFromView('top', 'import-users', 'import-users', 'end');
    }
}
