<?php
// File: server/printQRCode.php

// Mencegah output sebelum generasi PDF untuk menghindari error
ob_start();

// Import library yang dibutuhkan
require('../lib/phpqrcode/qrlib.php');    // Library untuk generate QR Code
require('../lib/TCPDF/tcpdf.php');        // Library untuk generate PDF
require('../server/configDB.php');                   // Konfigurasi database

// Mengambil ID inventaris dari parameter URL
$id_inventaris = $_GET['id'] ?? '';

// Query untuk mengambil data inventaris
// Menggunakan JOIN untuk mendapatkan nama departemen dan kategori
// Menggunakan LEFT JOIN dengan tabel penerimaan_barang untuk mendapatkan nama barang alternatif
$query = "SELECT i.*, d.nama_departemen, k.nama_kategori, 
          CASE 
              WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
              ELSE i.nama_barang 
          END as nama_barang
          FROM inventaris i
          JOIN departemen d ON i.id_departemen = d.id_departemen
          JOIN kategori k ON i.id_kategori = k.id_kategori
          LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
          WHERE i.id_inventaris = '$id_inventaris'";

// Eksekusi query langsung
$result = mysqli_query($conn, $query);
$inventaris = mysqli_fetch_assoc($result);

// Cek apakah data inventaris ditemukan
if (!$inventaris) {
    die('Data inventaris tidak ditemukan');
}

// Membuat direktori temporary untuk menyimpan file QR Code
$tempDir = __DIR__ . '/../temp/';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}

// Generate nama file QR Code unik menggunakan MD5 dari kode inventaris
$qrFile = $tempDir . 'qr_' . md5($inventaris['kode_inventaris']) . '.png';

// Generate QR Code
try {
    QRcode::png($inventaris['kode_inventaris'], $qrFile, QR_ECLEVEL_L, 10);
} catch (Exception $e) {
    die('Gagal membuat QR Code: ' . $e->getMessage());
}

// Pastikan file QR Code berhasil dibuat
if (!file_exists($qrFile)) {
    die('Gagal membuat file gambar QR Code');
}

// Kelas turunan TCPDF untuk kustomisasi header
class MYPDF extends TCPDF
{
    private $headerTitle;

    // Method untuk mengatur judul header
    public function setHeaderTitle($title)
    {
        $this->headerTitle = $title;
    }

    // Override method header bawaan TCPDF
    public function Header()
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, $this->headerTitle, 0, true, 'C');
    }
}

// Konfigurasi ukuran dalam millimeter
$qrSizeMM = 20;      // Ukuran QR Code (20mm = 2cm)
$spacingMM = 30;     // Jarak antar QR Code
$textHeightMM = 15;  // Tinggi area teks di bawah QR Code

// Inisialisasi PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setHeaderTitle('QR Code ' . $inventaris['nama_barang']);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PT. Buana Karya Bhakti');
$pdf->SetTitle('QR Codes - ' . $inventaris['kode_inventaris']);

// Atur margin PDF (dalam mm)
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

$pdf->AddPage();

// Hitung posisi QR Code
$codesPerRow = 2;                    // Jumlah QR Code per baris
$totalCodes = $inventaris['jumlah']; // Total QR Code yang akan dibuat
$pageWidth = $pdf->getPageWidth();   // Lebar halaman
$pageHeight = $pdf->getPageHeight(); // Tinggi halaman
$marginLeft = 5;                     // Margin kiri
$marginTop = 25;                     // Margin atas (termasuk header)

$currentX = $marginLeft;
$currentY = $marginTop;
$counter = 0;

// Hitung jarak antar QR Code
$totalWidth = ($qrSizeMM * $codesPerRow) + ($spacingMM * ($codesPerRow - 1));
$startX = ($pageWidth - $totalWidth) / 2;

// Jarak vertikal antar baris
$rowSpacing = 10;

// Loop untuk membuat QR Code sesuai jumlah
for ($i = 0; $i < $totalCodes; $i++) {
    // Cek apakah perlu pindah baris
    if ($counter >= $codesPerRow) {
        $currentY += $qrSizeMM + $textHeightMM + $rowSpacing;
        $counter = 0;

        // Cek apakah perlu halaman baru
        if ($currentY + $qrSizeMM + $textHeightMM > $pageHeight - 15) {
            $pdf->AddPage();
            $currentY = $marginTop;
        }
    }

    // Hitung posisi X untuk QR Code saat ini
    $x = $startX + ($counter * ($qrSizeMM + $spacingMM));

    // Tambahkan QR Code ke PDF
    if (file_exists($qrFile)) {
        $pdf->Image($qrFile, $x, $currentY, $qrSizeMM, $qrSizeMM);
    }

    // Tambahkan teks di bawah QR Code
    $pdf->SetTextColor(0, 0, 0); // Warna teks hitam

    // Nama Barang
    $pdf->SetXY($x, $currentY + $qrSizeMM + 2);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell($qrSizeMM, 4, 'Nama: ' . $inventaris['nama_barang'], 0, 1, 'C');

    // Departemen
    $pdf->SetXY($x, $currentY + $qrSizeMM + 6);
    $pdf->Cell($qrSizeMM, 4, 'Dept: ' . $inventaris['nama_departemen'], 0, 1, 'C');

    // Kode Inventaris
    $pdf->SetXY($x, $currentY + $qrSizeMM + 10);
    $pdf->Cell($qrSizeMM, 4, 'Kode: ' . $inventaris['kode_inventaris'], 0, 1, 'C');

    $counter++;
}

// Hapus file QR Code temporary
if (file_exists($qrFile)) {
    @unlink($qrFile);
}

ob_end_clean();

// Output PDF untuk didownload
$pdf->Output('QRCodes_' . $inventaris['kode_inventaris'] . '.pdf', 'D');
exit;
?>