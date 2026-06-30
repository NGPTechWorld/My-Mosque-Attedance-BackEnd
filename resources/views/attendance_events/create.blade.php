@extends('layouts.app')

@section('content')
<h2 class="mb-4">إضافة مناسبة حضور</h2>

<form action="{{ route('attendance_events.store') }}" method="POST" style="max-width:640px">
    @include('attendance_events._form', ['mode' => 'create'])
</form>
@endsection
