<?php

use CodeIgniter\Database\Migration;

class CreateSoal extends Migration
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
            'bank_soal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['pg', 'esai', 'isian', 'pg_kompleks', 'menjodohkan'],
                'comment'    => 'pg_kompleks & menjodohkan: rilis menyusul (skema sudah siap)',
            ],
            'pertanyaan' => [
                'type'    => 'TEXT',
                'comment' => 'HTML soal, disanitasi saat simpan',
            ],
            'bobot' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 1,
            ],
            'konten_json' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Isi per tipe: pg {opsi,kunci}, esai {rubrik}, isian {kunci}, pg_kompleks {opsi,kunci[]}, menjodohkan {pasangan[]}',
            ],
            'media_gambar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path relatif, contoh: uploads/soal/nama-file.jpg',
            ],
            'media_audio' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path relatif, contoh: uploads/soal/nama-file.mp3',
            ],
            'aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bank_soal_id', 'bank_soal', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('soal', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('soal', true);
    }
}
