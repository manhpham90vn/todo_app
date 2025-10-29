<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $query = Todo::query()
            ->orderByRaw("CASE WHEN is_complete = 0 THEN 0 ELSE 1 END ASC")
            ->orderBy('created_at', 'desc')
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at');

        // Lọc theo trạng thái/độ ưu tiên (optional)
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('status') && in_array($request->status, ['done', 'todo'], true)) {
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

        if ($todo->is_complete === true) {
            $todo->update(['completed_at' => now()]);
        } else {
            $todo->update(['completed_at' => null]);
        };

        return back()->with('success', 'Đã cập nhật trạng thái.');
    }
}
