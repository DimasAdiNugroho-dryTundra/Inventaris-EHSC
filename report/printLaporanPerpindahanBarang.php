<?php
require_once('../lib/TCPDF/tcpdf.php');
require_once('../server/configDB.php');

// Ambil ID dari URL
$id_perpindahan_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data perpindahan barang berdasarkan ID
$query = "SELECT pb.*, i.nama_barang, i.kode_inventaris 
          FROM perpindahan_barang pb 
          JOIN inventaris i ON pb.id_inventaris = i.id_inventaris 
          WHERE pb.id_perpindahan_barang = $id_perpindahan_barang";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Buat objek TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Laporan Perpindahan Barang');
$pdf->SetHeaderData('', 0, 'Laporan Perpindahan Barang', 'Generated on: ' . date('Y-m-d H:i:s'));
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Tambahkan judul laporan
$html = '<h1 style="text-align:center;">Laporan Perpindahan Barang</h1>';
$html .= '<p style="text-align:justify;">Laporan ini dibuat untuk mencatat dan mendokumentasikan proses perpindahan barang yang telah dilaksanakan. Berdasarkan catatan yang tersedia, kegiatan perpindahan barang dilakukan pada tanggal <strong>' . date('d-m-Y', strtotime($row['tanggal_perpindahan'])) . '</strong>. Berikut adalah rincian lebih lanjut terkait barang yang dipindahkan:</p>';

// Tambahkan rincian barang dalam bentuk kalimat
$html .= '<p style="text-align:justify;">Barang yang dipindahkan memiliki kode inventaris <strong>' . $row['kode_inventaris'] . '</strong>, dengan nama barang <strong>"' . $row['nama_barang'] . '"</strong>. Perpindahan ini dilakukan untuk periode atau cawu <strong>' . $row['cawu'] . '</strong>. Jumlah barang yang dipindahkan sebanyak <strong>' . $row['jumlah_perpindahan'] . ' unit</strong>. Adapun informasi tambahan mengenai perpindahan ini adalah sebagai berikut:</p>';

// Tambahkan keterangan
$html .= '<p style="text-align:justify;"><em>"' . nl2br($row['keterangan']) . '"</em></p>';
$html .= '<p style="text-align:justify;">Kami mencatat bahwa proses perpindahan barang ini dilakukan sesuai dengan prosedur yang berlaku di lingkungan pengelolaan inventaris. Segala bentuk kendala yang mungkin muncul selama proses perpindahan telah diatasi dengan langkah-langkah yang sesuai untuk menjaga keutuhan dan kelancaran proses.</p>';

// Tambahkan penutup laporan
$html .= '<h3>Penutup</h3>';
$html .= '<p style="text-align:justify;">Demikian laporan ini disusun untuk mendokumentasikan perpindahan barang secara resmi. Diharapkan laporan ini dapat menjadi acuan dalam meningkatkan efisiensi dan ketertiban pengelolaan barang di masa mendatang. Kami mengucapkan terima kasih atas perhatian dan kerja sama yang telah diberikan.</p>';
$html .= '<p style="text-align:justify;">Banjarmasin, ' . date('d-m-Y') . '</p>';
$html .= '<p><strong>Penanggung Jawab,</strong></p>';
$html .= '<br><br><p>(.............................)</p>';

// Tulis konten ke PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('laporan_perpindahan_barang_' . $id_perpindahan_barang . '.pdf', 'I');
?>