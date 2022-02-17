<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrders extends Migration
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
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['od', 'og', 'or']
            ],
            'number' => [
                'type' => 'INT',
                'constraint' => '11'
            ],
            'year' => [
                'type' => 'INT',
                'constraint' => '11'
            ],
            'date' => [
                'type' => 'DATE'
            ],
            'about' => [
                'type' => 'TEXT'
            ],
            'file_url' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['number', 'year'], false, true); //unique key
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
