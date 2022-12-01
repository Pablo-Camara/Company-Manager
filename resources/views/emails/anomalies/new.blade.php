@component('mail::message')

# {{ __('New anomaly reported') }}

## {{ __('Reported by') }}:
{{ $anomaly->user->name . ' ( ' . $anomaly->user->email . ' )' }}

## {{ __('Physical space') }}:
{{ $anomaly->physicalSpace->space_name . ' (' . $anomaly->physicalSpace->id . ')' }}

## {{ __('Description') }}:
{{ $anomaly->description }}

@component('mail::button', ['url' => route('anomaly.show', ['id' => $anomaly->id])])
{{ __('View in backoffice') }}
@endcomponent

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent
