<?php

namespace App\Http\Controllers\Operations;

use App\Imports\UsersImport;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

trait ImportOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param  string  $segment  Name of the current entity (singular). Used as first URL segment.
     * @param  string  $routeName  Prefix of the route name.
     * @param  string  $controller  Name of the current CrudController.
     */
    protected function setupImportRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/import', [
            'as'        => $routeName.'.getImport',
            'uses'      => $controller.'@importShowView',
            'operation' => 'import',
        ]);

        Route::post($segment.'/import', [
            'as'        => $routeName.'.postImport',
            'uses'      => $controller.'@importPost',
            'operation' => 'import',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupImportDefaults()
    {
        $this->crud->allowAccess('import');

        $this->crud->operation('import', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButtonFromView('top', 'import-users', 'import-users', 'end');
        });
    }

    /**
     * Show the form for creating inserting a new row.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function importShowView()
    {
        $this->crud->hasAccessOrFail('import');
        $this->crud->setOperation('import');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('vendor.backpack.crud.import', $this->data);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importPost()
    {
        $this->crud->hasAccessOrFail('import');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $importFile = request()->file('import_file');
        $roles = request()->input('roles');

        try {
            $result = (object) ['count' => 0];
            Excel::import(new UsersImport($result, $roles), $importFile);

            if($result->count > 0) {
                \Alert::success($result->count . ' ' . __('Users imported successfully'))->flash();
            } else {
                \Alert::error($result->count . ' ' . __('Users imported'))->flash();
            }

        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                \Alert::error(__('You tried to import an user with an email that is already taken'))->flash();
            } else {
                \Alert::error(__('Something went wrong, check the import file and try again'))->flash();
            }
        }

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction();
    }
}
