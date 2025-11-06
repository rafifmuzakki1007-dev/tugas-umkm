<?php

class MenuModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    private function nextId(): string {
        $stmt = $this->db->query("SELECT MAX(id_menu) AS last FROM menu");
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        $last = $row && $row['last'] ? $row['last'] : 'MN000';

        $num = (int) preg_replace('/\D/', '', $last);
        $num++;
        return 'MN' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function getAllMenu() {
        $stmt = $this->db->prepare("SELECT * FROM menu ORDER BY id_menu ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMenuById($id) {
        $stmt = $this->db->prepare("SELECT * FROM menu WHERE id_menu = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addMenu($nama, $harga, $stok, $gambar) {
        $id = $this->nextId();
        $stmt = $this->db->prepare("
            INSERT INTO menu (id_menu, nama_menu, harga, stok, gambar)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id, $nama, $harga, $stok, $gambar]);
        return $id;
    }

    public function updateMenu($id, $nama, $harga, $stok, $gambar) {
        $stmt = $this->db->prepare("
            UPDATE menu SET nama_menu=?, harga=?, stok=?, gambar=? WHERE id_menu=?
        ");
        $stmt->execute([$nama, $harga, $stok, $gambar, $id]);
    }

    public function updateMenuWithoutImage($id, $nama, $harga, $stok) {
        $stmt = $this->db->prepare("
            UPDATE menu SET nama_menu=?, harga=?, stok=? WHERE id_menu=?
        ");
        $stmt->execute([$nama, $harga, $stok, $id]);
    }

    public function menuUsed($id): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS jml FROM transaksi WHERE id_menu = ?");
        $stmt->execute([$id]);
        $jml = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$jml['jml'] > 0;
    }

    public function deleteMenu($id) {
        if ($this->menuUsed($id)) return false;

        $stmt = $this->db->prepare("DELETE FROM menu WHERE id_menu = ?");
        return $stmt->execute([$id]);
    }
}
