<?php
include_once __DIR__ . '/../Models/Tintuc.php';

class TintucRepository {
    private $conn;
    private $table_name = "tintuc";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT t.*, c.club_name 
                  FROM " . $this->table_name . " t
                  LEFT JOIN clubs c ON t.club_id = c.id 
                  ORDER BY t.ngay_dang DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $news = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $news[] = $row;
        }
        return $news;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (club_id, tieu_de, hinh_anh, noi_dung, dia_diem, created_by) 
                  VALUES (:club_id, :tieu_de, :hinh_anh, :noi_dung, :dia_diem, :created_by)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':club_id'   => $data->club_id,
            ':tieu_de'   => $data->tieu_de,
            ':hinh_anh'  => $data->hinh_anh ?? null,
            ':noi_dung'  => $data->noi_dung,
            ':dia_diem'  => $data->dia_diem ?? null,
            ':created_by'=> $data->created_by
        ]);
    }
        public function getById($id) {
        $query = "SELECT t.*, c.club_name 
                  FROM " . $this->table_name . " t
                  LEFT JOIN clubs c ON t.club_id = c.id 
                  WHERE t.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET club_id = :club_id,
                      tieu_de = :tieu_de,
                      hinh_anh = :hinh_anh,
                      noi_dung = :noi_dung,
                      dia_diem = :dia_diem
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id'        => $id,
            ':club_id'   => $data->club_id,
            ':tieu_de'   => $data->tieu_de,
            ':hinh_anh'  => $data->hinh_anh ?? null,
            ':noi_dung'  => $data->noi_dung,
            ':dia_diem'  => $data->dia_diem ?? null
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
?>  