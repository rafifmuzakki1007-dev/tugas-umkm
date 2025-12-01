<?php

class MenuModel {

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // 1. Ambil semua menu
    public function getAll()
    {
        $sql = "SELECT * FROM menu";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Ambil menu berdasarkan id
    public function getById($id)
    {
        $sql = "SELECT * FROM menu WHERE id_menu = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Ambil topping berdasarkan id_menu
    public function getToppingByMenu($id_menu)
    {
        $sql = "SELECT * FROM topping WHERE id_menu = 'MN001'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_menu]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
