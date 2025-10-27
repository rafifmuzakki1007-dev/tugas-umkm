<?php
class KaryawanModel {
  private $db;
  public function __construct($koneksi) {
    $this->db = $koneksi;
  }

  public function getAllKaryawan() {
    $query = "SELECT * FROM karyawan";
    $result = $this->db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}
