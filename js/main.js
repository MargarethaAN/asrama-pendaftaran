// ==================== KONFIGURASI ====================
const BASE_URL = window.location.origin + '/asrama-pendaftaran/backend/api/';

// ==================== FUNGSI API ====================
async function apiCall(endpoint, method = 'POST', data = null, isFormData = false) {
    const url = BASE_URL + endpoint;
    const options = {
        method: method,
        headers: {}
    };
    
    if (isFormData) {
        options.body = data;
    } else {
        options.headers['Content-Type'] = 'application/json';
        if (data) {
            options.body = JSON.stringify(data);
        }
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return { success: false, message: 'Terjadi kesalahan koneksi' };
    }
}

// ==================== FUNGSI AUTH ====================
async function login(email, password, role) {
    const result = await apiCall('login.php', 'POST', { email, password, role });
    
    if (result.success) {
        if (result.role === 'admin') {
            window.location.href = 'admin-dashboard.html';
        } else {
            window.location.href = 'dashboard.html';
        }
    } else {
        showAlert(result.message, 'error');
    }
    return result;
}

async function logout() {
    await apiCall('logout.php', 'POST');
    window.location.href = 'login.html';
}

async function checkSession() {
    const result = await apiCall('check_session.php', 'GET');
    if (!result.logged_in && !window.location.pathname.includes('login.html') && !window.location.pathname.includes('index.html')) {
        window.location.href = 'login.html';
    }
    return result;
}

// ==================== FUNGSI PENDAFTARAN ====================
async function savePendaftaran(data) {
    return await apiCall('save_pendaftaran.php', 'POST', data);
}

async function getPendaftar() {
    return await apiCall('get_pendaftar.php', 'GET');
}

async function getKamar() {
    return await apiCall('get_kamar.php', 'GET');
}

async function updateKamar(id_kamar, status_kamar) {
    return await apiCall('update_kamar.php', 'POST', { id_kamar, status_kamar });
}

async function uploadDokumen(formData) {
    return await apiCall('upload_dokumen.php', 'POST', formData, true);
}

// ==================== FUNGSI ADMIN ====================
async function getPenghuni() {
    return await apiCall('get_penghuni.php', 'GET');
}

async function getPendaftaranList() {
    return await apiCall('get_pendaftaran_list.php', 'GET');
}

async function verifikasiPendaftaran(id_pendaftaran, status, catatan) {
    return await apiCall('verifikasi_pendaftaran.php', 'POST', { id_pendaftaran, status, catatan });
}

async function konfirmasiKeluar(id_pendaftar, keterangan) {
    return await apiCall('konfirmasi_keluar.php', 'POST', { id_pendaftar, keterangan });
}

// ==================== FUNGSI UI ====================
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.form-container') || document.querySelector('.main-content') || document.querySelector('.dashboard');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        setTimeout(() => alertDiv.remove(), 3000);
    } else {
        alert(message);
    }
    function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    
    // Warna berdasarkan tipe
    let bgColor = '#d1fae5';
    let textColor = '#065f46';
    let icon = '✅';
    
    if (type === 'error') {
        bgColor = '#fee2e2';
        textColor = '#991b1b';
        icon = '❌';
    } else if (type === 'warning') {
        bgColor = '#fef3c7';
        textColor = '#92400e';
        icon = '⚠️';
    } else if (type === 'info') {
        bgColor = '#dbeafe';
        textColor = '#1e40af';
        icon = 'ℹ️';
    }
    
    alertDiv.className = `alert alert-${type}`;
    alertDiv.style.background = bgColor;
    alertDiv.style.color = textColor;
    alertDiv.style.border = `1px solid ${type === 'success' ? '#a7f3d0' : (type === 'error' ? '#fecaca' : '#bfdbfe')}`;
    alertDiv.style.padding = '14px 18px';
    alertDiv.style.borderRadius = '14px';
    alertDiv.style.marginBottom = '20px';
    alertDiv.style.fontSize = '14px';
    alertDiv.innerHTML = `${icon} ${message}`;
    
    const container = document.querySelector('.form-container') || document.querySelector('.main-content') || document.querySelector('.dashboard');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        setTimeout(() => alertDiv.remove(), 3000);
    } else {
        alert(message);
    }
}
}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}

function formatTanggal(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

// ==================== EVENT LISTENERS ====================
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;
            login(email, password, role);
        });
    }
    
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logout();
        });
    }
    
    if (!window.location.pathname.includes('index.html') && !window.location.pathname.includes('login.html')) {
        checkSession();
    }
});

// ==================== DATA DEMO ====================
window.demoData = {
    pendaftar: {
        nama: 'Maria Angelina',
        nim: '23K10020',
        prodi: 'Teknik Informatika',
        status: 'menunggu_verifikasi'
    },
    kamar: [
        { nomor: '101', lantai: 1, status: 'tersedia' },
        { nomor: '102', lantai: 1, status: 'tersedia' },
        { nomor: '103', lantai: 1, status: 'terisi' },
        { nomor: '201', lantai: 2, status: 'tersedia' },
        { nomor: '202', lantai: 2, status: 'tersedia' },
        { nomor: '203', lantai: 2, status: 'perbaikan' }
    ],
    penghuni: [
        { nama: 'Brigitta', nim: '23K10001', kamar: '103', tanggal_masuk: '2024-01-15' },
        { nama: 'Cecilia', nim: '23K10002', kamar: '203', tanggal_masuk: '2024-08-20' },
        { nama: 'Dominique', nim: '23K10003', kamar: '303', tanggal_masuk: '2025-01-10' }
    ]
};