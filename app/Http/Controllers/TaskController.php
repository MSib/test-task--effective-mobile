<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index()
    {
        return Task::select(...$this->getVisibleFields())->get();
    }

    public function store(Request $request)
    {
        $validated = $this->validateTask($request);
        $task = Task::create($validated);
        return response()->json(
            $task->only($this->getVisibleFields()),
            201
        );
    }

    public function show(string $id)
    {
        $this->validateID($id);
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'error' => 'Task not found'
            ], 404);
        }
        return response()->json(
            $task->only($this->getVisibleFields())
        );
    }

    public function update(Request $request, string $id)
    {
        $this->validateID($id);
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'error' => 'Task not found'
            ], 404);
        }
        $validated = $this->validateTask($request, true);
        if (isset($validated['status'])) {
            $validated['status'] = $validated['status'] ? 1 : 0;
        }
        $task->fill($validated);
        if (!$task->isDirty()) {
            return response()->json([
                'message' => 'No changes detected'
            ], 422);
        }
        $task->save();
        return response()->json(
            $task->only($this->getVisibleFields())
        );
    }

    public function destroy(string $id)
    {
        $this->validateID($id);
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'error' => 'Task not found'
            ], 404);
        }
        $task->delete();
        return response()->json(
            $task->only($this->getVisibleFields())
        );
    }

    public function validateID(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function validateTask(Request $request, bool $update = false)
    {
        $validated = $request->validate([
            'title' => ($update ? 'sometimes|' : '') . 'required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|boolean',
        ]);
        return $validated;
    }

    public function getVisibleFields()
    {
        return ['id', 'title', 'description', 'status'];
    }
}
