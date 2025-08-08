<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "todo_list";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"] ?? null;
    $nama = $_POST["nama_tugas"];
    $deskripsi = $_POST["deskripsi"];
    $prioritas = $_POST["prioritas"];
    $deadline = $_POST["tanggal_deadline"];

    if ($id) {
        $stmt = $conn->prepare("UPDATE tugas SET nama_tugas=?, deskripsi=?, prioritas=?, tanggal_deadline=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $deskripsi, $prioritas, $deadline, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO tugas (nama_tugas, deskripsi, prioritas, tanggal_deadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $deskripsi, $prioritas, $deadline);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM tugas WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['selesai'])) {
    $id = $_GET['selesai'];
    $conn->query("UPDATE tugas SET status='Selesai' WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['status_update'])) {
    // AJAX update status checkbox
    $id = $_POST['id'];
    $status = $_POST['status'] === 'true' ? 'Selesai' : 'Belum';
    $stmt = $conn->prepare("UPDATE tugas SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    echo 'OK';
    exit;
}

$tugas = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM tugas WHERE id=$id");
    $tugas = $result->fetch_assoc();
}

$limit = isset($_GET['all']) ? "" : "LIMIT 5";
$data = $conn->query("SELECT * FROM tugas ORDER BY tanggal_deadline ASC $limit");
$total = $conn->query("SELECT COUNT(*) as total FROM tugas")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>To-Do List</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f0f0;
        color: #222;
        padding: 20px;
    }
    h2 {
        text-align: center;
        color: #111;
        margin-bottom: 15px;
    }
    form {
        background: #fff;
        max-width: 400px;
        margin: 0 auto 20px auto;
        padding: 15px;
        border-radius: 6px;
        box-shadow: 0 0 5px #bbb;
    }
    form label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    input[type="text"], input[type="date"], select, textarea {
        width: 100%;
        padding: 7px;
        margin-bottom: 12px;
        border: 1px solid #999;
        border-radius: 4px;
        font-size: 15px;
        box-sizing: border-box;
        background: #fff;
        color: #222;
    }
    button {
        background: #222;
        color: #fff;
        border: none;
        padding: 10px 18px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 4px;
        width: 100%;
    }
    button:hover {
        background: #444;
    }
    .table-wrapper {
        max-width: 700px;
        margin: 0 auto;
        overflow-x: auto;
    }
    table {
        width: 700px;
        border-collapse: collapse;
        background: #fff;
        font-size: 18px;
        color: #111;
        border-radius: 6px;
        box-shadow: 0 0 6px #ccc;
        table-layout: fixed;
    }
    th, td {
        padding: 12px 10px;
        border: 1px solid #ddd;
        text-align: left;
        word-wrap: break-word;
    }
    th {
        background: #333;
        color: #eee;
    }
    td.status-belum {
        color: #a00;
        font-weight: bold;
    }
    td.status-selesai {
        color: #070;
        font-style: italic;
        font-weight: bold;
    }
    .aksi a {
        text-decoration: none;
        color: #222;
        margin-right: 8px;
        font-size: 16px;
    }
    .aksi a:hover {
        text-decoration: underline;
    }
    .empty {
        text-align: center;
        font-style: italic;
        color: #888;
    }
    .lihat-semua {
        text-align: center;
        margin-top: 10px;
    }
    .lihat-semua a {
        color: #222;
        text-decoration: none;
        font-size: 16px;
    }
    .lihat-semua a:hover {
        text-decoration: underline;
    }
    input[type="checkbox"].status-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>
</head>
<body>

<h2><?= $tugas ? "Edit Tugas" : "Tambah Tugas Baru" ?></h2>
<form method="POST" id="todoForm">
    <input type="hidden" name="id" value="<?= $tugas['id'] ?? '' ?>">
    <label>Nama Tugas</label>
    <input type="text" name="nama_tugas" required value="<?= htmlspecialchars($tugas['nama_tugas'] ?? '') ?>">

    <label>Deskripsi</label>
    <textarea name="deskripsi"><?= htmlspecialchars($tugas['deskripsi'] ?? '') ?></textarea>

    <label>Prioritas</label>
    <select name="prioritas">
        <option <?= ($tugas['prioritas'] ?? '') == 'Penting Mendesak' ? 'selected' : '' ?>>Penting Mendesak</option>
        <option <?= ($tugas['prioritas'] ?? '') == 'Tidak Penting Mendesak' ? 'selected' : '' ?>>Tidak Penting Mendesak</option>
        <option <?= ($tugas['prioritas'] ?? '') == 'Penting Tidak Mendesak' ? 'selected' : '' ?>>Penting Tidak Mendesak</option>
        <option <?= ($tugas['prioritas'] ?? '') == 'Tidak Penting Tidak Mendesak' ? 'selected' : '' ?>>Tidak Penting Tidak Mendesak</option>
    </select>

    <label>Deadline</label>
    <input type="date" name="tanggal_deadline" id="tanggal_deadline" value="<?= htmlspecialchars($tugas['tanggal_deadline'] ?? '') ?>">

    <button type="submit"><?= $tugas ? "Update" : "Tambah" ?></button>
</form>

<h2>Daftar Tugas</h2>
<div class="table-wrapper">
<table>
    <tr>
        <th>Nama</th>
        <th>Deskripsi</th>
        <th>Prioritas</th>
        <th>Deadline</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php if ($data->num_rows > 0): ?>
        <?php while ($row = $data->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_tugas']) ?></td>
                <td>
                    <?php
                        $desc = strip_tags($row['deskripsi']);
                        echo strlen($desc) > 50 ? substr($desc, 0, 47) . '...' : $desc;
                    ?>
                </td>
                <td><?= htmlspecialchars($row['prioritas']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_deadline']) ?></td>
                <td class="status-cell <?= ($row['status'] ?? '') == 'Selesai' ? 'status-selesai' : 'status-belum' ?>">
                    <input type="checkbox" class="status-checkbox" data-id="<?= $row['id'] ?>" <?= ($row['status'] ?? '') == 'Selesai' ? 'checked' : '' ?>>
                    <span class="status-text"><?= ($row['status'] ?? 'Belum') ?></span>
                </td>
                <td class="aksi">
                    <a href="?edit=<?= $row['id'] ?>">Edit</a>
                    <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus tugas ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6" class="empty">Belum ada tugas 😴</td></tr>
    <?php endif; ?>
</table>
</div>

<?php if ($total > 5 && !isset($_GET['all'])): ?>
    <div class="lihat-semua">
        <a href="?all=1">Lihat Semua (<?= $total ?> tugas)</a>
    </div>
<?php endif; ?>

<script>
document.getElementById('todoForm').addEventListener('submit', function(e) {
    const inputDeadline = document.getElementById('tanggal_deadline').value;
    if (inputDeadline) {
        const today = new Date();
        today.setHours(0,0,0,0);
        const deadlineDate = new Date(inputDeadline);
        if (deadlineDate < today) {
            alert('Deadline tidak bisa karana deadline sudah lewat dari hari ini');
            e.preventDefault();
        }
    }
});

document.querySelectorAll('.status-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const id = this.getAttribute('data-id');
        const status = this.checked;
        const statusText = this.nextElementSibling;
        statusText.textContent = status ? 'Selesai' : 'Belum';

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send("status_update=1&id=" + encodeURIComponent(id) + "&status=" + status);

        xhr.onload = function() {
            if (xhr.responseText !== 'OK') {
                alert('Berhasil update status');
            }
        };
    });
});
</script>

</body>
</html>