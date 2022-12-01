@php
    $recentDocuments = backpack_user()->recentDocuments();
@endphp
<h2>{{ __('Recent documents') }}</h2>
@if($recentDocuments->count() > 0)
<table class="table">
  <thead>
    <tr>
      <th scope="col">{{ __('Name') }}</th>
      <th>{{ __('Created at') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach($recentDocuments as $recentDocument)
    <tr>
      <td>
        <a href="{{ route('document.show', ['id' => $recentDocument->id]) }}">
            {{ $recentDocument->name }}
        </a>
      </td>
      <td>{{ $recentDocument->created_at }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@else
<p>{{ __('No recent documents') }}</p>
@endif
