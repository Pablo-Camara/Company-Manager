@if ($crud->hasAccess('create'))
	<a href="{{ url($crud->route.'/import') }}" class="btn btn-primary mt-1 mt-md-0" data-style="zoom-in"><span class="ladda-label"><i class="la la-upload"></i> {{ __('Import') }} {{ $crud->entity_name_plural }}</span></a>
@endif
