import Swal from 'sweetalert2';

// Buat mixin konfigurasi bawaan
const defaultSwalConfig = Swal.mixin({
    // Opsi global untuk semua SweetAlert2
    allowOutsideClick: true, // Cegah klik di luar modal untuk menutup
    allowEscapeKey: true,    // Cegah tombol ESC untuk menutup
    allowEnterKey: true,      // Izinkan tombol Enter untuk konfirmasi (jika ada tombol konfirmasi)
    timerProgressBar: true,
    // Tambahkan konfigurasi CSS untuk mengizinkan seleksi teks secara global
    willOpen: (modal) => {
        // Cari elemen konten utama SweetAlert
        const htmlContainer = modal.querySelector('.swal2-html-container');
        if (htmlContainer) {
            htmlContainer.style.userSelect = 'text'; // Izinkan seleksi teks
        }
        // Jika ada elemen lain yang perlu diizinkan seleksinya, tambahkan di sini
        const title = modal.querySelector('.swal2-title');
        if (title) {
            title.style.userSelect = 'text';
        }
        const content = modal.querySelector('.swal2-content');
        if (content) {
            content.style.userSelect = 'text';
        }
        // dst.
    }
});

// Set objek global
window.Swal = defaultSwalConfig; // Gunakan mixin sebagai objek global

// Toast configuration (tetap sama)
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

window.Toast = Toast;

// SweetAlert2 helper functions
// Gunakan mixin default di sini juga
window.showAlert = function (config) {
    return defaultSwalConfig.fire(config); // Gunakan mixin
};

window.showSuccess = function (message, title = 'Sukses') {
    return defaultSwalConfig.fire({ // Gunakan mixin
        icon: 'success',
        title: title,
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
};

window.showError = function (message, title = 'Error') {
    return defaultSwalConfig.fire({ // Gunakan mixin
        icon: 'error',
        title: title,
        text: message,
        // timer: 6000
    });
};

window.showWarning = function (message, title = 'Peringatan') {
    return defaultSwalConfig.fire({ // Gunakan mixin
        icon: 'warning',
        title: title,
        text: message,
        timer: 6000
    });
};

window.showInfo = function (message, title = 'Informasi') {
    return defaultSwalConfig.fire({ // Gunakan mixin
        icon: 'info',
        title: title,
        text: message,
        // timer: 6000,
        showConfirmButton: true
    });
};

window.showConfirm = function (config) {
    const defaultConfig = {
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        // Sertakan opsi global di sini juga jika ingin di-override
        allowOutsideClick: true,
        allowEscapeKey: true,
    };

    return defaultSwalConfig.fire({ ...defaultConfig, ...config }); // Gunakan mixin
};

// Delete confirmation (Gunakan mixin)
window.showDeleteConfirmation = function (callback, itemName = 'data') {
    return defaultSwalConfig.fire({ // Gunakan mixin
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus (${itemName}). Data yang terhapus tidak dapat dikembalikan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonColor: '#3085d6',
        cancelButtonText: 'Batal',
        // Sertakan opsi global di sini juga jika ingin di-override
        allowOutsideClick: true,
        allowEscapeKey: true,
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
        return result;
    });
};