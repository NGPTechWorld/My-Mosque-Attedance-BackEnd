@extends('layouts.app')

@section('content')
<h2 class="mb-4">إضافة سبب جديد</h2>

<form action="{{ route('point_reasons.store') }}" method="POST" style="max-width:500px">
    @include('point_reasons._form', ['mode' => 'create'])
</form>
@endsection
