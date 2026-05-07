<?php
class ClubRepository {
    private $conn;
  private $table_name = "clubs";
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM clubs");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $desc, $leader = null) {
        $sql = "INSERT INTO clubs (club_name, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name, $desc]);
    }

    public function update($id, $name, $desc) {
        $sql = "UPDATE clubs SET club_name = ?, description = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name, $desc, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM clubs WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
    public function saveRegistration($userId, $clubId, $reason) {
        $stmt = $this->db->prepare("INSERT INTO club_registrations (user_id, club_id, reason) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $clubId, $reason]);
    }
}