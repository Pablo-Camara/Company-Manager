@php
    $roles = $crud->data['roles'] ?? [];
    $selectedRole = $crud->data['role'] ?? null;
@endphp
<div style="margin: 10px 0 4px 0;">
    <select class="role-filter" name="role_id" style="width: 250px">
        <option value="" {{ $selectedRole ? '' : 'selected' }}></option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ $selectedRole &&  $selectedRole->id === $role->id ? 'selected' : ''  }}>{{ $role->name }}</option>
        @endforeach
    </select>
</div>

@push('after_scripts')
    <script>

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
