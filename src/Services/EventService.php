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

    public function getEventDetail($id) {
        if (!$id) return ["status" => "error", "message" => "Thiếu ID"];
        $event = $this->repository->getById($id);
        return $event ? ["status" => "success", "data" => $event] : ["status" => "error", "message" => "Không thấy sự kiện"];
    }

    public function updateEvent($id, $data) {
        $data->event_date = str_replace('T', ' ', $data->event_date);
        
        if ($this->repository->update($id, $data)) {
            return ["status" => "success", "message" => "Cập nhật thành công"];
        }
        return ["status" => "error", "message" => "Lỗi cập nhật database"];
    }

    public function deleteEvent($id) {
        if ($this->repository->delete($id)) {
            return ["status" => "success", "message" => "Xóa thành công"];
        }
        return ["status" => "error", "message" => "Lỗi thực thi xóa"];
    }
}
?>