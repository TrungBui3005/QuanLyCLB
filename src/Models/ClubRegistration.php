<?php
namespace App\Models;

class ClubRegistration {
    public $id;
    public $user_id;
    public $club_id;
    public $reason;
    public $status;
    public $registered_at;

    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->club_id = $data['club_id'] ?? null;
        $this->reason = $data['reason'] ?? null;
        $this->status = $data['status'] ?? 'Chờ duyệt';
    }
}