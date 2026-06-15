<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->tasks()
            ->with('category')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('tasks.create', compact('categories'));
    }

    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        Task::create($validated);
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを作成しました。');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('category');
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $categories = Category::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'categories'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update($request->validated());
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを更新しました。');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを削除しました。');
    }
}
