<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /** Los 22 departamentos de Guatemala (código ISO 3166-2:GT) con cabecera y centroide aproximado. */
    public const DEPARTAMENTOS = [
        ['AV', 'Alta Verapaz',   'Cobán',                 15.6000, -90.3000],
        ['BV', 'Baja Verapaz',   'Salamá',                15.1000, -90.3500],
        ['CM', 'Chimaltenango',  'Chimaltenango',         14.6600, -90.8200],
        ['CQ', 'Chiquimula',     'Chiquimula',            14.7500, -89.5000],
        ['PR', 'El Progreso',    'Guastatoya',            14.9000, -90.0500],
        ['ES', 'Escuintla',      'Escuintla',             14.2000, -90.9000],
        ['GU', 'Guatemala',      'Ciudad de Guatemala',   14.6200, -90.5300],
        ['HU', 'Huehuetenango',  'Huehuetenango',         15.6000, -91.5500],
        ['IZ', 'Izabal',         'Puerto Barrios',        15.5000, -88.9000],
        ['JA', 'Jalapa',         'Jalapa',                14.6300, -89.9900],
        ['JU', 'Jutiapa',        'Jutiapa',               14.2500, -89.8500],
        ['PE', 'Petén',          'Flores',                16.9000, -90.0000],
        ['QZ', 'Quetzaltenango', 'Quetzaltenango',        14.8000, -91.5500],
        ['QC', 'Quiché',         'Santa Cruz del Quiché', 15.3500, -91.0000],
        ['RE', 'Retalhuleu',     'Retalhuleu',            14.4500, -91.7500],
        ['SA', 'Sacatepéquez',   'Antigua Guatemala',     14.5500, -90.7200],
        ['SM', 'San Marcos',     'San Marcos',            15.0000, -91.9500],
        ['SR', 'Santa Rosa',     'Cuilapa',               14.2000, -90.3500],
        ['SO', 'Sololá',         'Sololá',                14.7200, -91.2000],
        ['SU', 'Suchitepéquez',  'Mazatenango',           14.4000, -91.4000],
        ['TO', 'Totonicapán',    'Totonicapán',           14.9500, -91.3500],
        ['ZA', 'Zacapa',         'Zacapa',                15.0000, -89.4500],
    ];

    public function run(): void
    {
        foreach (self::DEPARTAMENTOS as [$codigo, $nombre, $cabecera, $lat, $lon]) {
            Departamento::updateOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre, 'cabecera' => $cabecera, 'latitud' => $lat, 'longitud' => $lon],
            );
        }
    }
}
