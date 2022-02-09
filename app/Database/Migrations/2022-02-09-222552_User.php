<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role'       => [
                'type'      => 'ENUM',
                'constraint' => ['admin', 'jefatura', 'dependencia']
            ]
            ,
            'name'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nick'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'pass'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nick');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
