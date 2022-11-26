<?php
namespace App\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\AdminController AS BackpackAdminController;

class AdminController extends BackpackAdminController
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title

        $breadcrumbs = [
            trans('backpack::crud.admin') => backpack_url('dashboard'),
            trans('backpack::base.dashboard') => false,
        ];

        $user = backpack_user();
        if (!$user->hasRole('Admin')) {
            array_splice($breadcrumbs, 0, 1);
        }

        $this->data['breadcrumbs'] = $breadcrumbs;
        return view(backpack_view('dashboard'), $this->data);
    }
}
