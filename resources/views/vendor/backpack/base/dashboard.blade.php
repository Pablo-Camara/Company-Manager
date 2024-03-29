@extends(backpack_view('blank'))

@php
    $widgets['before_content'][] = [
        'type'        => 'jumbotron',
        'heading'     => trans('backpack::base.welcome'),
        'content'     => '',
        'button_link' => backpack_url('logout'),
        'button_text' => trans('backpack::base.logout'),
    ];

    $widgets['before_content'][] = [
        'type'     => 'view',
        'view'     => 'dashboard.recent-documents'
    ];
@endphp

@section('content')
@endsection
