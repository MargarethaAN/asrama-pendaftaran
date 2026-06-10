<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login terlebih dahulu']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Data tidak diterima']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// Escape semua data
$nim = mysqli_real_escape_string($conn, $data['nim'] ?? '');
$nama_lengkap = mysqli_real_escape_string($conn, $data['nama_lengkap'] ?? '');
$nama_panggilan = mysqli_real_escape_string($conn, $data['nama_panggilan'] ?? '');
$prodi = mysqli_real_escape_string($conn, $data['prodi'] ?? '');
$fakultas = mysqli_real_escape_string($conn, $data['fakultas'] ?? '');
$no_hp = mysqli_real_escape_string($conn, $data['no_hp'] ?? '');
$alamat_asal = mysqli_real_escape_string($conn, $data['alamat_asal'] ?? '');
$alamat_semarang = mysqli_real_escape_string($conn, $data['alamat_semarang'] ?? '');
$agama = mysqli_real_escape_string($conn, $data['agama'] ?? '');
$asal_sekolah = mysqli_real_escape_string($conn, $data['asal_sekolah'] ?? '');
$tempat_lahir = mysqli_real_escape_string($conn, $data['tempat_lahir'] ?? '');
$tanggal_lahir = mysqli_real_escape_string($conn, $data['tanggal_lahir'] ?? '');
$sifat_positif = mysqli_real_escape_string($conn, $data['sifat_positif'] ?? '');
$sifat_negatif = mysqli_real_escape_string($conn, $data['sifat_negatif'] ?? '');
$bakat = mysqli_real_escape_string($conn, $data['bakat'] ?? '');
$alasan_masuk = mysqli_real_escape_string($conn, $data['alasan_masuk'] ?? '');

// Data keluarga
$nama_ayah = mysqli_real_escape_string($conn, $data['nama_ayah'] ?? '');
$pekerjaan_ayah = mysqli_real_escape_string($conn, $data['pekerjaan_ayah'] ?? '');
$no_hp_ayah = mysqli_real_escape_string($conn, $data['no_hp_ayah'] ?? '');
$nama_ibu = mysqli_real_escape_string($conn, $data['nama_ibu'] ?? '');
$pekerjaan_ibu = mysqli_real_escape_string($conn, $data['pekerjaan_ibu'] ?? '');
$no_hp_ibu = mysqli_real_escape_string($conn, $data['no_hp_ibu'] ?? '');
$jumlah_saudara = intval($data['jumlah_saudara'] ?? 0);

// Cek apakah pendaftar sudah ada
$check = "SELECT id_pendaftar FROM pendaftar WHERE id_pengguna = $id_pengguna";
$checkResult = mysqli_query($conn, $check);

if (mysqli_num_rows($checkResult) > 0) {
    // UPDATE - JANGAN UPDATE NIM
    $row = mysqli_fetch_assoc($checkResult);
    $id_pendaftar = $row['id_pendaftar'];
    
    $query = "UPDATE pendaftar SET 
              nama_lengkap = '$nama_lengkap',
              nama_panggilan = '$nama_panggilan',
              prodi = '$prodi',
              fakultas = '$fakultas',
              no_hp = '$no_hp',
              alamat_asal = '$alamat_asal',
              alamat_semarang = '$alamat_semarang',
              agama = '$agama',
              asal_sekolah = '$asal_sekolah',
              tempat_lahir = '$tempat_lahir',
              tanggal_lahir = '$tanggal_lahir',
              sifat_positif = '$sifat_positif',
              sifat_negatif = '$sifat_negatif',
              bakat = '$bakat',
              alasan_masuk_asrama = '$alasan_masuk'
              WHERE id_pendaftar = $id_pendaftar";
    
    if (mysqli_query($conn, $query)) {
        // Update keluarga
        $checkKeluarga = "SELECT id_keluarga FROM data_keluarga WHERE id_pendaftar = $id_pendaftar";
        $resultKeluarga = mysqli_query($conn, $checkKeluarga);
        
        if (mysqli_num_rows($resultKeluarga) > 0) {
            $updateKeluarga = "UPDATE data_keluarga SET 
                               nama_ayah = '$nama_ayah',
                               pekerjaan_ayah = '$pekerjaan_ayah',
                               no_hp_ayah = '$no_hp_ayah',
                               nama_ibu = '$nama_ibu',
                               pekerjaan_ibu = '$pekerjaan_ibu',
                               no_hp_ibu = '$no_hp_ibu',
                               jumlah_saudara = $jumlah_saudara
                               WHERE id_pendaftar = $id_pendaftar";
            mysqli_query($conn, $updateKeluarga);
        } else {
            $insertKeluarga = "INSERT INTO data_keluarga (id_pendaftar, nama_ayah, pekerjaan_ayah, no_hp_ayah, nama_ibu, pekerjaan_ibu, no_hp_ibu, jumlah_saudara) 
                               VALUES ($id_pendaftar, '$nama_ayah', '$pekerjaan_ayah', '$no_hp_ayah', '$nama_ibu', '$pekerjaan_ibu', '$no_hp_ibu', $jumlah_saudara)";
            mysqli_query($conn, $insertKeluarga);
        }
        
        echo json_encode(['success' => true, 'message' => 'Data berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update: ' . mysqli_error($conn)]);
    }
} else {
    // INSERT baru - cek duplikat NIM
    $checkNim = "SELECT id_pendaftar FROM pendaftar WHERE nim = '$nim'";
    $resultNim = mysqli_query($conn, $checkNim);
    
    if (mysqli_num_rows($resultNim) > 0) {
        echo json_encode(['success' => false, 'message' => 'NIM sudah terdaftar!']);
        exit;
    }
    
    $query = "INSERT INTO pendaftar (
                id_pengguna, nim, nama_lengkap, nama_panggilan, prodi, fakultas, 
                no_hp, alamat_asal, alamat_semarang, agama, asal_sekolah, 
                tempat_lahir, tanggal_lahir, sifat_positif, sifat_negatif, 
                bakat, alasan_masuk_asrama
              ) VALUES (
                $id_pengguna, '$nim', '$nama_lengkap', '$nama_panggilan', '$prodi', '$fakultas',
                '$no_hp', '$alamat_asal', '$alamat_semarang', '$agama', '$asal_sekolah',
                '$tempat_lahir', '$tanggal_lahir', '$sifat_positif', '$sifat_negatif',
                '$bakat', '$alasan_masuk'
              )";
    
    if (mysqli_query($conn, $query)) {
        $id_pendaftar = mysqli_insert_id($conn);
        
        $insertKeluarga = "INSERT INTO data_keluarga (id_pendaftar, nama_ayah, pekerjaan_ayah, no_hp_ayah, nama_ibu, pekerjaan_ibu, no_hp_ibu, jumlah_saudara) 
                           VALUES ($id_pendaftar, '$nama_ayah', '$pekerjaan_ayah', '$no_hp_ayah', '$nama_ibu', '$pekerjaan_ibu', '$no_hp_ibu', $jumlah_saudara)";
        mysqli_query($conn, $insertKeluarga);
        
        $insertPendaftaran = "INSERT INTO pendaftaran (id_pendaftar, tanggal_daftar, status_pendaftaran) 
                               VALUES ($id_pendaftar, CURDATE(), 'menunggu_verifikasi')";
        mysqli_query($conn, $insertPendaftaran);
        
        echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal insert: ' . mysqli_error($conn)]);
    }
}
?>