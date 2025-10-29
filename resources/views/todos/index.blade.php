@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Todos</h1>
            <div>
                <a href="{{ route('todos.trash') }}" class="btn btn-outline-secondary me-2">Thùng rác</a>
                <a href="{{ route('todos.create') }}" class="btn btn-primary">+ Thêm</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form class="row g-2 mb-3">
            <div class="col-auto">
                <select name="priority" class="form-select" onchange="this.form.submit()">
                    <option value="">— Ưu tiên —</option>
                    <option value="high" {{ request('priority')=='high'?'selected':'' }}>Cao</option>
                    <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Trung bình</option>
                    <option value="low" {{ request('priority')=='low'?'selected':'' }}>Thấp</option>
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">— Trạng thái —</option>
                    <option value="todo" {{ request('status')=='todo'?'selected':'' }}>Chưa xong</option>
                    <option value="done" {{ request('status')=='done'?'selected':'' }}>Đã xong</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th class="text-nowrap" style="width:160px;">Ưu tiên</th>
                    <th class="text-nowrap" style="width:160px;">Ngày tạo</th>
                    <th class="text-nowrap" style="width:160px;">Ngày hoàn thành</th>
                    <th class="text-nowrap" style="width:200px">Hành động</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($todos as $todo)
                    <tr class="{{ $todo->is_complete ? 'table-success' : '' }}">
                        <td>
                            <a href="{{ route('todos.show',$todo) }}" class="text-decoration-none">
                                {{ $todo->title }}
                            </a>
                            @if($todo->description)
                                <div class="text-muted small">{{ Str::limit($todo->description, 80) }}</div>
                            @endif
                        </td>
                        <td>
                            @php $map=['high'=>'danger','medium'=>'warning','low'=>'secondary']; @endphp
                            <span class="badge bg-{{ $map[$todo->priority] }}">{{ ucfirst($todo->priority) }}</span>
                        </td>
                        <td>{{ $todo->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($todo->completed_at)
                                {{ $todo->completed_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted d-block text-center">—</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('todos.toggle', $todo) }}" method="post" class="d-inline">
                                @csrf @method('PATCH')
                                @if(!$todo->is_complete)
                                    <button class="btn btn-sm btn-outline-success">Hoàn thành</button>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary">Hủy hoàn thành</button>
                                @endif
                            </form>
                            <a href="{{ route('todos.edit',$todo) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                            <form action="{{ route('todos.destroy',$todo) }}" method="post" class="d-inline"
                                  onsubmit="return confirm('Chắc chắn xóa (đưa vào thùng rác)?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Chưa có công việc.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $todos->links() }}
    </div>
@endsection
