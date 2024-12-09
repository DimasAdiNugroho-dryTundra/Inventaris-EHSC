<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

// Ambil data permintaan barang
$id_permintaan = $_GET['id']; 

$query = "
    SELECT pb.*, d.nama_departemen 
    FROM permintaan_barang pb
    JOIN departemen d ON pb.id_departemen = d.id_departemen
    WHERE pb.id_permintaan = '$id_permintaan'
";

$result = $conn->query($query);

// Buat objek TCPDF
$pdf = new TCPDF();

// Set informasi dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin' . $data['nama_departemen']);
$pdf->SetTitle('Laporan Permintaan Barang');
$pdf->SetSubject('Laporan Permintaan Barang');
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
    $tanggal_permintaan = $data['tanggal_permintaan'];
    $nama_barang = $data['nama_barang'];
    $spesifikasi = $data['spesifikasi'];
    $kebutuhan_qty = $data['kebutuhan_qty'];
    $harga = $data['harga_satuan'];
    $total_harga = $kebutuhan_qty * $harga;

    // Isi konten
    $pdf->Cell(0, 10, 'LAPORAN PERMINTAAN BARANG', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 5, 'No: ', 0, 1);
    $pdf->Cell(0, 5, "Tanggal: $tanggal_permintaan", 0, 1);
    $pdf->Cell(0, 5, "Departemen: $departemen", 0, 1);
    $pdf->Cell(0, 5, 'Lampiran:', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Kepada Yth.', 0, 1);
    $pdf->Cell(0, 5, 'Admin Gudang', 0, 1);
    $pdf->Cell(0, 5, 'Di Tempat', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'Dengan Hormat,', 0, 1);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, 'Laporan ini merinci jenis dan jumlah barang yang diperlukan, diharapkan dapat membantu dalam pengadaan yang efisien dan tepat waktu. Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses ini.', 0, 'L', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 0, 'Berikut adalah tabel rincian barang yang diminta.', 0, 1);
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 12);

    // Tambahkan sel dengan border untuk Nama Barang dan Spesifikasi
    $pdf->Cell(192, 10, 'Nama Barang: ' . $nama_barang, 1, 1, 'L');
    $pdf->Cell(192, 10, 'Spesifikasi: ' . $spesifikasi, 1, 1, 'L');

    $pdf->Cell(64, 10, 'Jumlah Kebutuhan', 1, 0, 'C');
    $pdf->Cell(64, 10, 'Harga', 1, 0, 'C');
    $pdf->Cell(64, 10, 'Total Harga', 1, 1, 'C');

    // Isi tabel
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(64, 10, $kebutuhan_qty, 1, 0, 'C');
    $pdf->Cell(64, 10, $harga, 1, 0, 'C');
    $pdf->Cell(64, 10, $total_harga, 1, 1, 'C');

    $pdf->Ln(5);
    $pdf->MultiCell(0, 10, 'Demikian laporan ini kami buat untuk dapat digunakan sebagaimana mestinya.', 0, 'L', 0, 1);

    $pdf->Ln(5); // Jarak setelah garis
    $pdf->Cell(90, 10, 'Gudang, ' . $tanggal_permintaan, 0, 0, 'C');
    $pdf->Cell(90, 10, $departemen . ', ' . $tanggal_permintaan, 0, 1, 'C');

    $pdf->Ln(5); // Jarak setelah garis
    $pdf->Cell(90, 10, '_______________________', 0, 0, 'C');
    $pdf->Cell(90, 10, '_______________________', 0, 1, 'C');

    $pdf->Cell(90, 10, 'Admin Gudang', 0, 0, 'C');
    $pdf->Cell(90, 10, 'Admin ' . $departemen, 0, 1, 'C');

} else {
    $pdf->Cell(0, 10, 'Data permintaan barang tidak ditemukan.', 0, 1, 'C');
}

// Tutup dan output PDF
$pdf->Output('laporan_permintaan_barang.pdf', 'I');
?>