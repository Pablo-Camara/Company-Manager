@component('mail::message')

# {{ __('New requisition') }}

## {{ __('Requested by') }}:
{{ $requisition->user->name . ' ( ' . $requisition->user->email . ' )' }}

@if (!empty($requisition->physicalSpace))
## {{ __('Physical space') }}:
{{ $requisition->physicalSpace->space_name . ' (' . $requisition->physicalSpace->id . ')' }}
@endif

@if (!empty($requisition->equipment))
## {{ __('Equipment') }}:
{{ $requisition->equipment->name . ' (' . $requisition->equipment->id . ')' }}
@endif

## {{ __('Motive') }}:
{{ $requisition->motive }}

@component('mail::button', ['url' => route('requisition.show', ['id' => $requisition->id])])
{{ __('View in backoffice') }}
@endcomponent

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent
