<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ============================================================================
 * AKUN AWAL / TEST SAJA — WAJIB DINONAKTIFKAN/DIHAPUS SEBELUM GO-LIVE.
 * ============================================================================
 * Seeder ini membuat akun default untuk pengembangan & uji coba awal:
 *   - admin   / admin    (Administrator, peran admin)
 *   - proktor / proktor  (Proktor Test, peran proktor)
 *   - siswa NIS 0001 / PIN 1234 (Siswa Test, kelas X TKJ)
 *
 * Password & PIN di bawah ini SEMENTARA dan mudah ditebak. Nonaktifkan
 * (kolom `aktif` = 0) atau hapus ketiga akun ini sebelum aplikasi dibuka
 * online. Jangan biarkan akun test aktif di server yang bisa diakses
 * dari internet.
 *
 * Seeder ini idempotent: dijalankan ulang tidak membuat duplikat.
 */
class PenggunaSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Kelas X TKJ (dibuat dulu bila belum ada — dibutuhkan siswa test).
        $kelas = $this->db->table('kelas')->where('nama', 'X TKJ')->get()->getRowArray();

        if ($kelas === null) {
            $this->db->table('kelas')->insert([
                'nama'       => 'X TKJ',
                'tingkat'    => 'X',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $kelasId = (int) $this->db->insertID();
        } else {
            $kelasId = (int) $kelas['id'];
        }

        // 2. Akun admin & proktor.
        $this->buatPengguna('admin', 'Administrator', 'admin', 'admin', $now);
        $this->buatPengguna('proktor', 'Proktor Test', 'proktor', 'proktor', $now);

        // 3. Siswa test (NIS 0001, PIN 1234, kelas X TKJ).
        $siswa = $this->db->table('siswa')->where('nis', '0001')->get()->getRowArray();

        if ($siswa === null) {
            $this->db->table('siswa')->insert([
                'nis'        => '0001',
                'nama'       => 'Siswa Test',
                'kelas_id'   => $kelasId,
                'pin_hash'   => password_hash('1234', PASSWORD_BCRYPT),
                'aktif'      => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Buat satu akun pengguna bila username belum ada.
     */
    private function buatPengguna(string $username, string $nama, string $password, string $peran, string $now): void
    {
        $ada = $this->db->table('pengguna')->where('username', $username)->get()->getRowArray();

        if ($ada !== null) {
            return;
        }

        $this->db->table('pengguna')->insert([
            'username'      => $username,
            'nama'          => $nama,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'peran'         => $peran,
            'aktif'         => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
    }
}
