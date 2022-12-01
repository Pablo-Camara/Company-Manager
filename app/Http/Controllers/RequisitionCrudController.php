<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequisitionRequest;
use App\Mail\Requisition;
use App\Models\Configuration;
use App\Models\Equipment;
use App\Models\PhysicalSpace;
use App\Models\Requisition as ModelsRequisition;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;

/**
 * Class RequisitionCrudController
 * @package App\Http\Controllers
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequisitionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Requisition::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/requisition');
        CRUD::setEntityNameStrings(__('requisition'), __('requisitions'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $user = backpack_user();
        if (!$user->isAdmin()) {
            $this->crud->addClause('where', 'user_id', '=', $user->id);
        }

        $this->crud->addColumns([
            [
                'name' => 'physical_space_id',
                'label' => __('Physical space'),
                'type' => 'select',
                'entity' => 'physicalSpace'
            ],
            [
                'name' => 'equipment_id',
                'label' => __('Equipment'),
                'type' => 'select',
                'entity' => 'equipment'
            ],
            [
                'name' => 'motive',
                'label' => __('Motive')
            ],
            [
                'name' => 'created_at',
                'label' => __('Created at')
            ],
            [
                'name' => 'updated_at',
                'label' => __('Updated at')
            ],
        ]);

        $physicalSpaceId = request()->input('physical_space_id');
        $physicalSpaces = PhysicalSpace::all();
        $this->crud->data['physical_spaces'] = $physicalSpaces;

        if ($physicalSpaceId) {
            $filterByPhysicalSpace = false;
            try {
                $physicalSpace = PhysicalSpace::findOrFail($physicalSpaceId);
                $filterByPhysicalSpace = true;
            } catch (\Throwable $th) {}

            if($filterByPhysicalSpace) {
                $this->crud->addClause('where', 'physical_space_id', '=', $physicalSpace->id);
                $this->crud->data['physical_space'] = $physicalSpace;
            }
        }

        $this->crud->addButtonFromView('top', 'filter-physical-space', 'filter-physical-space', 'end');

        $equipmentId = request()->input('equipment_id');
        $equipments = Equipment::all();
        $this->crud->data['equipments'] = $equipments;

        if ($equipmentId) {
            $filterByEquipment = false;
            try {
                $equipment = Equipment::findOrFail($equipmentId);
                $filterByEquipment = true;
            } catch (\Throwable $th) {}

            if($filterByEquipment) {
                $this->crud->addClause('where', 'equipment_id', '=', $equipment->id);
                $this->crud->data['equipment'] = $equipment;
            }
        }
        $this->crud->addButtonFromView('top', 'filter-equipment', 'filter-equipment', 'end');

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
        CRUD::setValidation(RequisitionRequest::class);

        $this->crud->addFields([
            [
                'name' => 'physical_space_id',
                'label' => __('Physical space'),
                'type' => 'select',
                'entity' => 'physicalSpace'
            ],
            [
                'name' => 'equipment_id',
                'label' => __('Equipment'),
                'type' => 'select',
                'entity' => 'equipment'
            ],
            [
                'name' => 'motive',
                'label' => __('Motive')
            ]
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
        $user = backpack_user();
        $entryId = $this->crud->getCurrentEntryId();
        $requisition = ModelsRequisition::findOrFail($entryId);

        if (!$user->isAdmin() && $requisition->user->id !== $user->id) {
            abort(403);
        }

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
        $user = backpack_user();
        $entryId = $this->crud->getCurrentEntryId();
        $requisition = ModelsRequisition::findOrFail($entryId);

        if (!$user->isAdmin() && $requisition->user->id !== $user->id) {
            abort(403);
        }

        $physicalSpace = [
            'name' => 'physical_space_id',
            'label' => __('Physical space'),
            'type' => 'select',
            'entity' => 'physicalSpace'
        ];

        $equipment = [
            'name' => 'equipment_id',
            'label' => __('Equipment'),
            'type' => 'select',
            'entity' => 'equipment'
        ];

        $requestedBy = [
            'name' => 'user_id',
            'label' => __('Requested by'),
            'value' => function($entry) {
                return [
                    $entry->user->name . ' (' . $entry->user->id . ')'
                ];
            }
        ];

        if (backpack_user()->isAdmin()) {
            $requestedBy['wrapper'] = [
                'element' => 'a',
                'href' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->user) {
                        return route('user.show', ['id' => $entry->user->id]);
                    }
                    return '#';
                }
            ];

            $physicalSpace['wrapper'] = [
                'element' => 'a',
                'href' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->physicalSpace) {
                        return route('physical-space.show', ['id' => $entry->physicalSpace->id]);
                    }
                    return '#';
                }
            ];

            $equipment['wrapper'] = [
                'element' => 'a',
                'href' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->equipment) {
                        return route('equipment.show', ['id' => $entry->equipment->id]);
                    }
                    return '#';
                }
            ];
        }


        $this->crud->addColumns([
            $physicalSpace,
            $equipment,
            $requestedBy,
            [
                'name' => 'motive',
                'label' => __('Motive'),
                'limit' => 65535
            ],
            [
                'name' => 'created_at',
                'label' => __('Created at')
            ],
            [
                'name' => 'updated_at',
                'label' => __('Updated at')
            ],
        ]);
    }


    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        $insertData = $this->crud->getStrippedSaveRequest($request);
        $insertData['user_id'] = backpack_user()->id;
        // insert item in the db
        $item = $this->crud->create($insertData);
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        $requisitionsDestinationEmail = Configuration::where('config_name', 'requisitions_destination_email')->first();

        if (!empty($requisitionsDestinationEmail)) {
            $destinationEmails = explode(',', $requisitionsDestinationEmail->config_value);
            $destinationEmails =  array_filter($destinationEmails);
            if (!empty($destinationEmails)) {
                try {
                    Mail::to($destinationEmails)->send(new Requisition($item));
                } catch (\Throwable $th) {
                    \Alert::error(__('Could not send the requisition by email'))->flash();
                }
            }
        }

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }


    /**
     * Define what happens when the Delete operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupDeleteOperation()
    {
        $user = backpack_user();
        $entryId = $this->crud->getCurrentEntryId();
        $requisition = ModelsRequisition::findOrFail($entryId);

        if (!$user->isAdmin() && $requisition->user->id !== $user->id) {
            abort(403);
        }
    }
}
