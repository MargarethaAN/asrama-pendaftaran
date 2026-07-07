<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();
require_once '../config/database.php';

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// ============ QUERY DIPERBAIKI ============
// Ambil data dengan JOIN ke tabel kamar
$query = "SELECT 
            pf.id_pendaftar,
            pf.nama_lengkap,
            pf.nim,
            pf.prodi,
            pf.no_hp,
            pf.alamat_asal,
            pf.alamat_semarang,
            pf.agama,
            pf.tanggal_masuk_asrama,
            pf.tanggal_keluar_asrama,
            pf.status_keluar,
            pf.akun_diblokir,
            pd.id_pendaftaran,
            pd.status_pendaftaran,
            pd.tanggal_daftar,
            pd.verifikasi_pada,
            pd.catatan_revisi,
            h.id_hunian,
            h.tanggal_masuk as tanggal_masuk_hunian,
            k.nomor_kamar,
            k.lantai,
            p.id_pembayaran,
            p.status_pembayaran,
            p.bukti_pembayaran,
            p.jumlah_tagihan,
            p.nomor_va,
            p.tanggal_bayar
          FROM pendaftar pf
          LEFT JOIN pendaftaran pd ON pf.id_pendaftar = pd.id_pendaftar
          LEFT JOIN hunian h ON pd.id_pendaftaran = h.id_pendaftaran
          LEFT JOIN kamar k ON h.id_kamar = k.id_kamar
          LEFT JOIN pembayaran p ON pf.id_pendaftar = p.id_pendaftar
          WHERE pf.id_pengguna = $id_pengguna
          ORDER BY pd.id_pendaftaran DESC LIMIT 1";

$result = mysqli_query($conn, $query);

if (!$result) {
    $error = mysqli_error($conn);
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $error]);
    exit;
}

$data = mysqli_fetch_assoc($result);

if ($data) {
    // Hitung dokumen
    $dokumenQuery = "SELECT COUNT(*) as total FROM dokumen WHERE id_pendaftar = " . $data['id_pendaftar'];
    $dokumenResult = mysqli_query($conn, $dokumenQuery);
    
    if (!$dokumenResult) {
        echo json_encode(['success' => false, 'message' => 'Dokumen query error: ' . mysqli_error($conn)]);
        exit;
    }
    
    $dokumenCount = mysqli_fetch_assoc($dokumenResult);
    
    $data['is_dokumen_lengkap'] = ($dokumenCount['total'] >= 4);
    $data['is_kamar_terpilih'] = !empty($data['nomor_kamar']);
    $data['is_pembayaran_lunas'] = ($data['status_pembayaran'] === 'lunas');
    $data['is_data_diri_lengkap'] = !empty($data['nama_lengkap']);
    
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
}

mysqli_close($conn);
?>