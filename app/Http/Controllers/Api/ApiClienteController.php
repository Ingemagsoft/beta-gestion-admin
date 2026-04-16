<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Ciudad; 
use Illuminate\Http\Request;

class ApiClienteController extends Controller
{
    // ─── Listado con búsqueda ────────────────────────────────────
    public function index(Request $request)
    {
        $busqueda = trim($request->input('busqueda', ''));

        if ($busqueda === '') {
            return response()->json([
                'success' => true,
                'message' => 'Ingresa un término de búsqueda.',
                'data'    => [],
                'total'   => 0,
            ], 200);
        }

        // Detectar código exacto
        $exacto = Cliente::where('codigo', $busqueda)
                         ->where('activo', true)
                         ->first();

        if ($exacto) {
            return response()->json([
                'success'      => true,
                'codigo_exacto'=> true,
                'data'         => $this->formatearCliente($exacto),
            ], 200);
        }

        // Búsqueda progresiva
        $clientes = Cliente::where('activo', true)
            ->where(function ($q) use ($busqueda) {
                $q->where('primer_nombre',    'LIKE', "%{$busqueda}%")
                  ->orWhere('primer_apellido', 'LIKE', "%{$busqueda}%")
                  ->orWhere('razon_social',    'LIKE', "%{$busqueda}%")
                  ->orWhere('numero_documento','LIKE', "%{$busqueda}%")
                  ->orWhere('codigo',          'LIKE', "%{$busqueda}%");
            })
            ->orderBy('primer_apellido')
            ->orderBy('razon_social')
            ->paginate(20);

        return response()->json([
            'success'      => true,
            'codigo_exacto'=> false,
            'data'         => $clientes->map(fn($c) => $this->formatearCliente($c)),
            'total'        => $clientes->total(),
            'pagina_actual'=> $clientes->currentPage(),
            'total_paginas'=> $clientes->lastPage(),
        ], 200);
    }

    // ─── Detalle de un cliente ────────────────────────────────────
    public function show($id)
    {
        $cliente = Cliente::where('id', $id)
                          ->where('activo', true)
                          ->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatearCliente($cliente),
        ], 200);
    }

    // ─── Crear cliente ────────────────────────────────────────────
    public function store(Request $request)
    {
        $esNit = $request->filled('digito_verificacion');

        $rules = [
            'codigo'           => 'required|alpha_num|unique:tenant.clientes,codigo',
            'tipo_documento'   => 'required',
            'numero_documento' => 'required|numeric',
            'email'            => 'required|email',
            'direccion'        => 'required',
            'ciudad_id'        => 'nullable|exists:tenant.ciudades,id',
        ];

        if ($esNit) {
            $rules['digito_verificacion'] = 'required|numeric';
        } else {
            $rules['primer_apellido'] = 'required';
            $rules['primer_nombre']   = 'required';
        }

        $validator = validator($request->all(), $rules, [
            'codigo.required'           => 'El código es obligatorio.',
            'codigo.alpha_num'          => 'El código no puede tener espacios ni caracteres especiales.',
            'codigo.unique'             => 'Este código ya existe.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.numeric'  => 'El número de documento solo puede contener números.',
            'email.required'            => 'El correo electrónico es obligatorio.',
            'email.email'               => 'El formato del correo no es válido.',
            'direccion.required'        => 'La dirección es obligatoria.',
            'primer_apellido.required'  => 'El primer apellido es obligatorio para persona natural.',
            'primer_nombre.required'    => 'El primer nombre es obligatorio para persona natural.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $tipoDoc = $esNit ? 'NIT' : ($request->tipo_documento ?? 'CC');

        $cliente = Cliente::create([
            'codigo'                    => strtoupper($request->codigo),
            'tipo_cliente'              => $request->tipo_cliente ?? 'Cliente',
            'tipo_documento'            => $tipoDoc,
            'numero_documento'          => $request->numero_documento,
            'digito_verificacion'       => $request->digito_verificacion ?: null,
            'razon_social'              => $esNit ? $request->razon_social               : null,
            'rep_legal_primer_nombre'   => $esNit ? $request->rep_legal_primer_nombre    : null,
            'rep_legal_segundo_nombre'  => $esNit ? $request->rep_legal_segundo_nombre   : null,
            'rep_legal_primer_apellido' => $esNit ? $request->rep_legal_primer_apellido  : null,
            'rep_legal_segundo_apellido'=> $esNit ? $request->rep_legal_segundo_apellido : null,
            'rep_legal_documento'       => $esNit ? $request->rep_legal_documento        : null,
            'primer_apellido'           => !$esNit ? $request->primer_apellido  : null,
            'segundo_apellido'          => !$esNit ? $request->segundo_apellido : null,
            'primer_nombre'             => !$esNit ? $request->primer_nombre    : null,
            'segundo_nombre'            => !$esNit ? $request->segundo_nombre   : null,
            'email'                     => $request->email,
            'telefono'                  => $request->telefono,
            'celular'                   => $request->celular,
            'direccion'                 => $request->direccion,
            'ciudad_id'                 => $request->ciudad_id ?: null,
            'activo'                    => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado exitosamente.',
            'data'    => $this->formatearCliente($cliente),
        ], 201);
    }

    // ─── Actualizar cliente ───────────────────────────────────────
    public function update(Request $request, $id)
    {
        $cliente = Cliente::where('id', $id)
                          ->where('activo', true)
                          ->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado.',
            ], 404);
        }

        $esNit = $request->filled('digito_verificacion');

        $rules = [
            'codigo'           => "required|alpha_num|unique:tenant.clientes,codigo,{$cliente->id}",
            'tipo_documento'   => 'required',
            'numero_documento' => 'required|numeric',
            'email'            => 'required|email',
            'direccion'        => 'required',
            'ciudad_id'        => 'nullable|exists:tenant.ciudades,id',
        ];

        if ($esNit) {
            $rules['digito_verificacion'] = 'required|numeric';
        } else {
            $rules['primer_apellido'] = 'required';
            $rules['primer_nombre']   = 'required';
        }

        $validator = validator($request->all(), $rules, [
            'codigo.required'           => 'El código es obligatorio.',
            'codigo.unique'             => 'Este código ya existe en otro cliente.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.email'               => 'Formato de correo inválido.',
            'direccion.required'        => 'La dirección es obligatoria.',
            'primer_apellido.required'  => 'Primer apellido obligatorio para persona natural.',
            'primer_nombre.required'    => 'Primer nombre obligatorio para persona natural.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $tipoDoc = $esNit ? 'NIT' : ($request->tipo_documento ?? 'CC');

        $cliente->update([
            'codigo'                    => strtoupper($request->codigo),
            'tipo_cliente'              => $request->tipo_cliente ?? 'Cliente',
            'tipo_documento'            => $tipoDoc,
            'numero_documento'          => $request->numero_documento,
            'digito_verificacion'       => $request->digito_verificacion ?: null,
            'razon_social'              => $esNit ? $request->razon_social               : null,
            'rep_legal_primer_nombre'   => $esNit ? $request->rep_legal_primer_nombre    : null,
            'rep_legal_segundo_nombre'  => $esNit ? $request->rep_legal_segundo_nombre   : null,
            'rep_legal_primer_apellido' => $esNit ? $request->rep_legal_primer_apellido  : null,
            'rep_legal_segundo_apellido'=> $esNit ? $request->rep_legal_segundo_apellido : null,
            'rep_legal_documento'       => $esNit ? $request->rep_legal_documento        : null,
            'primer_apellido'           => !$esNit ? $request->primer_apellido  : null,
            'segundo_apellido'          => !$esNit ? $request->segundo_apellido : null,
            'primer_nombre'             => !$esNit ? $request->primer_nombre    : null,
            'segundo_nombre'            => !$esNit ? $request->segundo_nombre   : null,
            'email'                     => $request->email,
            'telefono'                  => $request->telefono,
            'celular'                   => $request->celular,
            'direccion'                 => $request->direccion,
            'ciudad_id'                 => $request->ciudad_id ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado exitosamente.',
            'data'    => $this->formatearCliente($cliente->fresh()),
        ], 200);
    }

    // ─── Desactivar cliente ───────────────────────────────────────
    public function destroy($id)
    {
        $cliente = Cliente::where('id', $id)
                          ->where('activo', true)
                          ->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado o ya está inactivo.',
            ], 404);
        }

        $cliente->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente desactivado correctamente.',
        ], 200);
    }

    // ─── Helper — formatear cliente para JSON ─────────────────────
    private function formatearCliente(Cliente $cliente): array
    {
        return [
            'id'                        => $cliente->id,
            'codigo'                    => $cliente->codigo,
            'tipo_cliente'              => $cliente->tipo_cliente,
            'tipo_documento'            => $cliente->tipo_documento,
            'numero_documento'          => $cliente->numero_documento,
            'digito_verificacion'       => $cliente->digito_verificacion,
            'documento_formateado'      => $cliente->documento_formateado,
            'nombre_completo'           => $cliente->nombre_completo,
            'razon_social'              => $cliente->razon_social,
            'rep_legal_primer_nombre'   => $cliente->rep_legal_primer_nombre,
            'rep_legal_segundo_nombre'  => $cliente->rep_legal_segundo_nombre,
            'rep_legal_primer_apellido' => $cliente->rep_legal_primer_apellido,
            'rep_legal_segundo_apellido'=> $cliente->rep_legal_segundo_apellido,
            'rep_legal_documento'       => $cliente->rep_legal_documento,
            'primer_apellido'           => $cliente->primer_apellido,
            'segundo_apellido'          => $cliente->segundo_apellido,
            'primer_nombre'             => $cliente->primer_nombre,
            'segundo_nombre'            => $cliente->segundo_nombre,
            'email'                     => $cliente->email,
            'telefono'                  => $cliente->telefono,
            'celular'                   => $cliente->celular,
            'direccion'                 => $cliente->direccion,
            'ciudad'                    => $cliente->ciudad ? [
                'id'            => $cliente->ciudad->id,
                'nombre'        => $cliente->ciudad->nombre,
                'departamento'  => $cliente->ciudad->departamento,
            ] : null,
            'activo'                    => $cliente->activo,
            'created_at'                => $cliente->created_at?->toDateTimeString(),
            'updated_at'                => $cliente->updated_at?->toDateTimeString(),
        ];
    }
}