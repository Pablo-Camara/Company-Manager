@php
    $documentCategories = $crud->data['documentCategories'] ?? [];
    $selectedCategory = $crud->data['documentCategory'] ?? null;
@endphp
<div style="margin: 10px 0 4px 0">
    <select class="document-category-filter" name="folder_id" style="width: 300px">
        <option value="" {{ $selectedCategory ? '' : 'selected' }}></option>
        @foreach($documentCategories as $docCategory)
            <option value="{{ $docCategory->id }}" {{ $selectedCategory &&  $selectedCategory->id === $docCategory->id ? 'selected' : ''  }}>{{ $docCategory->name }}</option>
        @endforeach
    </select>
</div>

@push('after_scripts')
    <script>
        function removeURLParameter(url, parameter) {
            var urlparts = url.split('?');
            if (urlparts.length >= 2) {

                var prefix = encodeURIComponent(parameter) + '=';
                var pars = urlparts[1].split(/[&;]/g);

                for (var i = pars.length; i-- > 0;) {
                    if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                        pars.splice(i, 1);
                    }
                }

                return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
            }
            return url;
        }

        $(document).ready(function() {
            $('.document-category-filter').select2({
                placeholder: "{{ __('Folder') }}"
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
