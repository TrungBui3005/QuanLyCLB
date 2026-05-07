<?php

class MemberService {
    private $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    /**
     * Lấy danh sách thành viên
     * @param string $search Từ khóa tìm kiếm
     * @param int|null $club_id ID của CLB (Nếu truyền vào, chỉ lấy thành viên CLB đó)
     */
    public function getAllMembers($search = '', $club_id = null) {
        // Gọi repository xử lý lọc theo từ khóa và club_id
        return $this->repository->getAll($search, $club_id);
    }

    /**
     * Lấy chi tiết 1 thành viên theo ID
     */
    public function getMemberById($id) {
        if (empty($id)) return null;
        return $this->repository->findById($id);
    }

    /**
     * Lưu thành viên (Thêm mới hoặc Cập nhật)
     * Đảm bảo dữ liệu đầu vào hợp lệ trước khi lưu
     */
    public function saveMember($data) {
        // Chuyển sang object nếu data truyền vào là array
        if (is_array($data)) {
            $data = (object)$data;
        }

        // 1. Kiểm tra các trường bắt buộc
        if (empty($data->student_code)) {
            return ["status" => "error", "message" => "Mã sinh viên không được để trống"];
        }

        if (empty($data->club_id)) {
            return ["status" => "error", "message" => "Câu lạc bộ không hợp lệ. Vui lòng thử lại"];
        }

        if (empty($data->joined_date)) {
            return ["status" => "error", "message" => "Vui lòng chọn ngày tham gia"];
        }

        // 2. Kiểm tra logic ngày tháng (Ngày tham gia không được ở tương lai)
        if (strtotime($data->joined_date) > time()) {
            return ["status" => "error", "message" => "Ngày tham gia không thể là ngày ở tương lai"];
        }

        // 3. Thực thi lưu dữ liệu qua Repository
        try {
            if ($this->repository->save($data)) {
                $isUpdate = isset($data->id) && !empty($data->id);
                return [
                    "status" => "success", 
                    "message" => $isUpdate ? "Cập nhật thông tin thành công" : "Thêm thành viên vào CLB thành công"
                ];
            }
        } catch (Exception $e) {
            return ["status" => "error", "message" => "Lỗi hệ thống: " . $e->getMessage()];
        }

        return ["status" => "error", "message" => "Không thể lưu dữ liệu vào cơ sở dữ liệu"];
    }

    /**
     * Xóa thành viên khỏi câu lạc bộ
     */
    public function deleteMember($id) {
        if (empty($id)) {
            return ["status" => "error", "message" => "ID thành viên không hợp lệ"];
        }

        try {
            if ($this->repository->delete($id)) {
                return ["status" => "success", "message" => "Đã xóa thành viên khỏi danh sách"];
            }
        } catch (Exception $e) {
            return ["status" => "error", "message" => "Lỗi khi xóa: " . $e->getMessage()];
        }
        
        return ["status" => "error", "message" => "Thao tác xóa thất bại"];
    }
}