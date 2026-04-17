<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Golongan;

class GolonganSeeder extends Seeder
{
    public function run(): void
    {
        $golongan = [
            ['kode' => 'I/a', 'nama' => 'Juru Muda', 'keterangan' => 'Pangkat Juru Muda'],
            ['kode' => 'I/b', 'nama' => 'Juru Muda Tingkat 1', 'keterangan' => 'Pangkat Juru Muda Tingkat 1'],
            ['kode' => 'I/c', 'nama' => 'Juru', 'keterangan' => 'Pangkat Juru'],
            ['kode' => 'I/d', 'nama' => 'Juru Tingkat 1', 'keterangan' => 'Pangkat Juru Tingkat 1'],
            ['kode' => 'II/a', 'nama' => 'Pengatur Muda', 'keterangan' => 'Pangkat Pengatur Muda'],
            ['kode' => 'II/b', 'nama' => 'Pengatur Muda Tingkat 1', 'keterangan' => 'Pangkat Pengatur Muda Tingkat 1'],
            ['kode' => 'II/c', 'nama' => 'Pengatur', 'keterangan' => 'Pangkat Pengatur'],
            ['kode' => 'II/d', 'nama' => 'Pengatur Tingkat 1', 'keterangan' => 'Pangkat Pengatur Tingkat 1'],
            ['kode' => 'III/a', 'nama' => 'Penata Muda', 'keterangan' => 'Pangkat Penata Muda'],
            ['kode' => 'III/b', 'nama' => 'Penata Muda Tingkat 1', 'keterangan' => 'Pangkat Penata Muda Tingkat 1'],
            ['kode' => 'III/c', 'nama' => 'Penata', 'keterangan' => 'Pangkat Penata'],
            ['kode' => 'III/d', 'nama' => 'Penata Tingkat 1', 'keterangan' => 'Pangkat Penata Tingkat 1'],
            ['kode' => 'IV/a', 'nama' => 'Pembina', 'keterangan' => 'Pangkat Pembina'],
            ['kode' => 'IV/b', 'nama' => 'Pembina Tingkat 1', 'keterangan' => 'Pangkat Pembina Tingkat 1'],
            ['kode' => 'IV/c', 'nama' => 'Pembina Utama Muda', 'keterangan' => 'Pangkat Pembina Utama Muda'],
            ['kode' => 'IV/d', 'nama' => 'Pembina Utama Madya', 'keterangan' => 'Pangkat Pembina Utama Madya'],
            ['kode' => 'IV/e', 'nama' => 'Pembina Utama', 'keterangan' => 'Pangkat Pembina Utama'],
        ];

        foreach ($golongan as $g) {
            Golongan::create($g);
        }
    }
}
