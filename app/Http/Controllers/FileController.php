<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // Lista todos os arquivos salvos na pasta public/uploads
    public function index()
    {
        $files = Storage::disk('public')->files('uploads');
        return array_map(function($file) {
            return [
                'name' => basename($file),
                'url' => asset('storage/' . $file),
                'path' => $file
            ];
        }, $files);
    }

    // Realiza o upload de novos arquivos
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // Limite de 5MB
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            return response()->json(['message' => 'Upload realizado com sucesso', 'path' => $path], 201);
        }

        return response()->json(['message' => 'Erro ao subir arquivo'], 400);
    }

    // Remove um arquivo permanentemente
    public function destroy(Request $request)
    {
        if (Storage::disk('public')->exists($request->path)) {
            Storage::disk('public')->delete($request->path);
            return response()->json(['message' => 'Arquivo removido']);
        }
        return response()->json(['message' => 'Arquivo não encontrado'], 404);
    }
}