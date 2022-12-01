<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnomalyRequest;
use App\Mail\AnomalyReport;
use App\Models\Anomaly;
use App\Models\Configuration;
use App\Models\Equipment;
use App\Models\PhysicalSpace;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;

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
                'name' => 'equipment_id',
                'label' => __('Equipment'),
                'type' => 'select',
                'entity' => 'equipment'
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

        $user = backpack_user();
        if (!$user->isAdmin()) {
            $this->crud->addClause('where', 'user_id', '=', $user->id);
        }
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
            [
                'name' => 'equipment_id',
                'label' => __('Equipment'),
                'type' => 'select',
                'entity' => 'equipment'
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
        $user = backpack_user();
        $entryId = $this->crud->getCurrentEntryId();
        $anomaly = Anomaly::findOrFail($entryId);

        if (!$user->isAdmin() && $anomaly->user->id !== $user->id) {
            abort(403);
        }

        $this->setupCreateOperation();
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
        $anomaly = Anomaly::findOrFail($entryId);

        if (!$user->isAdmin() && $anomaly->user->id !== $user->id) {
            abort(403);
        }
    }


    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    public function setupShowOperation()
    {
        $user = backpack_user();
        $entryId = $this->crud->getCurrentEntryId();
        $anomaly = Anomaly::findOrFail($entryId);

        if (!$user->isAdmin() && $anomaly->user->id !== $user->id) {
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

        $reportedBy = [
            'name' => 'user_id',
            'label' => __('Reported by'),
            'value' => function($entry) {
                if ($entry->user) {
                    return [
                        $entry->user->name . ' (' . $entry->user->id . ')'
                    ];
                }

                return [
                    __('Unknown')
                ];
            }
        ];

        if (backpack_user()->isAdmin()) {
            $reportedBy['wrapper'] = [
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
            $reportedBy,
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
                'limit' => 65535
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

        $anomaliesDestinationEmail = Configuration::where('config_name', 'anomalies_destination_email')->first();

        if (!empty($anomaliesDestinationEmail)) {
            $destinationEmails = explode(',', $anomaliesDestinationEmail->config_value);
            $destinationEmails =  array_filter($destinationEmails);
            if (!empty($destinationEmails)) {
                try {
                    Mail::to($destinationEmails)->send(new AnomalyReport($item));
                } catch (\Throwable $th) {
                    \Alert::error(__('Could not report anomaly by email'))->flash();
                }
            }
        }

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
