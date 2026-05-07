<?php
class Club {
    public $id;
    public $club_name;
    public $description;
    public $leader_id;
    public $created_at;

    public function __construct($id, $club_name, $description, $leader_id, $created_at) {
        $this->id = $id;
        $this->club_name = $club_name;
        $this->description = $description;
        $this->leader_id = $leader_id;
        $this->created_at = $created_at;
    }
}