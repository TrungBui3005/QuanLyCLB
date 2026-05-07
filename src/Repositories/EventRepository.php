<?php
include_once __DIR__ . '/../Models/Event.php';

class EventRepository {
    private $conn;
    private $table_name = "events";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
       
        $query = "SELECT e.*, c.club_name 
                  FROM " . $this->table_name . " e
                  LEFT JOIN clubs c ON e.club_id = c.id 
                  ORDER BY e.event_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $events = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = $row; 
        }
        return $events;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (club_id, title, description, event_date, location, created_by) 
                  VALUES (:club_id, :title, :description, :event_date, :location, :created_by)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':club_id' => $data->club_id,
            ':title' => $data->title,
            ':description' => $data->description,
            ':event_date' => $data->event_date,
            ':location' => $data->location,
            ':created_by' => $data->created_by 
        ]);
    }
}