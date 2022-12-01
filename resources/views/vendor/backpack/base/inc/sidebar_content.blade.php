@php
    $user = backpack_user();
    $userPermissions = $user->getPermissions();
    $folders = $user->getFolders($userPermissions);
@endphp
{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@if($user->hasRole('Admin'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>{{ __('Users') }}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>{{ __('Roles') }}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('physical-space') }}"><i class="nav-icon la la-map-marker"></i> {{ __('Physical spaces') }}</a></li>
@endif
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('anomaly') }}"><i class="nav-icon la la-bomb"></i> {{ __('Anomalies') }}</a></li>
@if($user->hasRole('Admin'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('folders') }}"><i class="nav-icon la la-folder"></i> <span>{{ __('Folders') }}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('document') }}"><i class="nav-icon la la-file"></i> {{ __('Documents') }}</a></li>
@endif

@if(!$user->hasRole('Admin'))
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="nav-icon la la-file"></i> {{ __('Documents') }}</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('document') }}"><i class="nav-icon la la-list"></i> <span>{{ __('View all') }}</span></a></li>
        @foreach($folders as $folder)
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('document?folder_id=' . $folder->id) }}"><i class="nav-icon la la-folder"></i> <span>{{ $folder->name }}</span></a></li>
        @endforeach
    </ul>
</li>

@endif
