{{-- Wrapper kept for module views; the canonical structure lives in layouts/admin.blade.php. --}}
@section('title', $attributes->get('title', 'Dashboard'))
@section('content')
    {{ $slot }}
@endsection

@include('layouts.admin')
