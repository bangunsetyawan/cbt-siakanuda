<?php

use CodeIgniter\Database\Migration;

class CreateSnapshotSoal extends Migration
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
            'sesi_ujian_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'soal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Terkunci ke soal (analisis butir memakai ini, bukan urutan acak)',
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'pertanyaan_snapshot' => [
                'type'    => 'TEXT',
                'comment' => 'Isi pertanyaan saat tes dimulai (edit bank soal sesudahnya tidak mengubahnya)',
            ],
            'konten_snapshot' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'bobot_snapshot' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sesi_ujian_id', 'sesi_ujian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('soal_id', 'soal', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('snapshot_soal', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('snapshot_soal', true);
    }
}
