<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('departamento', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Ciudades principales de Colombia
        DB::table('ciudades')->insert([
            // Eje Cafetero
            ['nombre' => 'Pereira',           'departamento' => 'Risaralda',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Manizales',         'departamento' => 'Caldas',             'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Armenia',           'departamento' => 'Quindío',            'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Dosquebradas',      'departamento' => 'Risaralda',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Santa Rosa de Cabal','departamento' => 'Risaralda',         'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            // Principales
            ['nombre' => 'Bogotá',            'departamento' => 'Cundinamarca',       'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Medellín',          'departamento' => 'Antioquia',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cali',              'departamento' => 'Valle del Cauca',    'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Barranquilla',      'departamento' => 'Atlántico',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cartagena',         'departamento' => 'Bolívar',            'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cúcuta',            'departamento' => 'Norte de Santander', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Bucaramanga',       'departamento' => 'Santander',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ibagué',            'departamento' => 'Tolima',             'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Villavicencio',     'departamento' => 'Meta',               'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pasto',             'departamento' => 'Nariño',             'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Neiva',             'departamento' => 'Huila',              'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Montería',          'departamento' => 'Córdoba',            'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Sincelejo',         'departamento' => 'Sucre',              'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Popayán',           'departamento' => 'Cauca',              'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Valledupar',        'departamento' => 'Cesar',              'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Santa Marta',       'departamento' => 'Magdalena',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Palmira',           'departamento' => 'Valle del Cauca',    'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Bello',             'departamento' => 'Antioquia',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Buenaventura',      'departamento' => 'Valle del Cauca',    'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Floridablanca',     'departamento' => 'Santander',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Soledad',           'departamento' => 'Atlántico',          'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Soacha',            'departamento' => 'Cundinamarca',       'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Otra ciudad',       'departamento' => null,                 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};