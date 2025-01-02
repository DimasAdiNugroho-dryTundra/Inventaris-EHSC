<?php
ob_start();

require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');


$id_permintaan = $_GET['id'];

$query = "SELECT pb.*, d.* FROM permintaan_barang pb 
          JOIN departemen d ON pb.id_departemen = d.id_departemen 
            WHERE pb.id_permintaan = '$id_permintaan'";

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
    $pdf->SetAuthor('Admin ' . $data['kode_departemen']);
    $pdf->SetTitle('Laporan Permintaan Barang');

    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);

    $pdf->SetAutoPageBreak(TRUE, 15);

    $pdf->setImageScale(1.25);

    $pdf->SetFont('helvetica', '', 11);

    $pdf->AddPage();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        $status_keterangan = '';
        if ($data['status'] == 0) {
            $status_keterangan = 'Menunggu';
        } elseif ($data['status'] == 1) {
            $status_keterangan = 'Disetujui';
        } else {
            $status_keterangan = 'Tidak Disetujui';
        }

        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN PERMINTAAN BARANG</h1>

        <p>Nomor: ' . $data['id_permintaan'] . '<br>Tanggal: ' . date('d/m/Y', strtotime($data['tanggal_permintaan'])) . '<br>Departemen: ' . $data['nama_departemen'] . '</p>
        
        <p>Kepada Yth.<br>Staff ' . $data['kode_departemen'] . '<br>Di Tempat</p>

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
                <td><strong>Merk</strong></td>
                <td>' . $data['merk'] . '</td>
            </tr>
            <tr>
                <td><strong>Tanggal Permintaan</strong></td>
                <td>' . date('d/m/Y', strtotime($data['tanggal_permintaan'])) . '</td>
            </tr>
            <tr>
                <td><strong>Jumlah</strong></td>
                <td>' . $data['jumlah_kebutuhan'] . ' ' . $data['satuan'] . '</td>
            </tr>
            <tr>
                <td><strong>Spesifikasi</strong></td>
                <td>' . $data['spesifikasi'] . '</td>
            </tr>
             <tr>
                <td><strong>Status</strong></td>
                <td>' . $status_keterangan . '</td>
            </tr>
        </table>
        
        <br>
        <p>Demikian laporan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
        
        <br><br>
        <table cellpadding="5">
            <tr>
                <td width="50%" style="text-align: center;">Mengetahui,</td>
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
                <td style="text-align: center;">Staff ' . $data['kode_departemen'] . '</td>
                <td style="text-align: center;">Admin ' . $data['kode_departemen'] . '</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        $pdf->Cell(0, 10, 'Data tidak ditemukan', 0, 1, 'C');
    }

    ob_end_clean();

    $pdf->Output('laporan_permintaan_barang_' . $id_permintaan . '.pdf', 'I');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>