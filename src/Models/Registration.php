<?php
class Registration {
    public $id;
    public $user_id;
    public $event_id;
    public $status;
    public $registered_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->event_id = $data['event_id'] ?? null;
        $this->status = $data['status'] ?? 'registered';
        $this->registered_at = $data['registered_at'] ?? null;
    }
}