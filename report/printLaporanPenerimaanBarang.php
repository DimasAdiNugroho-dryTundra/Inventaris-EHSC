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
    $pdf->Cell(0, 10, 'LAPORAN PERMINTAAN BARANG', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 5, 'No: ', 0, 1);
    $pdf->Cell(0, 5, "Tanggal: $tanggal_terima", 0, 1);
    $pdf->Cell(0, 5, "Departemen: $departemen", 0, 1);
    $pdf->Cell(0, 5, 'Lampiran:', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Kepada Yth.', 0, 1);
    $pdf->Cell(0, 5, 'Direksi', 0, 1);
    $pdf->Cell(0, 5, 'Di Tempat', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'Dengan Hormat,', 0, 1);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, 'Laporan ini merinci jenis dan jumlah barang yang telah diterima, diharapkan dapat memastikan kesesuaian dengan permintaan dan kebutuhan. Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses penerimaan ini.', 0, 'L', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 0, 'Berikut adalah tabel rincian barang yang diterima.', 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 12);

    $pdf->Cell(64, 10, 'Nama Barang', 1, 0, 'C');
    $pdf->Cell(64, 10, 'Jumlah', 1, 0, 'C');
    $pdf->Cell(64, 10, 'Satuan', 1, 1, 'C');

    // Isi tabel
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(64, 10, $nama_barang, 1, 0, 'C');
    $pdf->Cell(64, 10, $jumlah, 1, 0, 'C');
    $pdf->Cell(64, 10, $satuan, 1, 1, 'C');

    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, 'Demikian laporan ini kami buat untuk dapat digunakan sebagaimana mestinya.', 0, 'L', 0, 1);

    $pdf->Ln(5); // Jarak setelah garis
    $pdf->Cell(90, 10, 'Gudang, ' . $tanggal_terima, 0, 0, 'C');
    $pdf->Cell(90, 10, $departemen . ', ' . $tanggal_terima, 0, 1, 'C');

    $pdf->Ln(5); // Jarak setelah garis
    $pdf->Cell(90, 10, '_______________________', 0, 0, 'C');
    $pdf->Cell(90, 10, '_______________________', 0, 1, 'C');

    $pdf->Cell(90, 10, 'Admin Gudang', 0, 0, 'C');
    $pdf->Cell(90, 10, 'Admin ' . $departemen, 0, 1, 'C');

} else {
    $pdf->Cell(0, 10, 'Data penerimaan barang tidak ditemukan.', 0, 1, 'C');
}

// Tutup dan output PDF
$pdf->Output('laporan_penerimaan_barang.pdf', 'I');
?>