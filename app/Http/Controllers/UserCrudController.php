<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Operations\ImportUsersOperation;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as ControllersUserCrudController;

class UserCrudController extends ControllersUserCrudController
{
    use ImportUsersOperation;
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
    }

    public function setupImportOperation()
    {
        $this->crud->addFields([
            [
                'name' => 'template_file',
                'label' => __('Template file'),
                'type' => 'custom_html',
                'value' => '<a href="#">'.__('Download template file').'</a>'
            ],
            [
                'name' => 'roles',
                'type' => 'select',
                'model' => \Backpack\PermissionManager\app\Models\Role::class,
                'attribute' => 'name'
            ],
            [
                'name' => 'import_file',
                'type' => 'upload',
                'upload' => true
            ]
        ]);

    }
}
