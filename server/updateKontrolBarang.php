<?php
function updateKontrolBarang($conn, $data)
{
    // Memulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // Mengambil data lama untuk perbandingan dan tracking perubahan
        $old_data_query = "SELECT kb.*, i.jumlah as jumlah_inventaris,
                          i.kode_inventaris, pb.nama_barang,
                          kr.jumlah as jumlah_rusak,
                          kh.jumlah as jumlah_hilang,
                          pb2.jumlah as jumlah_pindah,
                          i.satuan
                          FROM kontrol_barang kb
                          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
                          JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
                          LEFT JOIN kerusakan_barang kr ON kb.id_kontrol_barang = kr.id_kontrol_barang
                          LEFT JOIN kehilangan_barang kh ON kb.id_kontrol_barang = kh.id_kontrol_barang
                          LEFT JOIN perpindahan_barang pb2 ON kb.id_kontrol_barang = pb2.id_kontrol_barang
                          WHERE kb.id_kontrol_barang = '{$data['id_kontrol']}'";
        $old_result = mysqli_query($conn, $old_data_query);
        $old_data = mysqli_fetch_assoc($old_result);

        // === PENGELOLAAN PERUBAHAN STATUS BARANG ===
        if ($old_data['status'] != $data['status']) {

            // 1. BARANG BAIK MENJADI RUSAK/PINDAH/HILANG
            if ($old_data['status'] == 1 && $data['status'] > 1) {
                // Kurangi jumlah di tabel Inventaris
                $update_inv = "UPDATE inventaris 
                             SET jumlah = jumlah - {$data['jumlah']}
                             WHERE id_inventaris = '{$data['id_inventaris']}'";
                mysqli_query($conn, $update_inv);

                // Tentukan tabel tujuan untuk status baru
                $detail_table = '';
                $date_field = '';
                switch ($data['status']) {
                    case 2:
                        $detail_table = 'perpindahan_barang';
                        $date_field = 'tanggal_perpindahan';
                        break;
                    case 3:
                        $detail_table = 'kerusakan_barang';
                        $date_field = 'tanggal_kerusakan';
                        break;
                    case 4:
                        $detail_table = 'kehilangan_barang';
                        $date_field = 'tanggal_kerusakan';
                        break;
                }

                // Pindahkan ke tabel status yang sesuai
                $detail_query = "INSERT INTO $detail_table 
                               (id_kontrol_barang, kode_barang, nama_barang,
                                cawu, $date_field, jumlah, keterangan)
                               VALUES (
                                   '{$data['id_kontrol']}',
                                   '{$old_data['kode_inventaris']}',
                                   '{$old_data['nama_barang']}',
                                   '{$data['cawu']}',
                                   '{$data['tanggal']}',
                                   '{$data['jumlah']}',
                                   '{$data['keterangan']}'
                               )";
                mysqli_query($conn, $detail_query);
            }

            // 2. BARANG RUSAK/PINDAH/HILANG MENJADI BAIK
            elseif ($old_data['status'] > 1 && $data['status'] == 1) {
                // Kembalikan jumlah ke tabel Inventaris
                $update_inv = "UPDATE inventaris 
                             SET jumlah = jumlah + {$old_data['jumlah']}
                             WHERE id_inventaris = '{$data['id_inventaris']}'";
                mysqli_query($conn, $update_inv);

                // Hapus dari tabel status sebelumnya
                mysqli_query($conn, "DELETE FROM perpindahan_barang WHERE id_kontrol_barang = '{$data['id_kontrol']}'");
                mysqli_query($conn, "DELETE FROM kerusakan_barang WHERE id_kontrol_barang = '{$data['id_kontrol']}'");
                mysqli_query($conn, "DELETE FROM kehilangan_barang WHERE id_kontrol_barang = '{$data['id_kontrol']}'");
            }

            // 3. PERPINDAHAN ANTAR STATUS RUSAK/PINDAH/HILANG
            elseif ($old_data['status'] > 1 && $data['status'] > 1) {
                // Hapus dari tabel status lama
                switch ($old_data['status']) {
                    case 2:
                        mysqli_query($conn, "DELETE FROM perpindahan_barang WHERE id_kontrol_barang = '{$data['id_kontrol']}'");
                        break;
                    case 3:
                        mysqli_query($conn, "DELETE FROM kerusakan_barang WHERE id_kontrol_barang = '{$data['id_kontrol']}'");
                        break;
                    case 4:
                        mysqli_query($conn, "DELETE FROM kehilangan_barang WHERE id_kontrol_barang = '{$data['id_kontrol']}'");
                        break;
                }

                // Pindahkan ke tabel status baru
                $detail_table = '';
                $date_field = '';
                switch ($data['status']) {
                    case 2:
                        $detail_table = 'perpindahan_barang';
                        $date_field = 'tanggal_perpindahan';
                        break;
                    case 3:
                        $detail_table = 'kerusakan_barang';
                        $date_field = 'tanggal_kerusakan';
                        break;
                    case 4:
                        $detail_table = 'kehilangan_barang';
                        $date_field = 'tanggal_kerusakan';
                        break;
                }

                // Insert ke tabel status baru
                $detail_query = "INSERT INTO $detail_table 
                               (id_kontrol_barang, kode_barang, nama_barang,
                                cawu, $date_field, jumlah, keterangan)
                               VALUES (
                                   '{$data['id_kontrol']}',
                                   '{$old_data['kode_inventaris']}',
                                   '{$old_data['nama_barang']}',
                                   '{$data['cawu']}',
                                   '{$data['tanggal']}',
                                   '{$data['jumlah']}',
                                   '{$data['keterangan']}'
                               )";
                mysqli_query($conn, $detail_query);
            }
        }

        // === PENGELOLAAN PERUBAHAN JUMLAH ===
        elseif ($old_data['status'] > 1 && $data['jumlah'] != $old_data['jumlah']) {
            $selisih = $data['jumlah'] - $old_data['jumlah'];

            // Update jumlah di tabel Inventaris
            $update_inv = "UPDATE inventaris 
                          SET jumlah = jumlah - $selisih
                          WHERE id_inventaris = '{$data['id_inventaris']}'";
            mysqli_query($conn, $update_inv);

            // Update jumlah di tabel status terkait
            $table_name = '';
            switch ($data['status']) {
                case 2:
                    $table_name = 'perpindahan_barang';
                    break;
                case 3:
                    $table_name = 'kerusakan_barang';
                    break;
                case 4:
                    $table_name = 'kehilangan_barang';
                    break;
            }

            if ($table_name) {
                $update_detail = "UPDATE $table_name 
                                SET jumlah = '{$data['jumlah']}'
                                WHERE id_kontrol_barang = '{$data['id_kontrol']}'";
                mysqli_query($conn, $update_detail);
            }
        }

        // === PENGELOLAAN ARSIP INVENTARIS ===
        // Cek apakah barang perlu diarsipkan (stok = 0)
        $check_stock = "SELECT jumlah FROM inventaris 
                       WHERE id_inventaris = '{$data['id_inventaris']}'";
        $stock_result = mysqli_query($conn, $check_stock);
        $stock_data = mysqli_fetch_assoc($stock_result);

        if ($stock_data['jumlah'] <= 0) {
            // Pindahkan ke tabel Arsip Inventaris
            $archive_query = "INSERT INTO arsip_inventaris 
                            (id_inventaris, kode_inventaris, nama_barang,
                             jumlah, satuan, status, tanggal_arsip, keterangan)
                            VALUES (
                                '{$data['id_inventaris']}',
                                '{$old_data['kode_inventaris']}',
                                '{$old_data['nama_barang']}',
                                '0',
                                '{$old_data['satuan']}',
                                'arsip',
                                NOW(),
                                'Diarsipkan karena stok habis'
                            )";
            mysqli_query($conn, $archive_query);

            // Hapus dari tabel Inventaris
            mysqli_query($conn, "DELETE FROM inventaris 
                               WHERE id_inventaris = '{$data['id_inventaris']}'");
        }

        // === UPDATE DATA KONTROL UTAMA ===
        // Update tanggal dan informasi lainnya
        $update_kontrol = "UPDATE kontrol_barang 
                          SET status = '{$data['status']}',
                              tanggal = '{$data['tanggal']}',
                              keterangan = '{$data['keterangan']}'
                          WHERE id_kontrol_barang = '{$data['id_kontrol']}'";
        mysqli_query($conn, $update_kontrol);

        // Commit transaksi jika semua operasi berhasil
        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        // Rollback semua perubahan jika terjadi kesalahan
        mysqli_rollback($conn);
        throw new Exception("Gagal mengupdate data: " . $e->getMessage());
    }
}