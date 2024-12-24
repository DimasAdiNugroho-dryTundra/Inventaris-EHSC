<?php
ob_start();

require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../lib/phpqrcode/qrlib.php');
require('../lib/TCPDF/tcpdf.php');

$id_inventaris = $_GET['id'];

$query = "SELECT i.*, d.nama_departemen, k.nama_kategori, 
          CASE 
              WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
              ELSE i.nama_barang 
          END as nama_barang
          FROM inventaris i
          JOIN departemen d ON i.id_departemen = d.id_departemen
          JOIN kategori k ON i.id_kategori = k.id_kategori
          LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
          WHERE i.id_inventaris = '$id_inventaris'";

$result = $conn->query($query);

class MYPDF extends TCPDF
{
    public function Header()
    {
        $this->SetY(15);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'QR Code Inventaris', 0, true, 'C');
    }

    public function Footer()
    {
    }
}

$tempDir = __DIR__ . '/../temp/';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}

try {
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PT. Buana Karya Bhakti');
    $pdf->SetTitle('QR Codes Inventaris');

    $pdf->SetMargins(5, 35, 5);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(0);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(TRUE, 10);

    $pdf->AddPage();

    if ($result->num_rows > 0) {
        $inventaris = $result->fetch_assoc();

        $qrFile = $tempDir . 'qr_' . md5($inventaris['kode_inventaris']) . '.png';
        QRcode::png($inventaris['kode_inventaris'], $qrFile, QR_ECLEVEL_L, 10);

        $qrcode_base64 = base64_encode(file_get_contents($qrFile));
        $namaBarang = $inventaris['nama_barang'] . ' - ' . $inventaris['merk'];
        $totalCodes = $inventaris['jumlah_awal'];

        $html = '<style>
            table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                margin: 0;
                padding: 0;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            td {
                text-align: center;
                vertical-align: middle;
                height: 120px;
                width: 50%;
                padding: 15px;
            }
            .qr-cell {
                border-top: 1px dashed #000;
                border-right: 1px dashed #000;
                border-bottom: 1px dashed #000;
                border-left: 1px dashed #000;
            }
            .qr-info {
                font-size: 7.5pt;
                margin-top: 5px;
                line-height: 1.3;
            }
            .qr-wrapper {
                display: inline-block;
                padding: 5px;
            }
            .empty-cell {
                border: none;
            }
            img {
                display: block;
                margin: 0 auto;
            }
        </style>';

        $html .= '<table>';

        for ($i = 0; $i < $totalCodes; $i += 2) {
            $html .= '<tr>';
            
            // First QR code of the row
            $html .= '<td class="qr-cell">
                <div class="qr-wrapper">
                    <img src="@' . $qrcode_base64 . '" width="65" height="65">
                    <div class="qr-info">
                        ' . $namaBarang . '<br>
                        ' . $inventaris['nama_departemen'] . '<br>
                        ' . $inventaris['kode_inventaris'] . '
                    </div>
                </div>
            </td>';
            
            // Second QR code of the row (if exists)
            if ($i + 1 < $totalCodes) {
                $html .= '<td class="qr-cell">
                    <div class="qr-wrapper">
                        <img src="@' . $qrcode_base64 . '" width="65" height="65">
                        <div class="qr-info">
                            ' . $namaBarang . '<br>
                            ' . $inventaris['nama_departemen'] . '<br>
                            ' . $inventaris['kode_inventaris'] . '
                        </div>
                    </div>
                </td>';
            } else {
                $html .= '<td class="empty-cell"></td>';
            }
            
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');

    } else {
        $pdf->Cell(0, 10, 'Data tidak ditemukan', 0, 1, 'C');
    }

    if (file_exists($qrFile)) {
        unlink($qrFile);
    }

    ob_end_clean();
    $pdf->Output('QRCodes_' . $id_inventaris . '.pdf', 'I');

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>