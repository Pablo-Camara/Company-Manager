@component('mail::message')

# {{ __('New anomaly reported') }}

## {{ __('Reported by') }}:
{{ $anomaly->user->name . ' ( ' . $anomaly->user->email . ' )' }}

@if (!empty($anomaly->physicalSpace))
## {{ __('Physical space') }}:
{{ $anomaly->physicalSpace->space_name . ' (' . $anomaly->physicalSpace->id . ')' }}
@endif

@if (!empty($anomaly->equipment))
## {{ __('Equipment') }}:
{{ $anomaly->equipment->name . ' (' . $anomaly->equipment->id . ')' }}
@endif

## {{ __('Description') }}:
{{ $anomaly->description }}

@component('mail::button', ['url' => route('anomaly.show', ['id' => $anomaly->id])])
{{ __('View in backoffice') }}
@endcomponent

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent
