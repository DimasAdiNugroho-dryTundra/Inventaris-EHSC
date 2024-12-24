<?php
ob_start();

require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

$id_permintaan = $_GET['id'] ?? null;

$query = "SELECT pb.*, 
            COALESCE(d.nama_departemen, d2.nama_departemen) AS nama_departemen,
            COALESCE(d.kode_departemen, d2.kode_departemen) AS kode_departemen
            FROM penerimaan_barang pb
            LEFT JOIN permintaan_barang p ON pb.id_permintaan = p.id_permintaan
            LEFT JOIN departemen d ON p.id_departemen = d.id_departemen
            LEFT JOIN departemen d2 ON pb.id_departemen = d2.id_departemen
            WHERE pb.id_permintaan IS NULL OR pb.id_permintaan = '$id_permintaan'
            ";

$result = $conn->query($query);

class MYPDF extends TCPDF
{
    public function Header()
    {
        $image_file = 'headerLaporan.png';
        $this->setPageMark();
        $this->Image($image_file, 10, 5, 190, 0, 'PNG', '', 'T', false, 300, 'T', false, false, 0, false, false, false);
        $this->SetY(40);
    }
}

try {
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Laporan Penerimaan Barang');
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setImageScale(1.25);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->AddPage();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN PENERIMAAN BARANG</h1>

        <p>Nomor: ' . $data['id_penerimaan'] . '<br>Tanggal: ' . date('d/m/Y', strtotime($data['tanggal_terima'])) . '<br>Departemen: ' . $data['nama_departemen'] . '</p>
        
        <p>Kepada Yth.<br>Staff ' . $data['kode_departemen'] . '<br>Di Tempat</p>

        <p>Dengan Hormat,</p>
        <p style="text-align: justify;">Laporan ini merinci jenis dan jumlah barang yang telah diterima, diharapkan dapat memastikan kesesuaian dengan permintaan dan kebutuhan. Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses penerimaan ini.</p>
        
        <p>Berikut adalah tabel rincian barang yang diterima.</p>
        
        <br>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2;">
                <td><strong>Nama Barang</strong></td>
                <td>' . $data['nama_barang'] . '</td>
            </tr>
            <tr>
                <td><strong>Merk</strong></td>
                <td>' . $data['merk'] . '</td>
            </tr>
            <tr>
                <td><strong>Tanggal Penerimaan</strong></td>
                <td>' . date('d/m/Y', strtotime($data['tanggal_terima'])) . '</td>
            </tr>
            <tr>
                <td><strong>Sumber</strong></td>
                <td>' . $data['sumber_penerimaan'] . '</td>
            </tr>
            <tr>
                <td><strong>Jumlah</strong></td>
                <td>' . $data['jumlah'] . '</td>
            </tr>
            <tr>
                <td><strong>Satuan</strong></td>
                <td>' . $data['satuan'] . '</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>' . $data['status'] . '</td>
            </tr>
        </table>
        
        <br>
        <p>Demikian laporan ini kami buat untuk dapat digunakan sebagaimana mestinya.</p>
        
        <br><br>
        <table cellpadding="5">
            <tr>
                <td width="50%" style="text-align: center;">Disetujui</td>
                <td width="50%" style="text-align: center;">Penerima</td>
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
                <td style="text-align: center;">Staff ' . $data['kode_departemen'] . '</td>
                <td style="text-align: center;">Admin ' . $data['kode_departemen'] . '</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        $pdf->Cell(0, 10, 'Data penerimaan barang tidak ditemukan.', 0, 1, 'C');
    }

    ob_end_clean();
    $pdf->Output('laporan_penerimaan_barang.pdf', 'I');

} catch (Exception $e) {
    error_log('PDF Generation Error: ' . $e->getMessage());
    echo 'Error: ' . $e->getMessage();
}
?>