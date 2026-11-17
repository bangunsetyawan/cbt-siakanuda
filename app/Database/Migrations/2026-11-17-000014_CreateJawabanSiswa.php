<?php

use CodeIgniter\Database\Migration;

class CreateJawabanSiswa extends Migration
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
            'snapshot_soal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jawaban' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Teks atau JSON; disimpan tiap klik (aman mati lampu)',
            ],
            'benar' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
                'comment'    => 'Diisi otomatis untuk PG/isian; esai menunggu koreksi manual',
            ],
            'skor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'dikoreksi' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'dikoreksi_oleh' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'dikoreksi_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sesi_ujian_id', 'sesi_ujian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('snapshot_soal_id', 'snapshot_soal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dikoreksi_oleh', 'pengguna', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('jawaban_siswa', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('jawaban_siswa', true);
    }
}
