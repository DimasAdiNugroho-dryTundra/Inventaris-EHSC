<?php
require_once('../lib/TCPDF/tcpdf.php');
require_once('../server/configDB.php');

// Ambil ID dari URL
$id_kerusakan_barang = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data kerusakan barang berdasarkan ID
$query = "SELECT kb.*, i.nama_barang, i.kode_inventaris 
          FROM kerusakan_barang kb 
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
          WHERE kb.id_kerusakan_barang = $id_kerusakan_barang";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Buat objek TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Laporan Kerusakan Barang');
$pdf->SetHeaderData('', 0, 'Laporan Kerusakan Barang', 'Generated on: ' . date('Y-m-d H:i:s'));
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Tambahkan konten laporan
$html = '<h1 style="text-align:center;">Laporan Kerusakan Barang</h1>';
$html .= '<p>Pada tanggal <strong>' . date('d-m-Y', strtotime($row['tanggal_kerusakan'])) . '</strong>, telah dilaporkan kerusakan pada barang dengan kode inventaris <strong>' . $row['kode_inventaris'] . '</strong>. Barang yang dimaksud adalah <strong>' . $row['nama_barang'] . '</strong>, yang termasuk dalam catatan inventaris kami.</p>';
$html .= '<p>Berdasarkan laporan yang diterima, kerusakan terjadi pada <strong>' . $row['cawu'] . '</strong> dengan jumlah kerusakan sebanyak <strong>' . $row['jumlah_kerusakan'] . '</strong> unit. Kerusakan tersebut telah didokumentasikan dan dilaporkan untuk ditindaklanjuti sesuai prosedur yang berlaku.</p>';
$html .= '<p>Keterangan tambahan dari pelapor terkait kerusakan ini adalah sebagai berikut: ' . nl2br($row['keterangan']) . '</p>';
$html .= '<p>Demikian laporan kerusakan barang ini dibuat sebagai dokumentasi resmi. Terima kasih atas perhatian dan kerja samanya dalam menindaklanjuti laporan ini.</p>';

// Tulis konten HTML ke PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Tambahkan tanda tangan di halaman pertama
$html = '<br><br><br>
         <table style="width:100%; text-align:center;">
            <tr>
                <td><strong>Pelapor</strong></td>
                <td><strong>Disetujui Oleh</strong></td>
            </tr>
            <tr>
                <td style="height:100px;">(Nama Pelapor)</td>
                <td style="height:100px;">(Nama Penanggung Jawab)</td>
            </tr>
         </table>';
$pdf->writeHTML($html, true, false, true, false, '');

// Tambahkan halaman baru untuk gambar
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Lampiran: Foto Kerusakan', 0, 1, 'C');

// Cek dan tampilkan gambar
if ($row['foto_kerusakan']) {
    $fotoPath = '../upload/kerusakan/' . $row['foto_kerusakan'];
    if (file_exists($fotoPath)) {
        // Dapatkan dimensi asli gambar
        list($width, $height) = getimagesize($fotoPath);

        // Hitung skala untuk menyesuaikan dimensi di halaman PDF
        $maxWidth = 150; // Lebar maksimum di PDF
        $maxHeight = 200; // Tinggi maksimum di PDF

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = $width * $ratio;
        $newHeight = $height * $ratio;

        // Menampilkan gambar di PDF dengan ukuran yang disesuaikan
        $pdf->Image($fotoPath, '30', '', $newWidth, $newHeight, '', '', '', false, 300, '', false, false, 1, false, false, false);
    } else {
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Tidak ada foto (file tidak ditemukan)', 0, 1, 'C');
    }
} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Tidak ada foto', 0, 1, 'C');
}

// Output PDF
$pdf->Output('laporan_kerusakan_barang_' . $id_kerusakan_barang . '.pdf', 'I');
?>