<?php

namespace App\Http\Controllers;

use Backpack\PermissionManager\app\Http\Controllers\PermissionCrudController;

class DocumentCategoryCrudController extends PermissionCrudController
{

    public function setup()
    {
        parent::setup();
        $this->crud->setRoute(backpack_url('document-categories'));
    }
}
