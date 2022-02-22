<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'view_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true
            ]
        ]);

        $this->forge->addKey('view_id', true);
        $this->forge->addKey(['order_id', 'user_id'], false, true); //unique key
        $this->forge->createTable('views');
    }

    public function down()
    {
        $this->forge->dropTable('views');
    }
}
