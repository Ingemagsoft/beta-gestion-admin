<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function clientes(Request $request)
    {
        $busqueda = trim($request->input('busqueda', ''));

        // Si hay búsqueda aplica el filtro — si no trae todos
        $query = Cliente::where('activo', true)
                        ->with('ciudad')
                        ->orderBy('primer_apellido')
                        ->orderBy('razon_social');

        if ($busqueda !== '') {
            $query->where(function ($q) use ($busqueda) {
                $q->where('primer_nombre',    'LIKE', "%{$busqueda}%")
                  ->orWhere('primer_apellido', 'LIKE', "%{$busqueda}%")
                  ->orWhere('razon_social',    'LIKE', "%{$busqueda}%")
                  ->orWhere('numero_documento','LIKE', "%{$busqueda}%")
                  ->orWhere('codigo',          'LIKE', "%{$busqueda}%");
            });
        }

        $clientes       = $query->get();
        $totalClientes  = $clientes->count();
        $fechaGeneracion = now()->format('d/m/Y H:i');
        $empresa        = session('tenant_nombre');

        $pdf = Pdf::loadView('pdf.clientes', compact(
            'clientes',
            'totalClientes',
            'fechaGeneracion',
            'empresa',
            'busqueda'
        ))
        ->setPaper('letter', 'landscape')
        ->setOption('defaultFont', 'sans-serif')
        ->setOption('isRemoteEnabled', true);

        $nombreArchivo = 'clientes_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($nombreArchivo);
    }
}