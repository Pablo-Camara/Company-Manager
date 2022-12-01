@php
    $equipments = $crud->data['equipments'] ?? [];
    $selectedEquipment = $crud->data['equipment'] ?? null;
@endphp
<div style="margin: 10px 0 4px 0; display: inline-block;">
    <select class="equipment-filter" name="equipment_id">
        <option value="" {{ $selectedEquipment ? '' : 'selected' }}></option>
        @foreach($equipments as $equipment)
            <option value="{{ $equipment->id }}" {{ $selectedEquipment &&  $selectedEquipment->id === $equipment->id ? 'selected' : ''  }}>{{ $equipment->name }}</option>
        @endforeach
    </select>
</div>

@push('after_scripts')
    <script>

        $(document).ready(function() {
            $('.equipment-filter').select2({
                placeholder: "{{ __('Equipment') }}",
                width: 'style'
            }).on('select2:select', function (e) {
                const documentCategoryId = $(this).val();
                var currUrl = window.location.href;
                if (!documentCategoryId) {
                    window.location.href = removeURLParameter(currUrl, 'equipment_id');
                    return;
                }


                currUrl = removeURLParameter(currUrl, 'equipment_id');
                if (currUrl.indexOf('?') > -1){
                    currUrl += '&equipment_id=' + documentCategoryId;
                } else{
                    currUrl += '?equipment_id=' + documentCategoryId;
                }
                window.location.href = currUrl;
            });
        });
    </script>
@endpush
