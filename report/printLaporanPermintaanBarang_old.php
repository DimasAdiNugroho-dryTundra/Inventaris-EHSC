<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../lib/TCPDF/tcpdf.php');

// Ambil data permintaan barang
$id_permintaan = $_GET['id'];
$query = "SELECT pb.*, d.nama_departemen 
          FROM permintaan_barang pb
          JOIN departemen d ON pb.id_departemen = d.id_departemen
          WHERE pb.id_permintaan = '$id_permintaan'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Buat objek PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nama Anda');
$pdf->SetTitle('Laporan Permintaan Barang');
$pdf->SetHeaderData('', 0, 'LAPORAN PERMINTAAN BARANG', 'Tanggal: ' . date('d-m-Y'));
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(10, 30, 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Kata pembuka
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTMLCell(0, 0, '', '', 'Dengan hormat,<br>Laporan ini merinci jenis dan jumlah barang yang diperlukan, diharapkan dapat membantu dalam pengadaan yang efisien dan tepat waktu. Kami menghargai perhatian dan kerjasama dari pihak terkait dalam proses ini.', 0, 1, 0, true, '', true);
$pdf->Ln(5);

// Tabel Permintaan Barang
$pdf->SetFont('helvetica', 'B', 12);

$html = <<<EOT
<table cellpadding="5" cellspacing="0" border="1">
    <tr>
        <th width="40%">Nama Barang</th>
        <th width="20%">Spesifikasi</th>
        <th width="10%">Jumlah Kebutuhan</th>
        <th width="10%">Harga</th>
        <th width="20%">Total Harga</th>
    </tr>
EOT;

$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $total_harga = $row['kebutuhan_qty'] * $row['harga_satuan'];
    $html .= "<tr>
        <td align='center'>{$row['nama_barang']}</td>
        <td align='center'>{$row['spesifikasi']}</td>
        <td align='center'>{$row['kebutuhan_qty']}</td>
        <td align='center'>" . number_format($row['harga_satuan'], 2) . "</td>
        <td align='center'>" . number_format($total_harga, 2) . "</td>
    </tr>";
}

$html .= "</table>";
$pdf->writeHTML($html, true, false, true, false, '');

// Tanda tangan
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTMLCell(100, 10, '', '', 'Admin Gudang', 0, 0, 0, true, 'L', true);
$pdf->writeHTMLCell(100, 10, '', '', 'Admin ', 0, 1, 0, true, 'R', true);

// Set header untuk menampilkan PDF di tab baru
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="laporan_permintaan.pdf"');

$pdf->Output('laporan_permintaan.pdf', 'I');
?>