@extends('layouts.app')

@section('content')
<h2 class="mb-4">تعديل المناسبة</h2>

<form action="{{ route('attendance_events.update', $event->id) }}" method="POST" style="max-width:640px">
    @include('attendance_events._form', ['mode' => 'edit'])
</form>
@endsection
