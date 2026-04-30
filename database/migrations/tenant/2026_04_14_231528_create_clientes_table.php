<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            // ─── Identificación ────────────────────────────────────
            $table->string('codigo', 50)->unique();
            $table->string('tipo_cliente', 50)->default('Cliente');
            $table->string('tipo_documento', 50)->default('NIT');
            $table->string('numero_documento', 50);
            $table->string('digito_verificacion', 2)->nullable();

            // ─── Datos persona jurídica ────────────────────────────
            $table->string('razon_social', 200)->nullable();
            $table->string('rep_legal_primer_nombre', 100)->nullable();
            $table->string('rep_legal_segundo_nombre', 100)->nullable();
            $table->string('rep_legal_primer_apellido', 100)->nullable();
            $table->string('rep_legal_segundo_apellido', 100)->nullable();
            $table->string('rep_legal_documento', 50)->nullable();

            // ─── Datos persona natural ─────────────────────────────
            $table->string('primer_apellido', 100)->nullable();
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('primer_nombre', 100)->nullable();
            $table->string('segundo_nombre', 100)->nullable();

            // ─── Contacto ──────────────────────────────────────────
            $table->string('email', 150);
            $table->string('telefono', 30)->nullable();
            $table->string('celular', 30)->nullable();

            // ─── Dirección ─────────────────────────────────────────
            $table->string('direccion', 250);
            $table->foreignId('ciudad_id')
                  ->nullable()  // Permitir clientes sin ciudad asignada
                  ->constrained('ciudades')
                  ->nullOnDelete(); // Si la ciudad se borra, dejar el campo en null

            // ─── Control ───────────────────────────────────────────
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};