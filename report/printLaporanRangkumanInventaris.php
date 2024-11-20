<?php
ob_start();
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

// Extend TCPDF with custom header
class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 15, 'Laporan Inventaris', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(5);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 15, 'Tahun: ' . $_POST['year'], 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$selectedYear = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

$selectedYear = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

if (!is_numeric($selectedYear) || $selectedYear < 2000) {
    die('Tahun tidak valid.');
}

$query = " SELECT 
        i.nama_barang, 
        i.kode_inventaris, 
        COALESCE(kbc1.jumlah_baik, 0) + COALESCE(kbc1.jumlah_rusak, 0) + COALESCE(kbc1.jumlah_pindah, 0) + COALESCE(kbc1.jumlah_hilang, 0) AS jumlah_awal,
        i.satuan,
        COALESCE(kbc1.jumlah_baik, 0) as jumlah_baik_cawu_satu,
        COALESCE(kbc1.jumlah_rusak, 0) as jumlah_rusak_cawu_satu,
        COALESCE(kbc1.jumlah_pindah, 0) as jumlah_pindah_cawu_satu,
        COALESCE(kbc1.jumlah_hilang, 0) as jumlah_hilang_cawu_satu,
        COALESCE(kbc2.jumlah_baik, 0) as jumlah_baik_cawu_dua,
        COALESCE(kbc2.jumlah_rusak, 0) as jumlah_rusak_cawu_dua,
        COALESCE(kbc2.jumlah_pindah, 0) as jumlah_pindah_cawu_dua,
        COALESCE(kbc2.jumlah_hilang, 0) as jumlah_hilang_cawu_dua,
        COALESCE(kbc3.jumlah_baik, 0) as jumlah_baik_cawu_tiga,
        COALESCE(kbc3.jumlah_rusak, 0) as jumlah_rusak_cawu_tiga,
        COALESCE(kbc3.jumlah_pindah, 0) as jumlah_pindah_cawu_tiga,
        COALESCE(kbc3.jumlah_hilang, 0) as jumlah_hilang_cawu_tiga
    FROM inventaris i
    LEFT JOIN kontrol_barang_cawu_satu kbc1 ON i.id_inventaris = kbc1.id_inventaris AND kbc1.tahun_kontrol = $selectedYear
    LEFT JOIN kontrol_barang_cawu_dua kbc2 ON i.id_inventaris = kbc2.id_inventaris AND kbc2.tahun_kontrol = $selectedYear
    LEFT JOIN kontrol_barang_cawu_tiga kbc3 ON i.id_inventaris = kbc3.id_inventaris AND kbc3.tahun_kontrol = $selectedYear
    WHERE EXISTS (
        SELECT 1 FROM kontrol_barang_cawu_satu k1 
        WHERE k1.id_inventaris = i.id_inventaris AND k1.tahun_kontrol = $selectedYear
        UNION
        SELECT 1 FROM kontrol_barang_cawu_dua k2 
        WHERE k2.id_inventaris = i.id_inventaris AND k2.tahun_kontrol = $selectedYear
        UNION
        SELECT 1 FROM kontrol_barang_cawu_tiga k3 
        WHERE k3.id_inventaris = i.id_inventaris AND k3.tahun_kontrol = $selectedYear
    )
";

$result = mysqli_query($conn, $query);

$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Laporan Inventaris ' . $selectedYear);

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set margins
$pdf->SetMargins(10, 30, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Add a page
$pdf->AddPage('L', 'A4');

// Set font
$pdf->SetFont('helvetica', '', 8);

// Header tabel
$header = array('No', 'Nama Barang', 'Kode', 'Jml Awal', 'Satuan', 
                'B1', 'R1', 'P1', 'H1',
                'B2', 'R2', 'P2', 'H2',
                'B3', 'R3', 'P3', 'H3', 'Jml Akhir');

// Column widths
$w = array(10, 40, 25, 15, 15, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 20);

// Header
for($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
}
$pdf->Ln();

// Data
if (mysqli_num_rows($result) > 0) {
    $no = 1;
    while($row = mysqli_fetch_assoc($result)) {
        $jumlah_akhir = $row['jumlah_baik_cawu_tiga'];
        
        $pdf->Cell($w[0], 6, $no++, 1, 0, 'C');
        $pdf->Cell($w[1], 6, $row['nama_barang'], 1);
        $pdf->Cell($w[2], 6, $row['kode_inventaris'], 1, 0, 'C');
        $pdf->Cell($w[3], 6, $row['jumlah_awal'], 1, 0, 'C');
        $pdf->Cell($w[4], 6, $row['satuan'], 1, 0, 'C');
        $pdf->Cell($w[5], 6, $row['jumlah_baik_cawu_satu'], 1, 0, 'C');
        $pdf->Cell($w[6], 6, $row['jumlah_rusak_cawu_satu'], 1, 0, 'C');
        $pdf->Cell($w[7], 6, $row['jumlah_pindah_cawu_satu'], 1, 0, 'C');
        $pdf->Cell($w[8], 6, $row['jumlah_hilang_cawu_satu'], 1, 0, 'C');
        $pdf->Cell($w[9], 6, $row['jumlah_baik_cawu_dua'], 1, 0, 'C');
        $pdf->Cell($w[10], 6, $row['jumlah_rusak_cawu_dua'], 1, 0, 'C');
        $pdf->Cell($w[11], 6, $row['jumlah_pindah_cawu_dua'], 1, 0, 'C');
        $pdf->Cell($w[12], 6, $row['jumlah_hilang_cawu_dua'], 1, 0, 'C');
        $pdf->Cell($w[13], 6, $row['jumlah_baik_cawu_tiga'], 1, 0, 'C');
        $pdf->Cell($w[14], 6, $row['jumlah_rusak_cawu_tiga'], 1, 0, 'C');
        $pdf->Cell($w[15], 6, $row['jumlah_pindah_cawu_tiga'], 1, 0, 'C');
        $pdf->Cell($w[16], 6, $row['jumlah_hilang_cawu_tiga'], 1, 0, 'C');
        $pdf->Cell($w[17], 6, $jumlah_akhir, 1, 0, 'C');
        $pdf->Ln();
    }
} else {
    $pdf->Cell(array_sum($w), 6, 'Tidak ada data yang dikontrol untuk tahun ' . $selectedYear, 1, 0, 'C');
}

// Tambahkan keterangan
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Keterangan:', 0, 1);
$pdf->Cell(0, 6, 'B = Baik', 0, 1);
$pdf->Cell(0, 6, 'R = Rusak', 0, 1);
$pdf->Cell(0, 6, 'P = Pindah', 0, 1);
$pdf->Cell(0, 6, 'H = Hilang', 0, 1);

ob_end_clean();

// Output PDF
$pdf->Output('laporan_inventaris_' . $selectedYear . '.pdf', 'I');
exit();
?>