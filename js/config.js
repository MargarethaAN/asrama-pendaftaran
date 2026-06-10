// Konfigurasi Base URL
const BASE_URL = window.location.origin + '/asrama-pendaftaran/backend/api/';

// Fungsi API dengan base URL otomatis
async function callAPI(endpoint, method = 'POST', data = null) {
    const url = BASE_URL + endpoint;
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return { success: false, message: 'Koneksi gagal. Pastikan server berjalan.' };
    }
}