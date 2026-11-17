<?php

use CodeIgniter\Database\Migration;

class CreateBankSoal extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'periode_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'mapel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'guru_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Guru penyetor soal (boleh kosong)',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('periode_id', 'periode', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('mapel_id', 'mapel', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('bank_soal', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('bank_soal', true);
    }
}
