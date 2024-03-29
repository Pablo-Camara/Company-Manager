<?php

namespace App\Http\Controllers;

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
        CRUD::setEntityNameStrings(__('Document'), __('Documents'));

        $user = backpack_user();
        if (!$user->isAdmin()) {
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('update');
            $this->crud->denyAccess('delete');
        }
    }

    private function addColumns($except = []) {
        $columns = [
            [
                'name' => 'name',
                'label' => __('Name'),
                'limit' => 255
            ],
            [
                'name' => 'document_category_link',
                'label' => __('Folder'),
                'type' => 'custom_html',
                'value' => function ($entry) {
                    $documentCategory = DocumentCategory::findById($entry->folder_id);
                    if (empty($documentCategory)) {
                        return '';
                    }
                    $documentCategoryFilter = '?folder_id=' . $documentCategory->id;
                    $filterLink = backpack_url('document' . $documentCategoryFilter);
                    return '<a href="' . $filterLink . '">' . $documentCategory->name . '</a>';
                }
            ],
            [
                'name' => 'description',
                'label' => __('Description'),
                'type' => 'custom_html',
                'value' => function ($entry) {
                    return $entry->description;
                }
            ],
            [
                'name' => 'location',
                'label' => __('File'),
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
        $document = Document::with('folder')->findOrFail($documentId);
        $user = backpack_user();
        if (!$user->can($document->folder->name) && !$user->isAdmin()) {
            abort(403);
        }

        if (!$user->isAdmin()) {
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
        $userPermissions = $user->getPermissions();
        $documentCategoryId = request()->input('folder_id');
        $documentCategories = $user->getFolders($userPermissions);

        $this->crud->data['folders'] = $documentCategories;

        if ($documentCategoryId) {
            try {
                $documentCategory = DocumentCategory::findById($documentCategoryId);
            } catch (\Throwable $th) {
                abort(404);
            }

            if (!$user->can($documentCategory->name) && !$user->isAdmin()) {
                abort(403);
            }
            $this->crud->addClause('where', 'folder_id', '=', $documentCategory->id);
            $this->crud->data['folder'] = $documentCategory;
        }

        $this->crud->addButtonFromView('top', 'filter-document-category', 'filter-document-category', 'end');


        if (!$user->isAdmin()) {
            $this->crud->addClause('whereIn', 'folder_id', $userPermissions);
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



        $locationField = [
            'name' => 'location',
            'label' => __('File'),
            'type' => 'upload',
            'upload'    => true,
            'disk'      => 'documents',
        ];

        if ($this->crud->getCurrentOperation() === 'update') {
            $locationField = array_merge(
                $locationField,
                [
                    'file_link' => backpack_url('documents/download/' . $this->crud->getCurrentEntryId()),
                ]
            );
        }
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => __('Name')
            ],
            [
                'name' => 'folder_id',
                'label' => __('Folder'),
                'type' => 'select',
                'entity' => 'folder'
            ],
            [
                'name' => 'description',
                'label' => __('Description')
            ],
            $locationField
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
        if (!$user->can($document->folder->name) && !$user->isAdmin()) {
            abort(403);
        }
        $disk = Storage::disk('documents');
        if ($disk->exists($document->location) ) {
            return Storage::download('documents/' . $document->location);
        }
        abort(404);
    }
}
