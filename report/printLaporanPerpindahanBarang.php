<?php
ob_start();

require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

$id_perpindahan_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query = "SELECT pb.*, 
            i_asal.kode_inventaris AS kode_inventaris_asal, 
            i_asal.nama_barang AS nama_barang_asal, 
            i_asal.merk AS merk_asal, 
            i_asal.jumlah_awal, 
            i_asal.jumlah_akhir, 
            i_asal.sumber_inventaris AS sumber_asal,
            i_asal.satuan AS satuan_asal,
            r_asal.nama_ruangan AS ruangan_asal,
            i_baru.kode_inventaris AS kode_inventaris_baru, 
            i_baru.nama_barang AS nama_barang_baru, 
            i_baru.merk AS merk_baru,
            i_baru.satuan AS satuan_baru,
            r_baru.nama_ruangan AS ruangan_baru,
            d.nama_departemen, 
            d.kode_departemen,
            COALESCE(u1.nama, u2.nama, u3.nama) AS nama_petugas
        FROM perpindahan_barang pb
        JOIN inventaris i_asal ON pb.id_inventaris = i_asal.id_inventaris
        JOIN ruangan r_asal ON i_asal.id_ruangan = r_asal.id_ruangan
        JOIN inventaris i_baru ON i_baru.kode_inventaris = pb.kode_inventaris_baru
        JOIN ruangan r_baru ON i_baru.id_ruangan = r_baru.id_ruangan
        JOIN departemen d ON i_asal.id_departemen = d.id_departemen
        LEFT JOIN kontrol_barang_cawu_satu k1 ON k1.id_inventaris = i_asal.id_inventaris
        LEFT JOIN kontrol_barang_cawu_dua k2 ON k2.id_inventaris = i_asal.id_inventaris
        LEFT JOIN kontrol_barang_cawu_tiga k3 ON k3.id_inventaris = i_asal.id_inventaris
        LEFT JOIN user u1 ON k1.id_user = u1.id_user
        LEFT JOIN user u2 ON k2.id_user = u2.id_user
        LEFT JOIN user u3 ON k3.id_user = u3.id_user
        WHERE pb.id_perpindahan_barang = $id_perpindahan_barang";

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
    $pdf->SetTitle('Laporan Perpindahan Barang');
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setImageScale(1.25);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->AddPage();

    if ($row = mysqli_fetch_assoc($result)) {
        $html = '
        <h1 style="text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;">LAPORAN PERPINDAHAN BARANG</h1>

        <p>Nomor: ' . $row['id_perpindahan_barang'] . '<br>Tanggal: ' . date('d/m/Y', strtotime($row['tanggal_perpindahan'])) . '<br>Departemen: ' . $row['nama_departemen'] . '</p>

        <p>Kepada Yth.<br>Staff ' . $row['kode_departemen'] . '<br>Di Tempat</p>

        <p>Dengan Hormat,</p>
        <p style="text-align: justify;">Laporan ini merinci perpindahan barang yang terjadi pada inventaris kami. 
        Diharapkan dapat segera ditindaklanjuti sesuai dengan prosedur yang berlaku. 
        Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses ini.</p>

        <h3>Inventaris Asal</h3>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2;">
                <td><strong>Kode Barang</strong></td>
                <td>' . $row['kode_inventaris_asal'] . '</td>
            </tr>
            <tr>
                <td><strong>Nama Barang & Merk</strong></td>
                <td>' . $row['nama_barang_asal'] . ' - ' . $row['merk_asal'] . '</td>
            </tr>
            <tr>
                <td><strong>Ruangan</strong></td>
                <td>' . $row['ruangan_asal'] . '</td>
            </tr>
            <tr>
                <td><strong>Jumlah (Awal/Akhir)</strong></td>
                <td>' . $row['jumlah_awal'] . ' ' . $row['satuan_asal'] . ' / ' . $row['jumlah_akhir'] . ' ' . $row['satuan_asal'] . '</td>
            </tr>
            <tr>
                <td><strong>Sumber</strong></td>
                <td>' . $row['sumber_asal'] . '</td>
            </tr>
        </table>

        <h3>Inventaris yang Sudah Dipindahkan</h3>
        <table border="1" cellpadding="5">
            <tr style="background-color: #f2f2f2;">
                <td><strong>Kode Barang</strong></td>
                <td>' . $row['kode_inventaris_baru'] . '</td>
            </tr>
            <tr>
                <td><strong>Nama Barang & Merk</strong></td>
                <td>' . $row['nama_barang_baru'] . ' - ' . $row['merk_baru'] . '</td>
            </tr>
            <tr>
                <td><strong>Ruangan Tujuan</strong></td>
                <td>' . $row['ruangan_baru'] . '</td>
            </tr>
            <tr>
                <td><strong>Tanggal Pindah</strong></td>
                <td>' . $row['tanggal_perpindahan'] . '</td>
            </tr>
            <tr>
                <td><strong>Jumlah Pindah</strong></td>
                <td>' . $row['jumlah_perpindahan'] . ' ' . $row['satuan_baru'] . '</td>
            </tr>
            <tr>
                <td><strong>Cawu</strong></td>
                <td>' . $row['cawu'] . '</td>
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
                <td style="text-align: center;">' . $row['cawu'] . '</td>
            </tr>
        </table>';

        ob_end_clean();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Laporan_Perpindahan_Barang_' . $id_perpindahan_barang . '.pdf', 'I');
    } else {
        echo "Data perpindahan barang tidak ditemukan.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>