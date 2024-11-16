<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

// Ambil data penerimaan barang berdasarkan ID permintaan
$id_permintaan = $_GET['id']; // Ambil ID dari parameter GET

$query = "
    SELECT pb.*, d.nama_departemen 
    FROM penerimaan_barang pb
    JOIN permintaan_barang p ON pb.id_permintaan = p.id_permintaan
    JOIN departemen d ON p.id_departemen = d.id_departemen
    WHERE pb.id_permintaan = '$id_permintaan'
";

$result = $conn->query($query);

// Buat objek TCPDF
$pdf = new TCPDF();

// Set informasi dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin Gudang');
$pdf->SetTitle('Laporan Penerimaan Barang');
$pdf->SetSubject('Laporan Penerimaan Barang');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Set font
$pdf->SetFont('helvetica', '', 12);

// Tambah halaman
$pdf->AddPage();

// Cek apakah ada data yang ditemukan
if ($result->num_rows > 0) {
    // Fetch data
    $data = $result->fetch_assoc();

    // Konten laporan
    $departemen = $data['nama_departemen'];
    $tanggal_terima = $data['tanggal_terima'];
    $nama_barang = $data['nama_barang'];
    $jumlah = $data['jumlah'];
    $satuan = $data['satuan'];
    $status = $data['status'];

    // Isi konten
    $pdf->Cell(0, 10, 'LAPORAN PENERIMAAN BARANG', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 5, 'Nomor: ', 0, 1);
    $pdf->Cell(0, 5, "Tanggal Penerimaan: $tanggal_terima", 0, 1);
    $pdf->Cell(0, 5, "Departemen: $departemen", 0, 1);
    $pdf->Cell(0, 5, "Nama Barang: $nama_barang", 0, 1);
    $pdf->Cell(0, 5, "Jumlah Diterima: $jumlah $satuan", 0, 1);
    $pdf->Cell(0, 5, "Status Penerimaan: $status", 0, 1);
    $pdf->Ln(5);

    // Tanda Tangan
    $pdf->Cell(90, 10, 'Diterima Oleh (Admin Gudang)', 0, 0, 'C'); // Kolom Admin Gudang
    $pdf->Cell(90, 10, 'Diketahui Oleh (Admin ' . $departemen . ')', 0, 1, 'C'); // Kolom Admin Departemen

    // Garis untuk tanda tangan
    $pdf->Cell(90, 0, '', 'T', 1, 'C'); // Garis horizontal untuk Admin Gudang
    $pdf->Cell(90, 0, '', 'T', 1, 'C'); // Garis horizontal untuk Admin Departemen

    $pdf->Cell(90, 10, '_______________________', 0, 0, 'C'); // Tempat untuk nama Admin Gudang
    $pdf->Cell(90, 10, '_______________________', 0, 1, 'C'); // Tempat untuk nama Admin Departemen

    $pdf->Cell(90, 10, 'Admin Gudang', 0, 0, 'C'); // Tempat untuk jabatan Admin Gudang
    $pdf->Cell(90, 10, 'Admin ' . $departemen, 0, 1, 'C'); // Tempat untuk jabatan Admin Departemen

} else {
    $pdf->Cell(0, 10, 'Data penerimaan barang tidak ditemukan.', 0, 1, 'C');
}

// Tutup dan output PDF
$pdf->Output('laporan_penerimaan_barang.pdf', 'I');
?>