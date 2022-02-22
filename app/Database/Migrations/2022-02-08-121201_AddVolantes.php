<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVolantes extends Migration
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
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['emitido', 'recibido']
            ],
            'number' => [
                'type' => 'INT',
                'constraint' => '11'
            ],
            'year' => [
                'type' => 'INT',
                'constraint' => '11'
            ],
            'origen' => [
                'type' => 'INT',
                'constraint' => '11'
            ],
            'destino' => [
                'type' => 'INT',
                'constraint' => '11'
            ],
            'fecha' => [
                'type' => 'DATE'
            ],
            'asunto' => [
                'type' => 'TEXT'
            ],
            'enlace_archivo' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'enlace_adjunto' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['number', 'year'], false, true); //unique key
        $this->forge->createTable('volantes');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
