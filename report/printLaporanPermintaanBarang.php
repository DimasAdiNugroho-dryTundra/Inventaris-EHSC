<?php
// Matikan semua error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Mulai output buffering
ob_start();

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

// Fungsi format rupiah
function formatRupiah($angka)
{
    return 'Rp. ' . number_format($angka, 0, ',', '.');
}

// Buat class turunan TCPDF untuk kustomisasi header
class MYPDF extends TCPDF
{
    public function Header()
    {
        $image_file = 'headerLaporan.png';
        $this->setPageMark();
        $this->Image($image_file, 10, 5, 190, 0, 'PNG', '', 'T', false, 300, 'T', false, false, 0, false, false, false);
        $this->SetY(0);
    }
}


try {
    // Buat objek TCPDF dengan class kustom
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set informasi dokumen
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Admin ' . $data['nama_departemen']);
    $pdf->SetTitle('Laporan Permintaan Barang');

    // Set margins
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);

    // Set image scale factor
    $pdf->setImageScale(1.25);

    // Set font
    $pdf->SetFont('helvetica', '', 11);

    // Add a page
    $pdf->AddPage();

    // Cek apakah ada data yang ditemukan
    if ($result->num_rows > 0) {
        // Fetch data
        $data = $result->fetch_assoc();

        // Hitung total harga
        $total_harga = $data['kebutuhan_qty'] * $data['harga_satuan'];

        // HTML Content
        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN PERMINTAAN BARANG</h1>

        <p>
Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . $data['id_permintaan'] . '<br>
Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . date('d/m/Y', strtotime($data['tanggal_permintaan'])) . '<br>
Departemen&nbsp;: ' . $data['nama_departemen'] . '<br>
Lampiran&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: -
</p>
        
        <br>
        <p>Kepada Yth.<br>Admin Gudang<br>Di Tempat</p>

        <p>Dengan Hormat,</p>
        <p style="text-align: justify;">Laporan ini merinci jenis dan jumlah barang yang diperlukan, 
        diharapkan dapat membantu dalam pengadaan yang efisien dan tepat waktu. 
        Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses ini.</p>
        
        <br>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2;">
                <td><strong>Nama Barang</strong></td>
                <td>' . $data['nama_barang'] . '</td>
            </tr>
            <tr>
                <td><strong>Spesifikasi</strong></td>
                <td>' . $data['spesifikasi'] . '</td>
            </tr>
            <tr>
                <td><strong>Jumlah Kebutuhan</strong></td>
                <td>' . $data['kebutuhan_qty'] . '</td>
            </tr>
            <tr>
                <td><strong>Harga Satuan</strong></td>
                <td>' . formatRupiah($data['harga_satuan']) . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td><strong>Total Harga</strong></td>
                <td>' . formatRupiah($total_harga) . '</td>
            </tr>
        </table>
        
        <br>
        <p>Demikian laporan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
        
        <br><br>
        <table cellpadding="5">
            <tr>
                <td width="50%" style="text-align: center;">Menyetujui,</td>
                <td width="50%" style="text-align: center;">Pemohon,</td>
            </tr>
            <tr>
                <td height="60"></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">(........................)</td>
                <td style="text-align: center;">(........................)</td>
            </tr>
            <tr>
                <td style="text-align: center;">Admin Gudang</td>
                <td style="text-align: center;">Admin ' . $data['nama_departemen'] . '</td>
            </tr>
        </table>';

        // Output HTML
        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        $pdf->Cell(0, 10, 'Data tidak ditemukan', 0, 1, 'C');
    }

    // Bersihkan output buffer
    ob_end_clean();

    // Output PDF
    $pdf->Output('laporan_permintaan_barang_' . $id_permintaan . '.pdf', 'I');
} catch (Exception $e) {
    // Tampilkan error jika terjadi masalah
    echo 'Error: ' . $e->getMessage();
}
?>