<div style="margin: 10px 0 4px 0">
    Filtering by category: <b>{{ $crud->data['documentCategory']->name }}</b> <a href="{{ url()->current().'?'.http_build_query(request()->except('document_category_id')) }}">(remove filter)</a>
</div>
