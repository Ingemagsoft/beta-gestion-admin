<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminEmpresaController extends Controller
{
    // ─── Listar todas las empresas ───────────────────────────────
    public function index()
    {
        $empresas = Tenant::listarTodas();
        return view('admin.empresas.index', compact('empresas'));
    }

    // ─── Formulario nueva empresa ────────────────────────────────
    public function create()
    {
        return view('admin.empresas.create');
    }

    // ─── Guardar nueva empresa ───────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'codigo'  => 'required|string|max:20|unique:tenants,codigo',
            'nombre'  => 'required|string|max:255',
            'nit'     => 'required|string|max:20|unique:tenants,nit',
            'db_name' => 'required|string|max:100|unique:tenants,db_name',
        ], [
            'codigo.required'  => 'El código de empresa es obligatorio.',
            'codigo.unique'    => 'Este código ya está registrado.',
            'nombre.required'  => 'La razón social es obligatoria.',
            'nit.required'     => 'El NIT es obligatorio.',
            'nit.unique'       => 'Este NIT ya está registrado.',
            'db_name.required' => 'El nombre de la base de datos es obligatorio.',
            'db_name.unique'   => 'Este nombre de BD ya está en uso.',
        ]);
    
        try {
            // Paso 1 — Registrar en tenants (protegido con transacción)
            /** @var Tenant|null $tenant */
            $tenant = null;
            DB::transaction(function () use ($request, &$tenant) {
                $tenant = Tenant::registrarNueva(
                    $request->only('codigo', 'nombre', 'nit', 'db_name')
                );
            });
    
            // Paso 2 — Crear BD y migrar (fuera de transacción — DDL no es transaccional)
            Tenant::crearBaseDeDatos($tenant);
    
        } catch (\Exception $e) {
            // Si algo falló después del INSERT, eliminar el registro huérfano
            if (isset($tenant)) {
                $tenant->delete();
            }
    
            return redirect()->route('admin.empresas.create')
                             ->withErrors(['error' => 'Error al crear la empresa: ' . $e->getMessage()])
                             ->withInput();
        }
    
        return redirect()->route('admin.empresas.index')
                         ->with('success', 'Empresa registrada, BD creada y migraciones ejecutadas correctamente.');
    }

    // ─── Formulario editar empresa ───────────────────────────────
    public function edit($id)
    {
        $empresa = Tenant::buscarPorId($id);
        return view('admin.empresas.edit', compact('empresa'));
    }

    // ─── Guardar cambios de empresa ──────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nit'    => 'required|string|max:20|unique:tenants,nit,' . $id,
        ], [
            'nombre.required' => 'La razón social es obligatoria.',
            'nit.required'    => 'El NIT es obligatorio.',
            'nit.unique'      => 'Este NIT ya está registrado en otra empresa.',
        ]);

        Tenant::actualizarDatos($id, $request->only('nombre', 'nit'));

        return redirect()->route('admin.empresas.index')
                         ->with('success', 'Empresa actualizada correctamente.');
    }

    // ─── Activar o desactivar empresa ───────────────────────────
    public function toggle($id)
    {
        Tenant::toggleActivo($id);

        return redirect()->route('admin.empresas.index')
                         ->with('success', 'Estado de la empresa actualizado.');
    }
}