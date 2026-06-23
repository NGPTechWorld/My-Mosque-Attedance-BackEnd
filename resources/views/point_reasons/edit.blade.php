@extends('layouts.app')

@section('content')
<h2 class="mb-4">تعديل السبب</h2>

<form action="{{ route('point_reasons.update', $reason->id) }}" method="POST" style="max-width:500px">
    @include('point_reasons._form', ['mode' => 'edit'])
</form>
@endsection
