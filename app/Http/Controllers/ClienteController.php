<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Ciudad;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // ─── Listado con búsqueda ────────────────────────────────────
    public function index(Request $request)
    {
        $busqueda = trim($request->input('busqueda', ''));
        $clientes = collect();

        if ($busqueda !== '') {
            // Detectar si es código exacto → redirigir a edición
            $exacto = Cliente::where('codigo', $busqueda)
                             ->where('activo', true)
                             ->first();

            if ($exacto) {
                return redirect()->route('clientes.edit', $exacto->id);
            }

            // Búsqueda progresiva por nombre, razón social o documento
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
                ->paginate(20)
                ->withQueryString();
        }

        return view('clientes.index', compact('clientes', 'busqueda'));
    }

    // ─── Formulario nuevo cliente ────────────────────────────────
    public function create()
    {
        $ciudades = Ciudad::where('activo', true)
                          ->orderBy('nombre')
                          ->get();

        $ciudadDefault = Ciudad::where('nombre', 'Pereira')->first();

        return view('clientes.create', compact('ciudades', 'ciudadDefault'));
    }

    // ─── Guardar nuevo cliente ───────────────────────────────────
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

        // Validaciones según tipo de persona
        if ($esNit) {
            $rules['digito_verificacion'] = 'required|numeric|max:9';
        } else {
            $rules['primer_apellido'] = 'required';
            $rules['primer_nombre']   = 'required';
        }

        $messages = [
            'codigo.required'           => 'El código es obligatorio.',
            'codigo.alpha_num'          => 'El código no puede tener espacios ni caracteres especiales.',
            'codigo.unique'             => 'Este código ya existe. Si desea editar ese cliente búsquelo por su código.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.numeric'  => 'El número de documento solo puede contener números.',
            'email.required'            => 'El correo electrónico es obligatorio.',
            'email.email'               => 'El formato del correo no es válido.',
            'direccion.required'        => 'La dirección es obligatoria.',
            'primer_apellido.required'  => 'El primer apellido es obligatorio para persona natural.',
            'primer_nombre.required'    => 'El primer nombre es obligatorio para persona natural.',
        ];

        $validated = $request->validate($rules, $messages);

        // Tipo de documento automático según dígito de verificación
        $tipoDoc = $esNit ? 'NIT' : ($request->tipo_documento ?? 'CC');

        Cliente::create([
            'codigo'              => strtoupper($request->codigo),
            'tipo_cliente'        => $request->tipo_cliente ?? 'Cliente',
            'tipo_documento'      => $tipoDoc,
            'numero_documento'    => $request->numero_documento,
            'digito_verificacion' => $request->digito_verificacion,
            'razon_social'        => $esNit ? $request->razon_social : null,
            'rep_legal_nombre'    => $esNit ? $request->rep_legal_nombre : null,
            'rep_legal_documento' => $esNit ? $request->rep_legal_documento : null,
            'primer_apellido'     => !$esNit ? $request->primer_apellido : null,
            'segundo_apellido'    => !$esNit ? $request->segundo_apellido : null,
            'primer_nombre'       => !$esNit ? $request->primer_nombre : null,
            'segundo_nombre'      => !$esNit ? $request->segundo_nombre : null,
            'email'               => $request->email,
            'telefono'            => $request->telefono,
            'celular'             => $request->celular,
            'direccion'           => $request->direccion,
            'ciudad_id'           => $request->ciudad_id ?: null,
            'activo'              => true,
        ]);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente creado exitosamente.');
    }

    // ─── Formulario editar cliente ───────────────────────────────
    public function edit(Cliente $cliente)
    {
        $ciudades     = Ciudad::where('activo', true)->orderBy('nombre')->get();
        $ciudadDefault = Ciudad::where('nombre', 'Pereira')->first();

        return view('clientes.edit', compact('cliente', 'ciudades', 'ciudadDefault'));
    }

    // ─── Actualizar cliente ──────────────────────────────────────
    public function update(Request $request, Cliente $cliente)
    {
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
            $rules['digito_verificacion'] = 'required|numeric|max:9';
        } else {
            $rules['primer_apellido'] = 'required';
            $rules['primer_nombre']   = 'required';
        }

        $messages = [
            'codigo.required'           => 'El código es obligatorio.',
            'codigo.alpha_num'          => 'El código no puede tener espacios ni caracteres especiales.',
            'codigo.unique'             => 'Este código ya existe en otro cliente.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.numeric'  => 'El número de documento solo puede contener números.',
            'email.required'            => 'El correo electrónico es obligatorio.',
            'email.email'               => 'El formato del correo no es válido.',
            'direccion.required'        => 'La dirección es obligatoria.',
            'primer_apellido.required'  => 'El primer apellido es obligatorio para persona natural.',
            'primer_nombre.required'    => 'El primer nombre es obligatorio para persona natural.',
        ];

        $request->validate($rules, $messages);

        $tipoDoc = $esNit ? 'NIT' : ($request->tipo_documento ?? 'CC');

        $cliente->update([
            'codigo'              => strtoupper($request->codigo),
            'tipo_cliente'        => $request->tipo_cliente ?? 'Cliente',
            'tipo_documento'      => $tipoDoc,
            'numero_documento'    => $request->numero_documento,
            'digito_verificacion' => $request->digito_verificacion,
            'razon_social'        => $esNit ? $request->razon_social : null,
            'rep_legal_nombre'    => $esNit ? $request->rep_legal_nombre : null,
            'rep_legal_documento' => $esNit ? $request->rep_legal_documento : null,
            'primer_apellido'     => !$esNit ? $request->primer_apellido : null,
            'segundo_apellido'    => !$esNit ? $request->segundo_apellido : null,
            'primer_nombre'       => !$esNit ? $request->primer_nombre : null,
            'segundo_nombre'      => !$esNit ? $request->segundo_nombre : null,
            'email'               => $request->email,
            'telefono'            => $request->telefono,
            'celular'             => $request->celular,
            'direccion'           => $request->direccion,
            'ciudad_id'           => $request->ciudad_id ?: null,
        ]);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente actualizado exitosamente.');
    }

    // ─── Eliminación lógica ──────────────────────────────────────
    public function destroy(Cliente $cliente)
    {
        $cliente->update(['activo' => false]);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente desactivado correctamente.');
    }
}