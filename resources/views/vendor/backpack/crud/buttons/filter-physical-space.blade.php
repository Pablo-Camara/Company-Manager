@php
    $physicalSpaces = $crud->data['physical_spaces'] ?? [];
    $selectedPhysicalSpace = $crud->data['physical_space'] ?? null;
@endphp
<div style="margin: 10px 0 4px 0">
    <select class="physical-space-filter" name="physical_space_id" style="width: 300px">
        <option value="" {{ $selectedPhysicalSpace ? '' : 'selected' }}></option>
        @foreach($physicalSpaces as $physicalSpace)
            <option value="{{ $physicalSpace->id }}" {{ $selectedPhysicalSpace &&  $selectedPhysicalSpace->id === $physicalSpace->id ? 'selected' : ''  }}>{{ $physicalSpace->space_name }}</option>
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
            $('.physical-space-filter').select2({
                placeholder: "{{ __('Physical space') }}"
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
