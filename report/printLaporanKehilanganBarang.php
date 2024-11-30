<?php
require_once('../lib/TCPDF/tcpdf.php');
require_once('../server/configDB.php');

// Ambil ID dari URL
$id_kehilangan_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data kehilangan barang berdasarkan ID
$query = "SELECT kb.*, i.nama_barang, i.kode_inventaris 
          FROM kehilangan_barang kb 
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
          WHERE kb.id_kehilangan_barang = $id_kehilangan_barang";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die('Data tidak ditemukan');
}

// Buat objek TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Laporan Kehilangan Barang');
$pdf->SetHeaderData('', 0, 'Laporan Kehilangan Barang', 'Dibuat pada: ' . date('d-m-Y'));
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Tambahkan konten laporan
$html = '
<h1 style="text-align:center;">Laporan Kehilangan Barang</h1>
<p>
Pada tanggal <strong>' . date('d-m-Y', strtotime($row['tanggal_kehilangan'])) . '</strong>, telah dilaporkan kehilangan barang dengan rincian sebagai berikut:
</p>
<p>
Barang yang hilang memiliki <strong>Kode Inventaris</strong> <em>' . $row['kode_inventaris'] . '</em> dan dikenal dengan nama <strong>' . $row['nama_barang'] . '</strong>. 
Kejadian kehilangan ini dilaporkan terjadi pada periode <strong>' . $row['cawu'] . '</strong>.
</p>
<p>
Sebanyak <strong>' . $row['jumlah_kehilangan'] . ' unit</strong> barang hilang, dan pelapor memberikan keterangan sebagai berikut:
</p>
<p>
<em>"' . nl2br($row['keterangan']) . '"</em>.
</p>
<p>
Kami berharap informasi ini dapat membantu dalam proses penyelesaian kasus kehilangan ini. Jika diperlukan, pihak terkait dapat menghubungi pelapor untuk informasi tambahan.
</p>
<p>
Demikian laporan ini dibuat untuk digunakan sebagaimana mestinya.
</p>
';

// Tulis konten ke PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('laporan_kehilangan_barang_' . $id_kehilangan_barang . '.pdf', 'I');
?>