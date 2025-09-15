<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTables extends Migration
{
    public function up()
    {
        // Table users
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '100'
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '150'
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        // Table voitures
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'marque' => [
                'type' => 'VARCHAR',
                'constraint' => '100'
            ],
            'modele' => [
                'type' => 'VARCHAR',
                'constraint' => '100'
            ],
            'annee' => [
                'type' => 'INT',
                'constraint' => 4
            ],
            'prix' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'
            ],
            'couleur' => [
                'type' => 'VARCHAR',
                'constraint' => '50'
            ],
            'kilometrage' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'carburant' => [
                'type' => 'VARCHAR',
                'constraint' => '50'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('voitures');
    }

    public function down()
    {
        $this->forge->dropTable('voitures');
        $this->forge->dropTable('users');
    }
}   