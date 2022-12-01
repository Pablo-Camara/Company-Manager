@php
    $physicalSpaces = $crud->data['physical_spaces'] ?? [];
    $selectedPhysicalSpace = $crud->data['physical_space'] ?? null;
@endphp
<br/>
<div style="margin: 10px 0 4px 0; display: inline-block;">
    <select class="physical-space-filter" name="physical_space_id">
        <option value="" {{ $selectedPhysicalSpace ? '' : 'selected' }}></option>
        @foreach($physicalSpaces as $physicalSpace)
            <option value="{{ $physicalSpace->id }}" {{ $selectedPhysicalSpace &&  $selectedPhysicalSpace->id === $physicalSpace->id ? 'selected' : ''  }}>{{ $physicalSpace->space_name }}</option>
        @endforeach
    </select>
</div>

@push('after_scripts')
    <script>

        $(document).ready(function() {
            $('.physical-space-filter').select2({
                placeholder: "{{ __('Physical space') }}",
                width: 'style'
            }).on('select2:select', function (e) {
                const documentCategoryId = $(this).val();
                var currUrl = window.location.href;
                if (!documentCategoryId) {
                    window.location.href = removeURLParameter(currUrl, 'physical_space_id');
                    return;
                }


                currUrl = removeURLParameter(currUrl, 'physical_space_id');
                if (currUrl.indexOf('?') > -1){
                    currUrl += '&physical_space_id=' + documentCategoryId;
                } else{
                    currUrl += '?physical_space_id=' + documentCategoryId;
                }
                window.location.href = currUrl;
            });
        });
    </script>
@endpush
