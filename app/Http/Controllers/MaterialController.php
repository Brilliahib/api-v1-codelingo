<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // Fetch all materials for a learning path
    public function index($learningPathId)
    {
        $materials = Material::where('learning_path_id', $learningPathId)->get();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Materials retrieved successfully',
            'data' => $materials
        ], 200);
    }

    // Create a new material
    public function store(Request $request)
    {
        $validated = $request->validate([
            'learning_path_id' => 'required|exists:learning_paths,id',
            'title' => 'required|string|max:255',
            'material_image' => 'nullable|string',
            'material_text' => 'required|string',
        ]);

        $material = Material::create($validated);
        return response()->json([
            'statusCode' => 201,
            'message' => 'Material created successfully',
            'data' => $material
        ], 201);
    }

    // Show a single material
    public function show($id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Material not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Material retrieved successfully',
            'data' => $material
        ], 200);
    }

    // Update a material
    public function update(Request $request, $id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Material not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'material_image' => 'nullable|string',
            'material_text' => 'required|string',
        ]);

        $material->update($validated);
        return response()->json([
            'statusCode' => 200,
            'message' => 'Material updated successfully',
            'data' => $material
        ], 200);
    }

    // Delete a material
    public function destroy($id)
    {
        $material = Material::find($id);
        if (!$material) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Material not found',
                'data' => null
            ], 404);
        }

        $material->delete();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Material deleted successfully',
            'data' => null
        ], 200);
    }
}
