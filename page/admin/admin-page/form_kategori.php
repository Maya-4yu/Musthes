<?php
$koneksi = new mysqli("localhost", "root", "", "musthes_database");

// --- VAR UNTUK NOTIFIKASI ---
$notifikasi = "";

// --- HAPUS DATA ---
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $koneksi->query("DELETE FROM tb_kategori WHERE kategori_id = '$id'");
  $notifikasi = "<div class='alert alert-warning text-center fade-alert'>
        Data kategori dengan ID <b>$id</b> berhasil dihapus!
    </div>";
}

// --- AMBIL DATA UNTUK EDIT ---
$edit_mode = false;
$edit_id = "";
$edit_nama = "";
$edit_deskripsi = "";

if (isset($_GET['edit'])) {
  $edit_id = $_GET['edit'];
  $data_edit = $koneksi->query("SELECT * FROM tb_kategori WHERE kategori_id = '$edit_id'");
  if ($data_edit->num_rows > 0) {
    $row = $data_edit->fetch_assoc();
    $edit_mode = true;
    $edit_id = $row['kategori_id'];
    $edit_nama = $row['nama_kategori'];
    $edit_deskripsi = $row['deskripsi'];
  }
}

// --- SIMPAN DATA (TAMBAH / UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $ID_kategori = trim($_POST['kategori_id']);
  $Nama_kategori = trim($_POST['nama_kategori']);
  $Deskripsi = trim($_POST['deskripsi']);

  if (!empty($ID_kategori) && !empty($Nama_kategori)) {
    if (isset($_POST['update'])) {
      $old_id = $_POST['old_id'];
      $koneksi->query("UPDATE tb_kategori 
                       SET kategori_id='$ID_kategori', nama_kategori='$Nama_kategori', deskripsi='$Deskripsi' 
                       WHERE kategori_id='$old_id'");
      $notifikasi = "<div class='alert alert-info text-center fade-alert'>Data kategori berhasil diperbarui!</div>";
    } else {
      $cek = $koneksi->query("SELECT * FROM tb_kategori WHERE kategori_id='$ID_kategori'");
      if ($cek->num_rows > 0) {
        $notifikasi = "<div class='alert alert-danger text-center fade-alert'>
                    ID kategori <b>$ID_kategori</b> sudah ada! Gunakan ID lain.
                </div>";
      } else {
        $koneksi->query("INSERT INTO tb_kategori (kategori_id, nama_kategori, deskripsi) 
                         VALUES ('$ID_kategori', '$Nama_kategori', '$Deskripsi')");
        $notifikasi = "<div class='alert alert-success text-center fade-alert'>
                    Data kategori berhasil disimpan!
                </div>";
      }
    }
  }
}

// --- AMBIL SEMUA DATA KATEGORI ---
$data_kategori = $koneksi->query("SELECT * FROM tb_kategori ORDER BY kategori_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Kategori</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .fade-alert { opacity: 1; transition: opacity 1s ease-out; }
    .fade-alert.hide { opacity: 0; }
  </style>
  <script>
    function konfirmasiHapus(id) {
      if (confirm('Yakin ingin menghapus data dengan ID ' + id + '?')) {
        window.location = '?page=kategori&hapus=' + id;
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      const alertBox = document.querySelector(".fade-alert");
      if (alertBox) {
        setTimeout(() => {
          alertBox.classList.add("hide");
          setTimeout(() => alertBox.remove(), 800);
        }, 2000);
      }
    });
  </script>
</head>

<body class="bg-light">
  <div class="container mt-4">

    <!-- Notifikasi -->
    <?php if (!empty($notifikasi)) echo $notifikasi; ?>

    <div class="card shadow-lg p-4 rounded-4 mb-4">
      <h3 class="mb-4 text-center"><?= $edit_mode ? 'Edit Data Kategori' : 'Tambah Kategori' ?></h3>

      <!-- ✅ ROUTING FIX: selalu kembali ke halaman ini -->
      <form action="?page=kategori" method="POST">
        <div class="mb-3">
          <label class="form-label">ID Kategori</label>
          <input type="text" name="kategori_id" class="form-control"
            value="<?= $edit_mode ? htmlspecialchars($edit_id) : '' ?>"
            placeholder="Masukkan ID Kategori" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama Kategori</label>
          <input type="text" name="nama_kategori" class="form-control"
            value="<?= $edit_mode ? htmlspecialchars($edit_nama) : '' ?>"
            placeholder="Tuliskan nama kategori" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="3"
            placeholder="Tuliskan deskripsi kategori"><?= $edit_mode ? htmlspecialchars($edit_deskripsi) : '' ?></textarea>
        </div>

        <?php if ($edit_mode): ?>
          <input type="hidden" name="old_id" value="<?= htmlspecialchars($edit_id) ?>">
          <button type="submit" name="update" class="btn btn-warning w-100">Update Data</button>
          <a href="?page=kategori" class="btn btn-secondary w-100 mt-2">Batal Edit</a>
        <?php else: ?>
          <button type="submit" class="btn btn-primary w-100">Simpan</button>
        <?php endif; ?>
      </form>
    </div>

    <div class="card shadow-lg p-4 rounded-4">
      <h4 class="mb-3 text-center">Daftar Kategori</h4>
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark text-center">
          <tr>
            <th>No</th>
            <th>ID Kategori</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          if ($data_kategori->num_rows > 0) {
            while ($row = $data_kategori->fetch_assoc()) {
              echo "<tr>
                      <td class='text-center'>$no</td>
                      <td class='text-center'>{$row['kategori_id']}</td>
                      <td class='text-center'>{$row['nama_kategori']}</td>
                      <td class='text-center'>{$row['deskripsi']}</td>
                      <td class='text-center'>
                        <a href='?page=kategori&edit={$row['kategori_id']}' class='btn btn-warning btn-sm me-1'>Edit</a>
                        <button class='btn btn-danger btn-sm' onclick=\"konfirmasiHapus('{$row['kategori_id']}')\">Hapus</button>
                      </td>
                    </tr>";
              $no++;
            }
          } else {
            echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data kategori.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
