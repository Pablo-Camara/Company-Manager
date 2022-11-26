<?php

namespace App\Http\Controllers;

use Backpack\PermissionManager\app\Http\Controllers\PermissionCrudController;

class DocumentCategoryCrudController extends PermissionCrudController
{

    public function setup()
    {
        $user = backpack_user();
        if (!$user->hasRole('Admin')) {
            abort(403);
        }
        parent::setup();
        $this->crud->setEntityNameStrings(__('Document category'), __('Document categories'));
        $this->crud->setRoute(backpack_url('document-categories'));
    }
}
