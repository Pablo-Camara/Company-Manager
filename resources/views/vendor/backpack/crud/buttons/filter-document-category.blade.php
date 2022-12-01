@php
    $documentCategories = $crud->data['folders'] ?? [];
    $selectedCategory = $crud->data['folder'] ?? null;
@endphp
<div style="margin: 10px 0 4px 0; ">
    <select class="document-category-filter" name="folder_id" style="width: 250px">
        <option value="" {{ $selectedCategory ? '' : 'selected' }}></option>
        @foreach($documentCategories as $docCategory)
            <option value="{{ $docCategory->id }}" {{ $selectedCategory &&  $selectedCategory->id === $docCategory->id ? 'selected' : ''  }}>{{ $docCategory->name }}</option>
        @endforeach
    </select>
</div>

@push('after_scripts')
    <script>

        $(document).ready(function() {
            $('.document-category-filter').select2({
                placeholder: "{{ __('Folder') }}",
                width: 'style'
            }).on('select2:select', function (e) {
                const documentCategoryId = $(this).val();
                var currUrl = window.location.href;
                if (!documentCategoryId) {
                    window.location.href = removeURLParameter(currUrl, 'folder_id');
                    return;
                }


                currUrl = removeURLParameter(currUrl, 'folder_id');
                if (currUrl.indexOf('?') > -1){
                    currUrl += '&folder_id=' + documentCategoryId;
                } else{
                    currUrl += '?folder_id=' + documentCategoryId;
                }
                window.location.href = currUrl;
            });
        });
    </script>
@endpush
