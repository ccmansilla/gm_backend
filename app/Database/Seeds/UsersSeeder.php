<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Using Query Builder
        $this->db->table('users')->truncate();

        $users = [
            [
                'id' => '1',
                'role' => 'admin',
                'name' => 'Administrador',
                'nick' => 'admin',
                'pass' => '1234'
            ],

            [
                'id' => '2',
                'role' => 'jefatura',
                'name' => 'Jefatura Grupo Mantenimiento',
                'nick' => 'jefatura',
                'pass' => '1234'
            ],

            [
                'id' => '3',
                'role' => 'dependencia',
                'name' => 'Departamento Ingenieria',
                'nick' => 'deping',
                'pass' => '1234'
            ]
        ];

        $this->db->table('users')->insertBatch($users);
        
    }
}
