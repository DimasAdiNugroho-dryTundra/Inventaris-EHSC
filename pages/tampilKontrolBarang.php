<div class="card">
    <h4 class="card-header d-flex justify-content-between align-items-center">
        Data Kontrol Barang
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKontrolModal">
            Tambah Kontrol
        </button>
    </h4>
    <div class="row p-3">
        <!-- Search, Cawu and Year Dropdown -->
        <div class="col-md-6">
            <form method="POST" class="d-flex">
                <div class="flex-grow-1 me-2">
                    <label for="search" class="form-label">Cari kode inventaris</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan kode..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                <input type="hidden" name="year" value="<?php echo $year; ?>">
                <div class="d-flex align-items-end">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
            </form>
        </div>
        <!-- Form limit -->
        <div class="col-md-6">
            <form class="flex-grow-1 me-2">
                <label for="limit" class="form-label">Tampilkan</label>
                <select id="limit" class="select2 form-select" onchange="changeLimit(this.value);">
                    <option value="5" <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                    <option value="10" <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                    <option value="20" <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                </select>
            </form>
        </div>
    </div>
    <!-- Tampilkan informasi cawu dan tahun yang dipilih -->
    <div class="row p-3">
        <div class="col-md-12">
            <div class="alert alert-secondary" role="alert">
                Data yang tampil adalah Cawu <?php echo $cawu; ?> untuk tahun <?php echo $year; ?>.
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive text-nowrap">
        <table class="table table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Kode Inventaris</th>
                    <th>Nama Barang</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        $no = $offset + 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $statusText = '';
                                            switch ($row['status_kontrol']) {
                                                case 1:
                                                    $statusText = '<span class="badge bg-success">Baik</span>';
                                                    break;
                                                case 2:
                                                    $statusText = '<span class="badge bg-warning">Pindah</span>';
                                                    break;
                                                case 3:
                                                    $statusText = '<span class="badge bg-danger">Rusak</span>';
                                                    break;
                                                case 4:
                                                    $statusText = '<span class="badge bg-dark">Hilang</span>';
                                                    break;
                                            }
                                    ?>
                <tr>
                    <td class="align-middle"><?php echo $no++; ?></td>
                    <td class="align-middle"><?php echo $row['kode_inventaris']; ?></td>
                    <td class="align-middle"><?php echo $row['nama_barang']; ?></td>
                    <td class="align-middle">
                        <?php echo date('d/m/Y', strtotime($row['tanggal_kontrol'])); ?></td>
                    <td class="align-middle"><?php echo $statusText; ?></td>
                    <td class="align-middle"><?php echo $row['jumlah_kontrol']; ?></td>
                    <td class="align-middle"><?php echo $row['keterangan']; ?></td>
                    <td class="align-middle">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal<?php echo $row[$cawuIdField]; ?>">
                                Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal<?php echo $row[$cawuIdField]; ?>">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?php echo $row[$cawuIdField]; ?>" tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Kontrol Barang</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="kontrolBarang.php">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id_kontrol" value="<?php echo $row[$cawuIdField]; ?>">
                                    <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                                    <input type="hidden" name="id_inventaris"
                                        value="<?php echo $row['id_inventaris']; ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control"
                                            value="<?php echo date('Y-m-d', strtotime($row['tanggal_kontrol'])); ?>"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="1"
                                                <?php echo ($row['status_kontrol'] == 1) ? 'selected' : ''; ?>>
                                                Baik</option>
                                            <option value="2"
                                                <?php echo ($row['status_kontrol'] == 2) ? 'selected' : ''; ?>>
                                                Pindah</option>
                                            <option value="3"
                                                <?php echo ($row['status_kontrol'] == 3) ? 'selected' : ''; ?>>
                                                Rusak</option>
                                            <option value="4"
                                                <?php echo ($row['status_kontrol'] == 4) ? 'selected' : ''; ?>>
                                                Hilang</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" name="jumlah" class="form-control"
                                            value="<?php echo $row['jumlah_kontrol']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="3"
                                            required><?php echo $row['keterangan']; ?></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Delete -->
                <div class="modal fade" id="deleteModal<?php echo $row[$cawuIdField]; ?>" tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus data kontrol barang ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <a href="kontrolBarang.php?delete=<?php echo $row[$cawuIdField]; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"
                                    class="btn btn-danger">Hapus</a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                                            } 
                                        } else { 
                                        ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data kontrol barang untuk
                        Cawu
                        <?php echo $cawu; ?> tahun <?php echo $year; ?>.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination pagination-rounded justify-content-center">
            <?php if ($page > 1) { ?>
            <li class="page-item">
                <a class="page-link"
                    href="?page=<?php echo ($page - 1); ?>&limit=<?php echo $limit; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"
                    aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php } ?>

            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link"
                    href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"><?php echo $i; ?></a>
            </li>
            <?php } ?>

            <?php if ($page < $totalPages) { ?>
            <li class="page-item">
                <a class="page-link"
                    href="?page=<?php echo ($page + 1); ?>&limit=<?php echo $limit; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"
                    aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
</div>

<!-- Modal Tambah Kontrol -->
<div class="modal fade" id="tambahKontrolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kontrol Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="kontrolBarang.php" id="tambahKontrolForm">
                    <input type="hidden" name="tambahKontrol" value="1">
                    <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                    <input type="hidden" name="year" value="<?php echo $year; ?>">

                    <div class="mb-3">
                        <label class="form-label">Inventaris</label>
                        <select name="id_inventaris" class="form-select" required>
                            <option value="">Pilih Barang</option>
                            <?php
                            $invResult = getAvailableInventaris($conn, $year);
                            while ($inv = mysqli_fetch_assoc($invResult)) {
                echo "<option value='" . $inv['id_inventaris'] . "' data-stock='" . ($inv['jumlah'] - $inv['jumlah_terkontrol']) . "'>"
                    . $inv['kode_inventaris'] . " - "
                    . $inv['nama_barang'] . " (Total: " . $inv['jumlah'] . ", Belum terkontrol: " . ($inv['jumlah'] - $inv['jumlah_terkontrol']) . " " . $inv['satuan'] . ")</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label><br>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="statusBaik" name="status[]" value="1"
                                onclick="toggleInput(this)">
                            <label class="form-check-label" for="statusBaik">Baik</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="statusPindah" name="status[]" value="2"
                                onclick="toggleInput(this)">
                            <label class="form-check-label" for="statusPindah">Pindah</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="statusRusak" name="status[]" value="3"
                                onclick="toggleInput(this)">
                            <label class="form-check-label" for="statusRusak">Rusak</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="statusHilang" name="status[]" value="4"
                                onclick="toggleInput(this)">
                            <label class="form-check-label" for="statusHilang">Hilang</label>
                        </div>
                    </div>

                    <div id="inputContainer" style="display: none;">
                        <div class="status-input" id="inputBaik" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Baik</label>
                                <input type="number" name="jumlah_baik" class="form-control" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan Baik</label>
                                <textarea name="keterangan_baik" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="status-input" id="inputPindah" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Pindah</label>
                                <input type="number" name="jumlah_pindah" class="form-control" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan Pindah</label>
                                <textarea name="keterangan_pindah" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="status-input" id="inputRusak" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Rusak</label>
                                <input type="number" name="jumlah_rusak" class="form-control" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan Rusak</label>
                                <textarea name="keterangan_rusak" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="status-input" id="inputHilang" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Hilang</label>
                                <input type="number" name="jumlah_hilang" class="form-control" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan Hilang</label>
                                <textarea name="keterangan_hilang" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleInput(checkbox) {
    const inputContainer = document.getElementById('inputContainer');
    const inputBaik = document.getElementById('inputBaik');
    const inputPindah = document.getElementById('inputPindah');
    const inputRusak = document.getElementById('inputRusak');
    const inputHilang = document.getElementById('inputHilang');

    inputContainer.style.display = 'block'; // Tampilkan container input

    // Sembunyikan semua input
    inputBaik.style.display = 'none';
    inputPindah.style.display = 'none';
    inputRusak.style.display = 'none';
    inputHilang.style.display = 'none';

    // Tampilkan input sesuai dengan checkbox yang dicentang
    if (document.getElementById('statusBaik').checked) {
        inputBaik.style.display = 'block';
    }
    if (document.getElementById('statusPindah').checked) {
        inputPindah.style.display = 'block';
    }
    if (document.getElementById('statusRusak').checked) {
        inputRusak.style.display = 'block';
    }
    if (document.getElementById('statusHilang').checked) {
        inputHilang.style.display = 'block';
    }

    // Jika tidak ada checkbox yang dicentang, sembunyikan container input
    if (!document.getElementById('statusBaik').checked &&
        !document.getElementById('statusPindah').checked &&
        !document.getElementById('statusRusak').checked &&
        !document.getElementById('statusHilang').checked) {
        inputContainer.style.display = 'none';
    }
}

function changeLimit(value) {
    // Ambil parameter lain yang sedang aktif
    var page = "<?php echo $page; ?>";
    var cawu = "<?php echo $cawu; ?>";
    var year = "<?php echo $year; ?>";

    // Buat URL baru dengan parameter limit yang baru
    var newUrl = "kontrolBarang.php?limit=" + value + "&page=" + page + "&cawu=" + cawu + "&year=" + year;
    window.location.href = newUrl;
}
</script>