<?php

use CodeIgniter\Database\Migration;

class CreateJadwalTesKelas extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        // Tabel pivot: kelas yang ikut satu jadwal tes.
        // Sengaja TANPA created_at/updated_at (sesuai rancangan).
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jadwal_tes_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kelas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['jadwal_tes_id', 'kelas_id']);
        $this->forge->addForeignKey('jadwal_tes_id', 'jadwal_tes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jadwal_tes_kelas', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('jadwal_tes_kelas', true);
    }
}
