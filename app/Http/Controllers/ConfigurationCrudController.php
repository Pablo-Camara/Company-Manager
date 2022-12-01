<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigurationRequest;
use App\Models\Configuration;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Validator;

/**
 * Class ConfigurationCrudController
 * @package App\Http\Controllers
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ConfigurationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        $user = backpack_user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        CRUD::setModel(\App\Models\Configuration::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/configuration');
        CRUD::setEntityNameStrings(__('configuration'), __('configurations'));

        $this->crud->denyAccess(['create', 'delete']);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'config_name',
                'label' => __('Config name'),
                'value' => function($configuration) {
                    return trans('configurations.' . $configuration->config_name);
                },
                'limit' => 255
            ],
            [
                'name' => 'config_value',
                'label' => __('Config value')
            ]
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ConfigurationRequest::class);

        $value = null;
        $entryId = $this->crud->getCurrentEntryId();
        if ($entryId) {
            $entry = Configuration::findOrFail($entryId);
            $value = trans('configurations.' . $entry->config_name);
        }

        $this->crud->addFields([
            [
                'name' => 'config_name',
                'label' => __('Config name'),
                'value' => $value,
                'attributes' => [
                    'disabled' => 'disabled',
                    'readonly' => 'readonly'
                ]
            ],
            [
                'name' => 'config_value',
                'label' => __('Config value')
            ]
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }


    /**
     * Update the specified resource in the database.
     *
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        $all = request()->all();
        if (
            !empty($all)
            &&
            (isset($all['id']) && !empty($all['id']))
            &&
            (isset($all['_method']) && $all['_method'] === 'PUT')
            &&
            (isset($all['config_value']) && !empty($all['config_value']))
        ) {
            $configId = $all['id'];
            $configuration = Configuration::findOrFail($configId);

            switch ($configuration->config_name) {
                case 'anomalies_destination_email':
                case 'requisitions_destination_email':
                    $configValue = explode(',', $all['config_value']);
                    $configValue = array_map(
                        function ($configValue) {
                            return trim($configValue);
                        },
                        $configValue
                    );
                    $configValue = array_unique(array_filter($configValue));
                    $errors = 0;
                    foreach($configValue as $email) {
                        $validator = Validator::make(
                            ['email' => $email],
                            [
                                'email' => 'email'
                            ]
                        );

                        if ($validator->fails()) {
                            $errors++;
                            \Alert::error(__('Invalid email') . ': ' . $email)->flash();
                        }
                    }

                    if ($errors > 0) {
                        return redirect()->back()->withInput();
                    }
                    request()->merge([
                        'config_value' => implode(',', $configValue)
                    ]);
                    break;
            }
        }

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // update the row in the db
        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest($request)
        );
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }



    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'config_name',
                'label' => __('Config name'),
                'value' => function($configuration) {
                    return trans('configurations.' . $configuration->config_name);
                },
                'limit' => 255
            ],
            [
                'name' => 'config_value',
                'label' => __('Config value'),
                'limit' => 65535
            ]
        ]);
    }
}
