<?php
require('server/auth.php');
require('layouts/headerLogin.php');
?>

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
            <!-- Login -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <?php require('layouts/logoLogin.php'); ?>
                    <!-- /Logo -->
                    <h4 class="mb-1">Selamat Datang ;)</h4>
                    <p class="mb-6">Silahkan Masuk</p>

                    <?php if (isset($error_message) && !empty($error_message)): ?>
                    <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                            <i class="ti ti-ban"></i>
                        </span>
                        <?php echo $error_message; ?>
                    </div>
                    <?php endif; ?>

                    <form id="formAuthentication" class="mb-4" method="POST">
                        <div class="mb-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Masukan username anda" autofocus required />
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" required />
                                <span class="input-group-text cursor-pointer toggle-password">
                                    <i class="ti ti-eye-off"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-6">
                            <button class="btn btn-primary d-grid w-100" type="submit" name="login">Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>

<script src="../../assets/vendor/libs/jquery/jquery.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');
    const icon = toggleButton.querySelector('i');
    toggleButton.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        }
    });
});

// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'username': 'Kolom username wajib diisi!',
        'password': 'Kolom password wajib diisi!'
    };

    return pesanKhusus[labelText] || `Mohon masukkan ${labelText}`;
}

// Fungsi untuk menghapus pesan error
function hapusPesanError(element) {
    element.addEventListener('input', function() {
        this.setCustomValidity('');
    });
}

// Fungsi untuk menerapkan validasi
function terapkanValidasi() {
    const elemenWajib = document.querySelectorAll('input[required]');

    elemenWajib.forEach(elemen => {
        // Atur pesan error kustom
        elemen.oninvalid = function(e) {
            if (e.target.validity.valueMissing) {
                const labelElemen = elemen.previousElementSibling;
                const labelTeks = labelElemen ? labelElemen.textContent : '';
                const jenisInput = elemen.tagName.toLowerCase();

                e.target.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
            }
        };

        // Hapus pesan error saat mulai diisi
        hapusPesanError(elemen);
    });
}

// Event listener untuk validasi saat form login disubmit
document.getElementById('formAuthentication').addEventListener('submit', function(event) {
    const elemenWajib = this.querySelectorAll('input[required]');

    elemenWajib.forEach(elemen => {
        if (elemen.validity.valueMissing) {
            const labelElemen = elemen.previousElementSibling;
            const labelTeks = labelElemen ? labelElemen.textContent : '';
            const jenisInput = elemen.tagName.toLowerCase();
            elemen.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
        } else {
            elemen.setCustomValidity('');
        }
    });

    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
}, false);

// Terapkan validasi saat dokumen dimuat
document.addEventListener('DOMContentLoaded', function() {
    terapkanValidasi();
});
</script>

<!-- Core JSJS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="../../assets/vendor/libs/popper/popper.js"></script>
<script src="../../assets/vendor/js/bootstrap.js"></script>
<script src="../../assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../../assets/vendor/libs/hammer/hammer.js"></script>
<script src="../../assets/vendor/libs/i18n/i18n.js"></script>
<script src="../../assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="../../assets/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
<script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
<script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>

<!-- Main JS -->
<script src="../../assets/js/main.js"></script>

<!-- Page JS -->
<script src="../../assets/js/pages-auth.js"></script>