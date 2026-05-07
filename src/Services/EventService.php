<?php
class EventService {
    private $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    public function getAllEvents() {
        return $this->repository->getAll();
    }

    public function createEvent($data) {
        if (empty($data->club_id)) {
            return ["status" => "error", "message" => "Vui lòng chọn câu lạc bộ"];
        }

        $data->event_date = str_replace('T', ' ', $data->event_date);
        
        if (strtotime($data->event_date) < time()) {
            return ["status" => "error", "message" => "Ngày sự kiện không được ở quá khứ"];
        }
        
        if ($this->repository->create($data)) {
            return ["status" => "success", "message" => "Tạo sự kiện thành công"];
        }
        return ["status" => "error", "message" => "Lỗi thực thi tại cơ sở dữ liệu"];
    }
}
?>