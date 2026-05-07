<?php
class Event {
    public $id;
    public $title;
    public $club_id;
    public $club_name;
    public $description;
    public $event_date;
    public $location;
    public $created_by;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->club_id = $data['club_id'] ?? null;
        $this->club_name = $data['club_name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->event_date = $data['event_date'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->created_by = $data['created_by'] ?? null;
    }
}