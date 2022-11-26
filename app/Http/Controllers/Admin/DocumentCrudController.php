<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;

/**
 * Class DocumentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DocumentCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Document::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/document');
        CRUD::setEntityNameStrings('document', 'documents');

        $user = backpack_user();
        if (!$user->hasRole('Admin')) {
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('update');
            $this->crud->denyAccess('delete');
        }
    }

    private function addColumns($except = []) {
        $columns = [
            [
                'name' => 'name',
            ],
            [
                'name' => 'document_category_link',
                'type' => 'custom_html',
                'value' => function ($entry) {
                    $documentCategory = DocumentCategory::findById($entry->document_category_id);
                    if (empty($documentCategory)) {
                        return '';
                    }
                    $documentCategoryFilter = '?document_category_id=' . $documentCategory->id;
                    $filterLink = backpack_url('document' . $documentCategoryFilter);
                    return '<a href="' . $filterLink . '">' . $documentCategory->name . '</a>';
                }
            ],
            [
                'name' => 'description',
            ],
            [
                'name' => 'location',
                'type' => 'custom_html',
                'value' => function ($entry) {
                    return '<a href="' . backpack_url('documents/download/' . $entry->id) . '">' . __('Download') . '</a>';
                }
            ]
        ];

        $this->crud->addColumns(array_filter(
            $columns,
            function ($col) use ($except) {
                return !in_array($col['name'], $except);
            }
        ));
    }
    protected function setupShowOperation () {
        $documentId = $this->crud->getCurrentEntryId();
        $document = Document::with('documentCategory')->findOrFail($documentId);
        $user = backpack_user();
        if (!$user->can($document->documentCategory->name) && !$user->hasRole('Admin')) {
            abort(403);
        }

        if (!$user->hasRole('Admin')) {
            $this->crud->removeAllButtons();
        }

        $this->addColumns();
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
        $documentCategoryId = request()->input('document_category_id');
        if ($documentCategoryId) {
            try {
                $documentCategory = DocumentCategory::findById($documentCategoryId);
            } catch (\Throwable $th) {
                abort(404);
            }

            if (!$user->can($documentCategory->name) && !$user->hasRole('Admin')) {
                abort(403);
            }

            $this->crud->addClause('where', 'document_category_id', '=', $documentCategory->id);
            $this->crud->data['documentCategory'] = $documentCategory;
            $this->crud->addButtonFromView('top', 'filter-document-category', 'filter-document-category', 'end');
        }


        if (!$user->hasRole('Admin')) {
            $userPermissions = $user->getAllPermissions();
            $userPermissions = $userPermissions->pluck('id')->toArray();

            $this->crud->addClause('whereIn', 'document_category_id', $userPermissions);
        }


        $this->addColumns(['description']);

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
        CRUD::setValidation(DocumentRequest::class);
        $this->crud->addFields([
            [
                'name' => 'name',
            ],
            [
                'name' => 'document_category_id',
                'type' => 'select',
                'entity' => 'documentCategory'
            ],
            [
                'name' => 'description',
            ],
            [
                'name' => 'location',
                'type' => 'upload',
                'upload'    => true,
                'disk'      => 'documents',
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
        $this->setupCreateOperation();
    }


    public function downloadDocument(Document $document) {
        $user = backpack_user();
        if (!$user->can($document->documentCategory->name) && !$user->hasRole('Admin')) {
            abort(403);
        }
        $disk = Storage::disk('documents');
        if ($disk->exists($document->location) ) {
            return Storage::download('documents/' . $document->location);
        }
        abort(404);
    }
}
