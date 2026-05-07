<?php
class Member {
    public $id;
    public $user_id;
    public $club_id;
    public $student_code;
    public $department;
    public $position;
    public $joined_date;
    public $created_at;

    // Các trường bổ sung từ JOIN
    public $full_name;
    public $club_name;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->club_id = $data['club_id'] ?? null;
        $this->student_code = $data['student_code'] ?? null;
        $this->department = $data['department'] ?? null;
        $this->position = $data['position'] ?? null;
        $this->joined_date = $data['joined_date'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->full_name = $data['full_name'] ?? null;
        $this->club_name = $data['club_name'] ?? null;
    }
}