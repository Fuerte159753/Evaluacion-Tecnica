<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonController extends Controller
{
    //el controlador de personas no se solicita pero lo veo necesario para sarle mas funcionalidad a la vista web
    public function index()
    {
        try {
            $persons = Person::all();
            return response()->json([
                'success' => true,
                'data' => $persons
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar personas: ' . $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'avatar' => $request->hasFile('avatar') ? 'required|image|max:2048' : 'required|string',
            ]);

            $avatarData = $request->hasFile('avatar') 
                ? base64_encode(file_get_contents($request->file('avatar')->getRealPath()))
                : $this->processAvatar($validated['avatar']);
            
            $person = Person::create([
                'name' => $validated['name'],
                'avatar' => $avatarData,
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $person,
                'message' => 'Persona creada exitosamente'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear persona: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $person = Person::findOrFail($id);
            
            $person->tasks()->detach();
            $person->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Persona eliminada exitosamente'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'La persona no existe'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar persona: ' . $e->getMessage()
            ], 500);
        }
    }
    private function processAvatar($base64)
    {
        try {
            if (strpos($base64, ',') !== false) {
                [$type, $base64] = explode(',', $base64);
            }
            $decoded = base64_decode($base64);
            if (!@imagecreatefromstring($decoded)) {
                throw new \Exception('El avatar no es una imagen válida');
            }
            
            return $base64;
            
        } catch (\Exception $e) {
            throw new \Exception('Error procesando avatar: ' . $e->getMessage());
        }
    }
    public function indexView()
    {
        return view('persons.index');
    }
}