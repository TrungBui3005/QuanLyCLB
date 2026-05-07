<?php
class TintucService {
    private $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    public function getAllNews() {
        return $this->repository->getAll();
    }

    public function createNews($data) {
        if (empty($data->club_id) || empty($data->tieu_de) || empty($data->noi_dung)) {
            return ["status" => "error", "message" => "Vui lòng điền đầy đủ thông tin bắt buộc"];
        }

        if ($this->repository->create($data)) {
            return ["status" => "success", "message" => "Đăng tin tức thành công"];
        }
        return ["status" => "error", "message" => "Lỗi khi thêm tin tức"];
    }
        public function getById($id) {
        return $this->repository->getById($id);
    }

    public function updateNews($id, $data) {
        if (empty($data->club_id) || empty($data->tieu_de) || empty($data->noi_dung)) {
            return ["status" => "error", "message" => "Vui lòng điền đầy đủ thông tin bắt buộc"];
        }

        if ($this->repository->update($id, $data)) {
            return ["status" => "success", "message" => "Cập nhật tin tức thành công"];
        }
        return ["status" => "error", "message" => "Lỗi khi cập nhật tin tức"];
    }

    public function deleteNews($id) {
        if ($this->repository->delete($id)) {
            return ["status" => "success", "message" => "Xóa tin tức thành công"];
        }
        return ["status" => "error", "message" => "Lỗi khi xóa tin tức"];
    }
}
?>