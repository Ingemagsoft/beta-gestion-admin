<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;

class ApiCiudadController extends Controller
{
    public function index()
    {
        $ciudades = Ciudad::where('activo', true)
                          ->orderBy('nombre')
                          ->get(['id', 'nombre', 'departamento']);

        return response()->json([
            'success' => true,
            'data'    => $ciudades,
            'total'   => $ciudades->count(),
        ], 200);
    }
}