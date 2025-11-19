<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

/* ============================================================
   GENERATE ID MENU (MN001, MN002, ...)
============================================================ */
function generateMenuId($db) {
    $q = $db->query("SELECT id_menu FROM menu ORDER BY id_menu DESC LIMIT 1");
    $last = $q->fetch(PDO::FETCH_ASSOC);
    if (!$last) return "MN001";
    $num = intval(substr($last['id_menu'], 2)) + 1;
    return "MN" . str_pad($num, 3, '0', STR_PAD_LEFT);
}

/* Utility: make unique filename */
function makeFilename($originalName) {
    $base = preg_replace('/\s+/', '_', basename($originalName));
    // include microtime + random for uniqueness
    $uniq = sprintf("%s_%04d", microtime(true)*1000, mt_rand(1000,9999));
    return $uniq . "_" . $base;
}

/* Safe redirect helper */
function safeRedirect($url) {
    header("Location: " . $url);
    exit;
}

/* ============================================================
   TAMBAH MENU (SINGLE)
============================================================ */
if (isset($_POST['add_menu'])) {
    try {
        $id_menu = generateMenuId($koneksi);
        $nama = trim($_POST['nama_menu'] ?? '');
        $harga = trim($_POST['harga'] ?? '');
        $stok  = trim($_POST['stok'] ?? '');

        // basic validation
        if ($nama === '' || $harga === '' || $stok === '') {
            safeRedirect("../../index.php?page=menu_admin&alert=none");
        }

        $filename = "default.png";
        if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] !== '' && is_uploaded_file($_FILES['gambar']['tmp_name'])) {
            $filename = makeFilename($_FILES['gambar']['name']);
            move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . "/../../assets/img/menu/" . $filename);
        }

        $stmt = $koneksi->prepare("
            INSERT INTO menu (id_menu, nama_menu, harga, stok, gambar)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id_menu, $nama, $harga, $stok, $filename]);

        safeRedirect("../../index.php?page=menu_admin&msg=added&alert=add&new_id={$id_menu}");
    } catch (Exception $e) {
        // log error if you want, then redirect safely
        safeRedirect("../../index.php?page=menu_admin&alert=none");
    }
}

/* ============================================================
   TAMBAH BANYAK MENU SEKALIGUS (BULK)
============================================================ */
if (isset($_POST['add_menu_bulk'])) {
    try {
        $namaArr  = isset($_POST['nama_menu']) ? $_POST['nama_menu'] : [];
        $hargaArr = isset($_POST['harga']) ? $_POST['harga'] : [];
        $stokArr  = isset($_POST['stok']) ? $_POST['stok'] : [];
        $gambarArr = isset($_FILES['gambar']) ? $_FILES['gambar'] : null;

        $total = max(count($namaArr), count($hargaArr), count($stokArr), ($gambarArr ? count($gambarArr['name']) : 0));
        $insertedIds = [];

        for ($i = 0; $i < $total; $i++) {
            $nama  = isset($namaArr[$i]) ? trim($namaArr[$i]) : '';
            $harga = isset($hargaArr[$i]) ? trim($hargaArr[$i]) : '';
            $stok  = isset($stokArr[$i]) ? trim($stokArr[$i]) : '';

            // skip empty rows or incomplete
            if ($nama === '' || $harga === '' || $stok === '') {
                continue;
            }

            $id_menu = generateMenuId($koneksi);

            // default filename if none uploaded
            $filename = "default.png";

            if ($gambarArr && isset($gambarArr['name'][$i]) && $gambarArr['name'][$i] !== '' && isset($gambarArr['tmp_name'][$i]) && is_uploaded_file($gambarArr['tmp_name'][$i])) {
                $filename = makeFilename($gambarArr['name'][$i]);
                move_uploaded_file($gambarArr['tmp_name'][$i], __DIR__ . "/../../assets/img/menu/" . $filename);
                // small delay to reduce chance of same microtime collisions
                usleep(500);
            }

            $stmt = $koneksi->prepare("
                INSERT INTO menu (id_menu, nama_menu, harga, stok, gambar)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_menu, $nama, $harga, $stok, $filename]);

            $insertedIds[] = $id_menu;

            // small pause so filenames don't collide (already used uniq)
            usleep(1000);
        }

        if (count($insertedIds) > 0) {
            // provide both new_id (first) for compatibility and new_ids (csv) for additional handling
            $firstId = $insertedIds[0];
            $idsParam = implode(',', $insertedIds);
            safeRedirect("../../index.php?page=menu_admin&msg=added&alert=add&new_id={$firstId}&new_ids={$idsParam}");
        } else {
            safeRedirect("../../index.php?page=menu_admin&alert=none");
        }
    } catch (Exception $e) {
        safeRedirect("../../index.php?page=menu_admin&alert=none");
    }
}

/* ============================================================
   UPDATE MENU
============================================================ */
if (isset($_POST['update_menu'])) {
    try {
        $id_menu = $_POST['id_menu'] ?? '';
        $nama    = trim($_POST['nama_menu'] ?? '');
        $harga   = trim($_POST['harga'] ?? '');
        $stok    = trim($_POST['stok'] ?? '');

        if ($id_menu === '' || $nama === '' || $harga === '' || $stok === '') {
            safeRedirect("../../index.php?page=menu_admin&alert=none");
        }

        // check if file uploaded
        $hasFile = isset($_FILES['gambar']) && $_FILES['gambar']['name'] !== '' && is_uploaded_file($_FILES['gambar']['tmp_name']);
        if ($hasFile) {
            $filename = makeFilename($_FILES['gambar']['name']);
            move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . "/../../assets/img/menu/" . $filename);

            $stmt = $koneksi->prepare("
                UPDATE menu SET nama_menu=?, harga=?, stok=?, gambar=? WHERE id_menu=?
            ");
            $stmt->execute([$nama, $harga, $stok, $filename, $id_menu]);
        } else {
            $stmt = $koneksi->prepare("
                UPDATE menu SET nama_menu=?, harga=?, stok=? WHERE id_menu=?
            ");
            $stmt->execute([$nama, $harga, $stok, $id_menu]);
        }

        safeRedirect("../../index.php?page=menu_admin&msg=updated&alert=edit&updated_id={$id_menu}");
    } catch (Exception $e) {
        safeRedirect("../../index.php?page=menu_admin&alert=none");
    }
}

/* ============================================================
   DELETE MENU
============================================================ */
if (isset($_GET['delete'])) {
    try {
        $id = $_GET['delete'];
        $stmt = $koneksi->prepare("DELETE FROM menu WHERE id_menu=?");
        $stmt->execute([$id]);

        // note: not deleting image file here to avoid accidental removal if used elsewhere
        safeRedirect("../../index.php?page=menu_admin&msg=deleted&alert=delete");
    } catch (Exception $e) {
        safeRedirect("../../index.php?page=menu_admin&alert=none");
    }
}

/* ============================================================
   DEFAULT REDIRECT
============================================================ */
safeRedirect("../../index.php?page=menu_admin");
