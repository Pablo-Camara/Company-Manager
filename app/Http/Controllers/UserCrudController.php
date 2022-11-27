<?php

namespace App\Http\Controllers;

use App\Exports\UsersImportTemplate;
use App\Http\Controllers\Operations\ImportUsersOperation;
use App\Http\Requests\ImportUsersRequest;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as ControllersUserCrudController;
use Maatwebsite\Excel\Facades\Excel;

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

        $this->crud->addColumn([
            'name' => 'nif'
        ]);
    }

    public function setupImportOperation()
    {
        $this->crud->setValidation(ImportUsersRequest::class);
        $this->crud->setEntityNameStrings(__('/ Import users'), __('Users'));
        $this->crud->addFields([
            [
                'name' => 'template_file',
                'type' => 'custom_html',
                'value' => '<a href="'.route('users.import.template').'">'.__('Download example import file').'</a>'
            ],
            [
                'name' => 'roles',
                'label' => __('Roles'),
                'type' => 'select_multiple',
                'model' => \Backpack\PermissionManager\app\Models\Role::class,
                'attribute' => 'name'
            ],
            [
                'name' => 'import_file',
                'label' => __('Import file'),
                'type' => 'upload',
                'upload' => true,
                'attributes' => [
                    'required' => 'required'
                ]
            ]
        ]);

    }

    public function downloadImportFileTemplate()
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@gmail.com',
                'nif' => '270100200'
            ],
            [
                'name' => 'Johana Doe',
                'email' => 'johana@gmail.com',
                'nif' => '270100201'
            ],
        ];

        return Excel::download(new UsersImportTemplate($users), 'users.csv');
    }
}
