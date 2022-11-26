{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@if(backpack_user()->hasRole('Admin'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>{{ __('Users') }}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>{{ __('Roles') }}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('folders') }}"><i class="nav-icon la la-folder"></i> <span>{{ __('Folders') }}</span></a></li>
@endif

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('document') }}"><i class="nav-icon la la-file"></i> {{ __('Documents') }}</a></li>
