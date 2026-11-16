<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_data_diri.html');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$hobi_dipilih = $_POST['hobi'] ?? [];

if ($nama === '' || $tanggal_lahir === '') {
    die('Nama dan tanggal lahir wajib diisi. <a href="form_data_diri.html">&laquo; Kembali ke Form</a>');
}

$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$bagian = explode('-', $tanggal_lahir);

if (count($bagian) === 3 && checkdate((int)$bagian[1], (int)$bagian[2], (int)$bagian[0])) {
    $tanggal_tampil = (int)$bagian[2] . ' ' . $nama_bulan[(int)$bagian[1]] . ' ' . $bagian[0];
} else {
    die('Format tanggal tidak valid. <a href="form_data_diri.html">&laquo; Kembali ke Form</a>');
}

$daftar_hobi = ['Membaca', 'Olahraga', 'Musik', 'Lainnya'];
$hobi_valid = array_values(array_intersect($daftar_hobi, (array)$hobi_dipilih));

if (count($hobi_valid) > 0) {
    $hobi_tampil = implode(', ', $hobi_valid);
} else {
    $hobi_tampil = 'Tidak ada hobi yang dipilih';
}

$status_db = '';

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = @new mysqli('localhost', 'root', '', 'db_data_diri');

if (!$koneksi->connect_errno) {

    $stmt = $koneksi->prepare(
        'INSERT INTO data_diri (nama_lengkap, tanggal_lahir, hobi) VALUES (?, ?, ?)'
    );

    $hobi_simpan = count($hobi_valid) > 0 ? implode(', ', $hobi_valid) : null;

    $stmt->bind_param('sss', $nama, $tanggal_lahir, $hobi_simpan);

    $status_db = $stmt->execute() ? 'Data juga tersimpan ke database.' : '';

    $stmt->close();
    $koneksi->close();
}

function e($teks) {
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampilan Data Anda</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 40px 16px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #fff;
            padding: 28px 32px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin: 0 0 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #eee;
            font-size: 26px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 14px 0;
            vertical-align: top;
            font-size: 16px;
        }

        td.label {
            font-weight: bold;
            width: 38%;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        .back {
            margin-top: 20px;
        }

        .info {
            margin-top: 12px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>Tampilan Data Anda</h2>

        <table>
            <tr>
                <td class="label">Nama Lengkap:</td>
                <td><?= e($nama) ?></td>
            </tr>

            <tr>
                <td class="label">Tanggal Lahir:</td>
                <td><?= e($tanggal_tampil) ?></td>
            </tr>

            <tr>
                <td class="label">Hobi:</td>
                <td><?= e($hobi_tampil) ?></td>
            </tr>
        </table>

        <div class="back">
            <a href="form_data_diri.html">&laquo; Kembali ke Form</a>
        </div>

        <?php if ($status_db): ?>
            <div class="info">
                <?= e($status_db) ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
