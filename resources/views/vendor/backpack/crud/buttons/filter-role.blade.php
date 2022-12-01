@php
    $roles = $crud->data['roles'] ?? [];
    $selectedRole = $crud->data['role'] ?? null;
@endphp
<div style="margin: 10px 0 4px 0;">
    <select class="role-filter" name="role_id">
        <option value="" {{ $selectedRole ? '' : 'selected' }}></option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ $selectedRole &&  $selectedRole->id === $role->id ? 'selected' : ''  }}>{{ $role->name }}</option>
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
            $('.role-filter').select2({
                placeholder: "{{ __('Role') }}",
                width: 'style'
            }).on('select2:select', function (e) {
                const documentCategoryId = $(this).val();
                var currUrl = window.location.href;
                if (!documentCategoryId) {
                    window.location.href = removeURLParameter(currUrl, 'role_id');
                    return;
                }


                currUrl = removeURLParameter(currUrl, 'role_id');
                if (currUrl.indexOf('?') > -1){
                    currUrl += '&role_id=' + documentCategoryId;
                } else{
                    currUrl += '?role_id=' + documentCategoryId;
                }
                window.location.href = currUrl;
            });
        });
    </script>
@endpush
