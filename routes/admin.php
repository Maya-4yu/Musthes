<?php 

require_once('../../config/database.php');

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'dashboard':
        include '../../page/admin/admin-page/dashboard.php';
        break;
    case 'kategori':
        include '../../page/admin/admin-page/form_kategori.php';
        break;
    default:
        echo "<h2 style='text-align: center; margin-top: 40px;'>Halaman Tidak Ditemukan </h2>";
        break;
}

?>