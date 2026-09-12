<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 3)->unique();
            $table->string('cabecera')->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->timestamps();
        });

        Schema::create('modelos_panel', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->decimal('potencia_kw', 8, 3);
            $table->decimal('eficiencia', 5, 2)->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->unique(['marca', 'modelo']);
        });

        Schema::create('granjas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->string('municipio')->nullable();
            $table->string('direccion')->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->unsignedInteger('familias_beneficiadas')->default(0);
            $table->decimal('generacion_esperada_mensual_kwh', 14, 2)->default(0);
            $table->date('fecha_instalacion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('granja_panel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('granja_id')->constrained('granjas')->cascadeOnDelete();
            $table->foreignId('modelo_panel_id')->constrained('modelos_panel');
            $table->unsignedInteger('cantidad');
            $table->date('fecha_instalacion')->nullable();
            $table->timestamps();
            $table->unique(['granja_id', 'modelo_panel_id']);
        });

        Schema::create('generaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('granja_id')->constrained('granjas')->cascadeOnDelete();
            $table->date('periodo'); // siempre día 1 del mes
            $table->decimal('generacion_real_kwh', 14, 2);
            $table->decimal('generacion_esperada_kwh', 14, 2);
            $table->timestamps();
            $table->unique(['granja_id', 'periodo']);
            $table->index('periodo');
        });

        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('granja_id')->constrained('granjas')->cascadeOnDelete();
            $table->foreignId('generacion_id')->constrained('generaciones')->cascadeOnDelete();
            $table->date('periodo');
            $table->decimal('generacion_esperada_kwh', 14, 2);
            $table->decimal('generacion_real_kwh', 14, 2);
            $table->decimal('porcentaje_desviacion', 6, 2); // negativo = por debajo
            $table->string('estado')->default('activa');
            $table->timestamps();
            $table->unique('generacion_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
        Schema::dropIfExists('generaciones');
        Schema::dropIfExists('granja_panel');
        Schema::dropIfExists('granjas');
        Schema::dropIfExists('modelos_panel');
        Schema::dropIfExists('departamentos');
    }
};
