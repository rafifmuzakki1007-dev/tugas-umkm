<?php
class ToppingModel {

    private $conn;

    public function __construct($db) {
        $this->conn = $db; // $db adalah objek PDO
    }

    // Ambil semua topping
    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM topping ORDER BY nama_topping ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM topping WHERE id_topping = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // KHUSUS UNTUK SEBLAK PRASMANAN (MN001) → hanya tampilkan topping yang id_menu = 'MN001' atau NULL
    public function getForSeblak() {
        $sql = "SELECT * FROM topping 
                WHERE id_menu = 'MN001' OR id_menu IS NULL 
                ORDER BY nama_topping ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Bonus: kurangi stok (nanti buat checkout)
    public function kurangiStok($id_topping, $jumlah = 1) {
        $sql = "UPDATE topping SET stok = stok - ? WHERE id_topping = ? AND stok >= ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$jumlah, $id_topping, $jumlah]);
    }
}
?>