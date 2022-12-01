<?php

namespace App\Http\Controllers;

use App\Exports\UsersImportTemplate;
use App\Http\Controllers\Operations\ImportOperation;
use App\Http\Requests\ImportUsersRequest;
use App\Http\Requests\UserCreateRequest;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as ControllersUserCrudController;
use Backpack\PermissionManager\app\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

class UserCrudController extends ControllersUserCrudController
{
    use ImportOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $user = backpack_user();
        if (!$user->isAdmin()) {
            abort(403);
        }
        parent::setup();
    }

    public function setupListOperation()
    {
        parent::setupListOperation();
        $roleId = request()->input('role_id');
        $roles = Role::all();
        $this->crud->data['roles'] = $roles;
        if ($roleId) {
            $filterByRole = false;
            try {
                $role = Role::findOrFail($roleId);
                $filterByRole = true;
            } catch (\Throwable $th) { }

            if ($filterByRole) {
                $this->crud->addClause('whereHas', 'roles', function ($query) use ($roleId) {
                    $query->where('role_id', '=', $roleId);
                });

                $this->crud->data['role'] = $role;
            }
        }

        $this->crud->addButtonFromView('top', 'filter-role', 'filter-role', 'end');

        $this->crud->addColumn([
            'name' => 'nif'
        ]);
    }

    public function setupImportOperation()
    {
        $this->crud->setSaveActions(
            [
                [
                    'name' => 'import',
                    'redirect' => function($crud, $request, $itemId) {
                        return $crud->route;
                    }, // what's the redirect URL, where the user will be taken after saving?

                    // OPTIONAL:
                    'button_text' => __('Import'), // override text appearing on the button
                    // You can also provide translatable texts, for example:
                    // 'button_text' => trans('backpack::crud.save_action_one'),
                    'visible' => function($crud) {
                        return true;
                    }, // customize when this save action is visible for the current operation
                    'referrer_url' => function($crud, $request, $itemId) {
                        return $crud->route;
                    }, // override http_referrer_url
                    'order' => 1, // change the order save actions are in
                ]
            ]
        );

        $this->crud->setValidation(ImportUsersRequest::class);
        $this->crud->setEntityNameStrings(__('users'), __('Users'));
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
            ],
            [
                'name' => 'notice',
                'type' => 'custom_html',
                'value' => '<b>'. __('Notice') . ':</b> ' . __('The users password will be their fiscal number and they can change it after logging in.'),
                'attributes' => [
                    'disabled' => 'disabled',
                    'readonly' => 'readonly'
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


    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UserCreateRequest::class);
    }

    public function setupShowOperation()
    {
        parent::setupListOperation();
        $this->crud->addColumn([
            'name' => 'nif'
        ]);
    }

    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'nif',
                'label' => __('Fiscal number'),
                'type'  => 'text',
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'name'              => ['roles', 'permissions'],
                'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'name', // foreign key attribute that is shown to user
                        'model'            => config('permission.models.role'), // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label'          => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => config('permission.models.permission'), // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                ],
            ],
        ]);
    }
}
