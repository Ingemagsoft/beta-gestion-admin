//<?php
//
//use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\Schema;
//use Illuminate\Support\Facades\DB;
//
//return new class extends Migration
//{
//    // La migración corre sobre la BD central — donde vive la tabla tenants
//    protected $connection = 'mysql';
//
//    public function up(): void
//        {
//            // Paso 1 — agregar la columna sin unique, nullable temporalmente
//            Schema::connection('mysql')->table('tenants', function (Blueprint $table) {
//                $table->string('codigo', 20)
//                      ->nullable()
//                      ->after('id')
//                      ->comment('Código de acceso de la empresa — ej: PSJ, HLM. Siempre mayúsculas.');
//            });
//
//            // Paso 2 — asignar códigos a los registros existentes
//            DB::connection('mysql')->table('tenants')->where('db_name', 'tenant_demo')->update(['codigo' => 'DEMO']);
//            DB::connection('mysql')->table('tenants')->where('db_name', 'tenant_pruebas')->update(['codigo' => 'PRBS']);
//
//            // Paso 3 — ahora sí agregar la restricción unique y quitar nullable
//            Schema::connection('mysql')->table('tenants', function (Blueprint $table) {
//                $table->string('codigo', 20)->nullable(false)->unique()->change();
//            });
//        }
//
//    public function down(): void
//    {
//        Schema::connection('mysql')->table('tenants', function (Blueprint $table) {
//            $table->dropColumn('codigo');
//        });
//    }
//};