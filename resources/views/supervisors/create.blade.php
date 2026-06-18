@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">إضافة مشرف</h2>
        <a href="{{ route('supervisors.index') }}" class="btn btn-outline-secondary">رجوع</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm" style="max-width: 760px;">
        <div class="card-body">
            <form method="POST" action="{{ route('supervisors.store') }}">
                @csrf
                @include('supervisors._form')
                <button class="btn btn-primary">حفظ المشرف</button>
            </form>
        </div>
    </div>
@endsection
