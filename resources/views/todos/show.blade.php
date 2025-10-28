@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">{{ $todo->title }}</h1>
            <div>
                <a href="{{ route('todos.edit',$todo) }}" class="btn btn-outline-primary">Sửa</a>
                <a href="{{ route('todos.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
        <p class="mb-2"><strong>Ưu tiên:</strong> {{ ucfirst($todo->priority) }}</p>
        <p class="mb-2"><strong>Trạng thái:</strong> {{ $todo->is_complete ? 'Đã hoàn thành' : 'Chưa xong' }}</p>
        @if($todo->description)
            <hr>
            <p>{{ $todo->description }}</p>
        @endif
    </div>
@endsection
