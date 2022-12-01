<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnomalyRequest;
use App\Models\PhysicalSpace;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AnomalyCrudController
 * @package App\Http\Controllers
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AnomalyCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Anomaly::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/anomaly');
        CRUD::setEntityNameStrings(__('anomaly'), __('anomalies'));
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
                'name' => 'physical_space_id',
                'label' => __('Physical space'),
                'type' => 'select',
                'entity' => 'physicalSpace'
            ],
            [
                'name' => 'created_at',
                'label' => __('Created at')
            ],
            [
                'name' => 'updated_at',
                'label' => __('Updated at')
            ],
            [
                'name' => 'description',
                'label' => __('Description')
            ],
        ]);


        $physicalSpaceId = request()->input('physical_space_id');
        $physicalSpaces = PhysicalSpace::all();

        $this->crud->data['physical_spaces'] = $physicalSpaces;

        if ($physicalSpaceId) {
            try {
                $physicalSpace = PhysicalSpace::find($physicalSpaceId);
            } catch (\Throwable $th) {
                abort(404);
            }

            $this->crud->addClause('where', 'physical_space_id', '=', $physicalSpace->id);
            $this->crud->data['physical_space'] = $physicalSpace;
        }

        $this->crud->addButtonFromView('top', 'filter-physical-space', 'filter-physical-space', 'end');

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
        CRUD::setValidation(AnomalyRequest::class);

        $this->crud->addFields([
            [
                'name' => 'description',
                'label' => __('Description')
            ],
            [
                'name' => 'physical_space_id',
                'label' => __('Physical space'),
                'type' => 'select',
                'entity' => 'physicalSpace'
            ],
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    public function setupShowOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'physical_space_id',
                'label' => __('Physical space'),
                'type' => 'select',
                'entity' => 'physicalSpace'
            ],
            [
                'name' => 'created_at',
                'label' => __('Created at')
            ],
            [
                'name' => 'updated_at',
                'label' => __('Updated at')
            ],
            [
                'name' => 'description',
                'label' => __('Description'),
                'limit' => -1
            ],
        ]);
    }
}
