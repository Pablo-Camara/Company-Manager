<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $documentId = request()->input('id');
        $document = Document::find($documentId);

        $rules = [
            'name' => 'required|min:1|max:255|unique:users,name',
            'folder_id' => 'required|exists:folders,id',
            'description' => 'required'
        ];

        if (
            empty($document)
            ||
            (!empty($document) && empty($document->location))
            ||
            request()->has('location')
        ) {
            $rules = array_merge(
                $rules,
                [
                    'location' => 'required'
                ]
            );
        }

        return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [

        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __('The document name is required'),
            'name.max' => __('The document name cannot have more than 255 characters.'),
            'folder_id.required' => __('You must select container folder for this document'),
            'folder_id.exists' => __('The selected folder does not exist'),
            'location.required' => __('You are required to upload a file when creating a document'),
            'description.required' => __('Describe the document')

        ];
    }
}
