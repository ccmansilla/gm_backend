<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'jefatura', 'dependencia']
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '60'
            ],
            'nick' => [
                'type' => 'VARCHAR',
                'constraint' => '60',
                'unique' => true
            ],
            'pass' => [
                'type' => 'VARCHAR',
                'constraint' => '60'
            ],
            'created_at timestamp default current_timestamp',
            'updated_at timestamp default current_timestamp on update current_timestamp',
            'deleted' => [
                'type' => 'tinyint',
                'constraint' => '1',
                'default' => '0'
            ]

        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
