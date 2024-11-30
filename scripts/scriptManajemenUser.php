<script>
function validateAddForm() {
    const form = document.getElementById('formAdd');

    // Ambil nilai input
    const nama = form.nama.value.trim();
    const username = form.username.value.trim();
    const password = form.password.value;
    const email = form.email.value.trim();
    const jabatan = form.jabatan.value;
    const hakAkses = form.hak_akses.value;
    const foto = form.foto.value;

    // Reset alert
    const alerts = ['alertNama', 'alertUsername', 'alertPassword', 'alertEmail', 'alertJabatan', 'alertHakAkses',
        'alertFoto'
    ];
    alerts.forEach(alertId => {
        document.getElementById(alertId).classList.add('d-none');
    });

    let isValid = true;

    // Validasi field kosong
    if (!nama) {
        document.getElementById('alertNama').textContent = 'Kolom nama wajib diisi!';
        document.getElementById('alertNama').classList.remove('d-none');
        form.nama.focus();
        isValid = false;
    }
    if (!username) {
        document.getElementById('alertUsername').textContent = 'Kolom username wajib diisi!';
        document.getElementById('alertUsername').classList.remove('d-none');
        form.username.focus();
        isValid = false;
    }
    if (!password) {
        document.getElementById('alertPassword').textContent = 'Kolom password wajib diisi!';
        document.getElementById('alertPassword').classList.remove('d-none');
        form.password.focus();
        isValid = false;
    }
    if (!email) {
        document.getElementById('alertEmail').textContent = 'Kolom email wajib diisi!';
        document.getElementById('alertEmail').classList.remove('d-none');
        form.email.focus();
        isValid = false;
    }
    if (!jabatan) {
        document.getElementById('alertJabatan').textContent = 'Silakan pilih jabatan!';
        document.getElementById('alertJabatan').classList.remove('d-none');
        form.jabatan.focus();
        isValid = false;
    }
    if (!hakAkses) {
        document.getElementById('alertHakAkses').textContent = 'Silakan pilih hak akses!';
        document.getElementById('alertHakAkses').classList.remove('d-none');
        form.hak_akses.focus();
        isValid = false;
    }
    if (!foto) {
        document.getElementById('alertFoto').textContent = 'Silakan unggah foto!';
        document.getElementById('alertFoto').classList.remove('d-none');
        form.foto.focus();
        isValid = false;
    }

    // Validasi email
    const emailPattern = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
    if (email && !emailPattern.test(email)) {
        document.getElementById('alertEmail').textContent = 'Format email tidak valid!';
        document.getElementById('alertEmail').classList.remove('d-none');
        form.email.focus();
        isValid = false;
    }

    // Validasi password minimal 6 karakter
    if (password && password.length < 6) {
        document.getElementById('alertPassword').textContent = 'Password minimal 6 karakter!';
        document.getElementById('alertPassword').classList.remove('d-none');
        form.password.focus();
        isValid = false;
    }

    // Submit form jika semua validasi berhasil
    if (isValid) {
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'add';
        form.appendChild(actionInput);
        form.submit();
    }
}

function validateAddForm() {
    const form = document.getElementById('formAdd');
    const alert = document.getElementById('alertAdd');
    const alertMessage = document.getElementById('alertAddMessage');

    // Reset alert
    alert.classList.add('d-none');

    // Ambil nilai input
    const nama = form.nama.value.trim();
    const username = form.username.value.trim();
    const password = form.password.value;
    const email = form.email.value.trim();
    const jabatan = form.jabatan.value;
    const hakAkses = form.hak_akses.value;
    const foto = form.foto.value;

    // Validasi field kosong
    if (!nama || !username || !password || !email || !jabatan || !hakAkses || !foto) {
        alert.classList.remove('d-none');
        alertMessage.textContent = 'Semua field harus diisi!';
        return false;
    }

    // Validasi email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert.classList.remove('d-none');
        alertMessage.textContent = 'Format email tidak valid!';
        return false;
    }

    // Validasi password minimal 6 karakter
    if (password.length < 6) {
        alert.classList.remove('d-none');
        alertMessage.textContent = 'Password minimal 6 karakter!';
        return false;
    }

    // Tambahkan hidden input untuk action
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'add';
    form.appendChild(actionInput);

    // Submit form jika semua validasi berhasil
    form.submit();
}
</script>