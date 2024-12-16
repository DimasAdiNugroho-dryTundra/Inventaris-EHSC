<?php

ob_start();

require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

$id_kehilangan_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query = "SELECT kb.*, 
            i.nama_barang, 
            i.kode_inventaris, 
            i.merk,
            r.nama_ruangan,
            i.sumber_inventaris,
            d.nama_departemen,
            COALESCE(u1.nama, u2.nama, u3.nama) AS nama_petugas
            FROM kehilangan_barang kb
            JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
            JOIN ruangan r ON i.id_ruangan = r.id_ruangan 
            JOIN departemen d ON i.id_departemen = d.id_departemen
            LEFT JOIN kontrol_barang_cawu_satu k1 ON k1.id_inventaris = i.id_inventaris
            LEFT JOIN kontrol_barang_cawu_dua k2 ON k2.id_inventaris = i.id_inventaris
            LEFT JOIN kontrol_barang_cawu_tiga k3 ON k3.id_inventaris = i.id_inventaris
            LEFT JOIN user u1 ON k1.id_user = u1.id_user
            LEFT JOIN user u2 ON k2.id_user = u2.id_user
            LEFT JOIN user u3 ON k3.id_user = u3.id_user
            WHERE 
            kb.id_kehilangan_barang = $id_kehilangan_barang";
$result = mysqli_query($conn, $query);

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
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Laporan Kehilangan Barang');
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setImageScale(1.25);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->AddPage();

    if ($row = mysqli_fetch_assoc($result)) {
        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN KEHILANGAN BARANG</h1>

        <p>Nomor: ' . $row['id_kehilangan_barang'] . '<br>Tanggal: ' . date('d/m/Y', strtotime($row['tanggal_kehilangan'])) . '<br>Departemen: ' . $row['nama_departemen'] . '</p>

        <p>Kepada Yth.<br>Staff ' . $row['kode_departemen'] . '<br>Di Tempat</p>

        <p>Dengan Hormat,</p>
        <p style="text-align: justify;">Laporan ini merinci kehilangan barang yang terjadi pada inventaris kami. 
        Diharapkan dapat segera ditindaklanjuti sesuai dengan prosedur yang berlaku. 
        Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses ini.</p>
        
        <br>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2;">
                <td><strong>Kode Inventaris</strong></td>
                <td>' . $row['kode_inventaris'] . '</td>
            </tr>
            <tr>
                <td><strong>Nama Barang</strong></td>
                <td>' . $row['nama_barang'] . '</td>
            </tr>
            <tr>
                <td><strong>Merk</strong></td>
                <td>' . $row['merk'] . '</td>
            </tr>
            <tr>
                <td><strong>Tanggal Kehilangan</strong></td>
                <td>' . $row['tanggal_kehilangan'] . '</td>
            </tr>
            <tr>
                <td><strong>Jumlah Kehilangan</strong></td>
                <td>' . $row['jumlah_kehilangan'] . ' ' . $row['satuan'] . '</td>
            </tr>
            <tr>
                <td><strong>Cawu</strong></td>
                <td>' . $row['cawu'] . '</td>
            </tr>
            <tr>
                <td><strong>Sumber Inventaris</strong></td>
                <td>' . $row['sumber_inventaris'] . '</td>
            </tr>
            <tr>
                <td><strong>Petugas Kontrol</strong></td>
                <td>' . $row['nama_petugas'] . '</td>
            </tr>   
            <tr style="background-color: #f2f2f2;">
                <td><strong>Keterangan</strong></td>
                <td>' . nl2br($row['keterangan']) . '</td>
            </tr>
        </table>
        
        <br>
        <p>Demikian laporan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
        
        <br><br>
        <table cellpadding="5">
            <tr>
                <td width="33%" style="text-align: center;">Mengetahui,</td>
                <td width="33%" style="text-align: center;">Pelapor,</td>
                <td width="33%" style="text-align: center;">Petugas Kontrol,</td>
            </tr>
            <tr>
                <td height="60"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">(........................)</td>
                <td style="text-align: center;">(........................)</td>
                <td style="text-align: center;">(........................)</td>
            </tr>
            <tr>
                <td style="text-align: center;">Staff ' . $row['kode_departemen'] . '</td>
                <td style="text-align: center;">Admin ' . $row['kode_departemen'] . '</td>
                <td style="text-align: center;">' . $row['nama_petugas'] . '</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        $pdf->Cell(0, 10, 'Data tidak ditemukan', 0, 1, 'C');
    }

    ob_end_clean();
    $pdf->Output('laporan_kehilangan_barang_' . $id_kehilangan_barang . '.pdf', 'I');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}