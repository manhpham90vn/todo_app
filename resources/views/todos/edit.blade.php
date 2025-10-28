@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 class="h3 mb-3">Sửa Todo</h1>
        <form action="{{ route('todos.update', $todo) }}" method="post">
            @method('PUT')
            @include('todos._form', ['submitLabel' => 'Cập nhật'])
        </form>
    </div>
@endsection
