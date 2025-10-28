@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 class="h3 mb-3">Thêm Todo</h1>
        <form action="{{ route('todos.store') }}" method="post">
            @include('todos._form', ['submitLabel' => 'Tạo mới'])
        </form>
    </div>
@endsection
