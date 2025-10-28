@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Thùng rác</h1>
            <a href="{{ route('todos.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Ngày xóa</th>
                    <th class="text-end">Hành động</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($todos as $todo)
                    <tr>
                        <td>{{ $todo->title }}</td>
                        <td>{{ optional($todo->deleted_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <form action="{{ route('todos.restore',$todo->id) }}" method="post" class="d-inline">
                                @csrf @method('PUT')
                                <button class="btn btn-sm btn-outline-success">Khôi phục</button>
                            </form>
                            <form action="{{ route('todos.forceDelete',$todo->id) }}" method="post" class="d-inline"
                                  onsubmit="return confirm('Xóa vĩnh viễn?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Xóa vĩnh viễn</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">Trống.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $todos->links() }}
    </div>
@endsection
