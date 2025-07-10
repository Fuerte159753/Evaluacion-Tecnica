<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::with('persons')->get();
            
            return response()->json([
                'success' => true,
                'data' => $tasks
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tareas: ' . $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                //Estados en los cuales puede estar una tarea
                // 0: No asignado(no se ah asignado a una persona oh en su defecto se acaba de crear)
                // 1: Pendiente(ya ah sido asignado a una persana)
                // 2: Completado (se ah completado la tarea)
                'status' => 'required|integer|in:0,1',
            ]);
            $validated['status'] = $validated['status'] ?? 0; 

            $task = Task::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'Tarea creada exitosamente'
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, Task $task)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|integer|in:0,1,2',
            ]);

            $task->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $task->fresh('persons'),
                'message' => 'Tarea actualizada exitosamente'
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Task $task)
    {
        DB::beginTransaction();
        
        try {
            $task->persons()->detach();
            $task->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tarea eliminada exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar tarea: ' . $e->getMessage()
            ], 500);
        }
    }
    public function assign_task(Request $request, Task $task)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'person_id' => 'required|exists:persons,id',
            ]);
            if ($task->persons()->where('person_task.persons_id', $validated['person_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La persona ya está asignada a esta tarea'
                ], 409);
            }
            $task->persons()->attach($validated['person_id']);
            $task->refresh();

            if ($task->status == 0) { 
                $task->update(['status' => 1]);
                $task->refresh();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $task->fresh('persons'),
                'message' => 'Persona asignada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar persona: ' . $e->getMessage()
            ], 500);
        }
    }
    public function unassign_task(Request $request, Task $task)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'person_id' => 'required|exists:persons,id',
            ]);

            if (!$task->persons()->where('person_task.persons_id', $validated['person_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La persona no está asignada a esta tarea'
                ], 404);
            }
            $task->persons()->detach($validated['person_id']);
            $task->refresh();
            if ($task->persons()->count() == 0 && $task->status != 0) {
                $task->update(['status' => 0]);
                $task->refresh();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $task->fresh('persons'),
                'message' => 'Persona desasignada exitosamente'
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al desasignar persona: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show(Task $task)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $task->load('persons') // Carga las relaciones si las necesitas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tarea: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexView()
    {
        return view('tasks.index');
    }
}