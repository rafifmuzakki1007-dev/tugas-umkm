<?php
class MenuModel {
  private $db;
  public function __construct($koneksi) {
    $this->db = $koneksi;
  }

  public function getAllMenu() {
    $query = "SELECT * FROM menu";
    $result = $this->db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}
