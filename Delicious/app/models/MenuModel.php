<?php
class MenuModel {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function getAllMenu() {
        $query = "SELECT * FROM menu";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMenu($data) {
        $query = "INSERT INTO menu (id_menu, nama_menu, stok, harga, gambar) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$data['id_menu'], $data['nama_menu'], $data['stok'], $data['harga'], $data['gambar']]);
    }

    public function updateMenu($data) {
        $query = "UPDATE menu SET nama_menu = ?, stok = ?, harga = ?, gambar = ? WHERE id_menu = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$data['nama_menu'], $data['stok'], $data['harga'], $data['gambar'], $data['id_menu']]);
    }

    public function deleteMenu($id) {
        $query = "DELETE FROM menu WHERE id_menu = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
