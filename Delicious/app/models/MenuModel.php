<?php
class MenuModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ==========================
    // GET ALL MENU
    // ==========================
    public function getAllMenu()
    {
        $query = "SELECT * FROM menu ORDER BY id_menu ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================
    // ADD MENU
    // ==========================
    public function addMenu($data)
    {
        $query = "INSERT INTO menu (id_menu, nama_menu, stok, harga, gambar) 
                  VALUES (:id_menu, :nama_menu, :stok, :harga, :gambar)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id_menu', htmlspecialchars($data['id_menu']));
        $stmt->bindValue(':nama_menu', htmlspecialchars($data['nama_menu']));
        $stmt->bindValue(':stok', intval($data['stok']));
        $stmt->bindValue(':harga', intval($data['harga']));
        $stmt->bindValue(':gambar', $data['gambar']);

        return $stmt->execute();
    }

    // ==========================
    // UPDATE MENU
    // ==========================
    public function updateMenu($data)
    {
        // Jika user tidak upload gambar baru
        if (empty($data['gambar'])) {
            $query = "UPDATE menu 
                      SET nama_menu = :nama_menu, stok = :stok, harga = :harga 
                      WHERE id_menu = :id_menu";
        } else {
            $query = "UPDATE menu 
                      SET nama_menu = :nama_menu, stok = :stok, harga = :harga, gambar = :gambar 
                      WHERE id_menu = :id_menu";
        }

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id_menu', $data['id_menu']);
        $stmt->bindValue(':nama_menu', htmlspecialchars($data['nama_menu']));
        $stmt->bindValue(':stok', intval($data['stok']));
        $stmt->bindValue(':harga', intval($data['harga']));
        
        if (!empty($data['gambar'])) {
            $stmt->bindValue(':gambar', $data['gambar']);
        }

        return $stmt->execute();
    }

    // ==========================
    // DELETE MENU
    // ==========================
    public function deleteMenu($id)
    {
        $query = "DELETE FROM menu WHERE id_menu = :id_menu";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id_menu', $id);
        return $stmt->execute();
    }
}
