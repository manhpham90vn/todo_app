<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $query = Todo::query()->orderByRaw("FIELD(priority,'high','medium','low')")->orderByDesc('created_at');

        // Lọc theo trạng thái/độ ưu tiên (optional)
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('status')) {
            $request->status === 'done'
                ? $query->where('is_complete', true)
                : $query->where('is_complete', false);
        }

        $todos = $query->paginate(10)->withQueryString();

        return view('todos.index', compact('todos'));
    }

    public function create()
    {
        return view('todos.create');
    }

    public function store(TodoRequest $request)
    {
        Todo::create($request->validated() + ['user_id' => auth()->id()]);

        return redirect()->route('todos.index')->with('success', 'Đã tạo việc cần làm.');
    }

    public function show(Todo $todo)
    {
        return view('todos.show', compact('todo'));
    }

    public function edit(Todo $todo)
    {
        return view('todos.edit', compact('todo'));
    }

    public function update(TodoRequest $request, Todo $todo)
    {
        $todo->update($request->validated());

        return redirect()->route('todos.index')->with('success', 'Đã cập nhật.');
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();

        return redirect()->route('todos.index')->with('success', 'Đã đưa vào thùng rác.');
    }

    public function trash()
    {
        $todos = Todo::onlyTrashed()->orderByDesc('deleted_at')->paginate(10);

        return view('todos.trash', compact('todos'));
    }

    public function restore($id)
    {
        $todo = Todo::onlyTrashed()->findOrFail($id);
        $todo->restore();

        return redirect()->route('todos.trash')->with('success', 'Đã khôi phục.');
    }

    public function forceDelete($id)
    {
        $todo = Todo::onlyTrashed()->findOrFail($id);
        $todo->forceDelete();

        return redirect()->route('todos.trash')->with('success', 'Đã xóa vĩnh viễn.');
    }

    public function toggleComplete(Todo $todo)
    {
        $todo->update(['is_complete' => ! $todo->is_complete]);

        return back()->with('success', 'Đã cập nhật trạng thái.');
    }
}
