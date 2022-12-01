<?php

namespace App\Http\Controllers;

use Backpack\PermissionManager\app\Http\Controllers\PermissionCrudController;

class DocumentCategoryCrudController extends PermissionCrudController
{

    public function setup()
    {
        $user = backpack_user();
        if (!$user->isAdmin()) {
            abort(403);
        }
        parent::setup();
        $this->crud->setEntityNameStrings(__('Folder'), __('Folders'));
        $this->crud->setRoute(backpack_url('folders'));
    }


    public function setupListOperation()
    {
        $this->crud->addColumns([
                [
                    'name'  => 'name',
                    'label' => trans('backpack::permissionmanager.name'),
                    'type'  => 'text',
                    'limit' => 255
                ],
                [
                    'name'  => 'documents',
                    'label' => __('Total documents'),
                    'type'  => 'relationship_count',
                ]
            ]
        );

    }
}
