<?php

namespace Faker\Provider\vi_VN;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{lastName}} {{firstNameMale}}',
        '{{titleMale}}. {{lastName}} {{firstNameMale}}',
        '{{lastName}} {{middleNameMale}} {{firstNameMale}}',
        '{{titleMale}}. {{lastName}} {{middleNameMale}} {{firstNameMale}}',
    ];

    protected static $femaleNameFormats = [
        '{{lastName}} {{firstNameFemale}}',
        '{{titleFemale}}. {{lastName}} {{firstNameFemale}}',
        '{{lastName}} {{middleNameFemale}} {{firstNameFemale}}',
        '{{titleFemale}}. {{lastName}} {{middleNameFemale}} {{firstNameFemale}}',
    ];

    protected static $middleNameFormat = [
        '{{firstNameMale}}',
        '{{firstNameFemale}}',
    ];

    /**
     * @see http://www.dattenhay.vn/1001-ten-cho-be-trai.htm
     */
    protected static $firstNameMale = [
        'An', 'Anh',
        'Bào', 'Bình', 'Bạch', 'Bảo', 'Bắc', 'Bằng', 'Bổng', 'Bửu',
        'Ca', 'Canh', 'Chiến', 'Chiểu', 'Châu', 'Chính', 'Chương', 'Chưởng', 'Chấn', 'Công', 'Cơ', 'Cương', 'Cường', 'Cảnh', 'Cần', 'Cẩn',
        'Danh', 'Di', 'Dinh', 'Diệp', 'Diệu', 'Du', 'Duy', 'Duệ', 'Dân', 'Dũng', 'Dương', 'Dụng',
        'Giang', 'Giác', 'Giáp',
        'Hiên', 'Hiếu', 'Hiền', 'Hiển', 'Hiệp', 'Hoa', 'Hoài', 'Hoàn', 'Hoàng', 'Hoán', 'Huy', 'Huynh', 'Huấn', 'Huỳnh', 'Hà', 'Hành', 'Hào', 'Hòa', 'Hùng', 'Hưng', 'Hạnh', 'Hải', 'Hảo', 'Hậu', 'Học', 'Hồng', 'Hội', 'Hợp', 'Hữu', 'Hỷ',
        'Kha', 'Khang', 'Khanh', 'Khiêm', 'Khiếu', 'Khoa', 'Khoát', 'Khánh', 'Khôi', 'Khương', 'Khải', 'Kim', 'Kiên', 'Kiếm', 'Kiện', 'Kiệt', 'Kính', 'Kỳ', 'Kỷ',
        'Lai', 'Lam', 'Linh', 'Liêm', 'Long', 'Luận', 'Luật', 'Lâm', 'Lân', 'Lý', 'Lĩnh', 'Lương', 'Lạc', 'Lập', 'Lễ', 'Lộ', 'Lộc', 'Lực',
        'Minh', 'Mạnh', 'Mẫn', 'Mỹ',
        'Nam', 'Nghiêm', 'Nghiệp', 'Nghĩa', 'Nghị', 'Nguyên', 'Ngân', 'Ngôn', 'Ngạn', 'Ngọc', 'Nhiên', 'Nhu', 'Nhuận', 'Nhân', 'Nhã', 'Nhượng', 'Nhạn', 'Nhật', 'Ninh',
        'Phi', 'Phong', 'Pháp', 'Phát', 'Phú', 'Phúc', 'Phương', 'Phước', 'Phụng',
        'Quang', 'Quyết', 'Quyền', 'Quân', 'Quý', 'Quảng', 'Quế', 'Quốc', 'Quỳnh',
        'Sang', 'Sinh', 'Siêu', 'Sáng', 'Sâm', 'Sĩ', 'Sơn', 'Sử', 'Sỹ',
        'Thanh', 'Thiên', 'Thiện', 'Thuận', 'Thành', 'Thái', 'Thông', 'Thúc', 'Thạc', 'Thạch', 'Thắng', 'Thể', 'Thịnh', 'Thọ', 'Thống', 'Thời', 'Thụy', 'Thủy', 'Thực', 'Tiến', 'Tiếp', 'Tiền', 'Tiển', 'Toàn', 'Toại', 'Toản', 'Trang', 'Triết', 'Triều', 'Triệu', 'Trung', 'Trác', 'Tráng', 'Trân', 'Trình', 'Trí', 'Trúc', 'Trường', 'Trưởng', 'Trạch', 'Trọng', 'Trụ', 'Trực', 'Tuyền', 'Tuấn', 'Tuệ', 'Tài', 'Tâm', 'Tân', 'Tín', 'Tùng', 'Tú', 'Tường', 'Tấn', 'Tụ', 'Từ',
        'Uy',
        'Vinh', 'Viên', 'Việt', 'Vu', 'Võ', 'Văn', 'Vĩ', 'Vĩnh', 'Vũ', 'Vương', 'Vượng', 'Vịnh', 'Vỹ',
        'Xuân',
        'Yên',
        'Án', 'Ân',
        'Đan', 'Điền', 'Điệp', 'Đoàn', 'Đình', 'Đôn', 'Đăng', 'Đại', 'Đạo', 'Đạt', 'Định', 'Đồng', 'Độ', 'Đức', 'Đức',
        'Ẩn',
    ];

    /**
     * @see http://www.dattenhay.vn/1001-ten-cho-be-trai.htm
     */
    protected static $middleNameMale = [
        'An', 'Anh',
        'Bá', 'Bách', 'Bình', 'Bích', 'Bảo', 'Bằng', 'Bửu', 'Bữu',
        'Cao', 'Chiêu', 'Chiến', 'Chung', 'Chuẩn', 'Chánh', 'Chí', 'Chính', 'Chấn', 'Chế', 'Cát', 'Công', 'Cương', 'Cường', 'Cảnh',
        'Danh', 'Duy', 'Dân', 'Dũng', 'Dương',
        'Gia', 'Giang',
        'Hiếu', 'Hiền', 'Hiểu', 'Hiệp', 'Hoài', 'Hoàn', 'Hoàng', 'Huy', 'Huân', 'Hà', 'Hào', 'Hán', 'Hòa', 'Hùng', 'Hưng', 'Hướng', 'Hạnh', 'Hạo', 'Hải', 'Hồ', 'Hồng', 'Hữu',
        'Khai', 'Khang', 'Khoa', 'Khuyến', 'Khánh', 'Khôi', 'Khương', 'Khải', 'Khắc', 'Khởi', 'Kim', 'Kiên', 'Kiến', 'Kiệt', 'Kỳ',
        'Lam', 'Liên', 'Long', 'Lâm', 'Lương', 'Lạc', 'Lập',
        'Minh', 'Mạnh', 'Mộng',
        'Nam', 'Nghĩa', 'Nghị', 'Nguyên', 'Nguyễn', 'Ngọc', 'Nhân', 'Như', 'Nhất', 'Nhật', 'Niệm',
        'Phi', 'Phong', 'Phú', 'Phúc', 'Phương', 'Phước', 'Phượng', 'Phục', 'Phụng',
        'Quang', 'Quyết', 'Quân', 'Quý', 'Quảng', 'Quốc',
        'Song', 'Sĩ', 'Sơn', 'Sỹ',
        'Tài', 'Tạ',
        'Ân',
        'Đan', 'Đinh', 'Đoàn', 'Đình', 'Đông', 'Đăng', 'Đại', 'Đạt', 'Đắc', 'Định', 'Đồng', 'Đức', 'Đăng', 'Đức',
    ];

    /**
     * @see http://www.dattenhay.vn/1001-ten-cho-be-gai.htm
     */
    protected static $firstNameFemale = [
        'An', 'Anh',
        'Bình', 'Bích', 'Băng', 'Bạch', 'Bảo',
        'Ca', 'Chi', 'Chinh', 'Chiêu', 'Chung', 'Châu', 'Cát', 'Cúc', 'Cương', 'Cầm',
        'Dao', 'Di', 'Diễm', 'Diệp', 'Diệu', 'Du', 'Dung', 'Duyên', 'Dân', 'Dương',
        'Giang', 'Giao',
        'Hiếu', 'Hiền', 'Hiệp', 'Hoa', 'Hoan', 'Hoài', 'Hoàn', 'Huyền', 'Huệ', 'Hà', 'Hân', 'Hòa', 'Hương', 'Hường', 'Hạ', 'Hạnh', 'Hải', 'Hảo', 'Hậu', 'Hằng', 'Hồng', 'Hợp',
        'Khai', 'Khanh', 'Khuyên', 'Khuê', 'Khánh', 'Khê', 'Khôi', 'Kim', 'Kiều',
        'Lam', 'Lan', 'Linh', 'Liên', 'Liễu', 'Loan', 'Ly', 'Lâm', 'Lý', 'Lễ', 'Lệ', 'Lộc', 'Lợi',
        'Mai', 'Mi', 'Minh', 'Miên', 'My', 'Mẫn', 'Mỹ',
        'Nga', 'Nghi', 'Nguyên', 'Nguyệt', 'Ngà', 'Ngân', 'Ngôn', 'Ngọc', 'Nhi', 'Nhiên', 'Nhung', 'Nhàn', 'Nhân', 'Nhã', 'Như', 'Nương', 'Nữ',
        'Oanh',
        'Phi',
        'Phong', 'Phúc', 'Phương', 'Phước', 'Phượng', 'Phụng',
        'Quyên', 'Quân', 'Quế', 'Quỳnh',
        'Sa', 'San', 'Sinh', 'Sương',
        'Thanh', 'Thảo', 'Thi', 'Thiên', 'Thiện', 'Thoa', 'Thoại', 'Thu', 'Thuần', 'Thuận', 'Thy', 'Thêu', 'Thùy', 'Thúy', 'Thơ', 'Thư', 'Thương', 'Thường', 'Thảo', 'Thắm', 'Thục', 'Thủy', 'Tiên', 'Trang', 'Trinh', 'Trung', 'Trà', 'Trâm', 'Trân', 'Trúc', 'Trầm', 'Tuyến', 'Tuyết', 'Tuyền', 'Tuệ', 'Ty', 'Tâm', 'Tú',
        'Uyên', 'Uyển',
        'Vi', 'Việt',
        'Vy', 'Vân', 'Vũ', 'Vọng', 'Vỹ',
        'Xuyến', 'Xuân',
        'Yên', 'Yến',
        'Ái', 'Ánh', 'Ân',
        'Đan', 'Điệp', 'Đoan', 'Đài', 'Đàn', 'Đào', 'Đình', 'Đường', 'Đan',
        'Ý',
    ];

    /**
     * @see http://www.dattenhay.vn/1001-ten-cho-be-gai.htm
     */
    protected static $middleNameFemale = [
        'An', 'Anh',
        'Ban', 'Bích', 'Băng', 'Bạch', 'Bảo', 'Bội',
        'Cam', 'Chi', 'Chiêu', 'Cát', 'Cẩm',
        'Di', 'Diên', 'Diễm', 'Diệp', 'Diệu', 'Duy', 'Duyên', 'Dã', 'Dạ',
        'Gia', 'Giang', 'Giao', 'Giáng',
        'Hiếu', 'Hiền', 'Hiểu', 'Hoa', 'Hoài', 'Hoàn', 'Hoàng', 'Huyền', 'Huệ', 'Huỳnh', 'Hà', 'Hàm', 'Hương', 'Hạ', 'Hạc', 'Hạnh', 'Hải', 'Hảo', 'Hằng', 'Họa', 'Hồ', 'Hồng',
        'Khiết', 'Khuê', 'Khánh', 'Khúc', 'Khả', 'Khải', 'Kim', 'Kiết', 'Kiều', 'Kỳ',
        'Lam', 'Lan', 'Linh', 'Liên', 'Liễu', 'Loan', 'Ly', 'Lâm', 'Lê', 'Lưu', 'Lệ', 'Lộc', 'Lục',
        'Mai', 'Minh', 'Mậu', 'Mộc', 'Mộng', 'Mỹ',
        'Nghi', 'Nguyên', 'Nguyết', 'Nguyệt', 'Ngân', 'Ngọc', 'Nhan', 'Nhã', 'Như', 'Nhất', 'Nhật',
        'Oanh',
        'Phi', 'Phong', 'Phương', 'Phước', 'Phượng', 'Phụng',
        'Quế', 'Quỳnh',
        'Sao', 'Song', 'Sông', 'Sơn', 'Sương',
        'Thanh', 'Thi', 'Thiên', 'Thiếu', 'Thiều', 'Thiện', 'Thu', 'Thuần', 'Thy', 'Thái', 'Thùy', 'Thúy', 'Thơ', 'Thư', 'Thương', 'Thạch', 'Thảo', 'Thục', 'Thụy', 'Thủy', 'Tiên', 'Tiểu', 'Trang', 'Triều', 'Triệu', 'Trà', 'Trâm', 'Trân', 'Trúc', 'Trầm', 'Tuyết', 'Tuệ', 'Tâm', 'Tùng', 'Tùy', 'Tú', 'Túy', 'Tường', 'Tịnh', 'Tố', 'Từ',
        'Uyên', 'Uyển',
        'Vi', 'Việt', 'Vy', 'Vàng', 'Vành', 'Vân', 'Vũ',
        'Xuyến', 'Xuân',
        'Yên', 'Yến',
        'Ái', 'Ánh',
        'Đan', 'Đinh', 'Đoan', 'Đài', 'Đông', 'Đồng', 'Đan', 'Đoan',
        'Ý',
    ];

    /**
     * @see http://vi.wikipedia.org/wiki/H%E1%BB%8D_ng%C6%B0%E1%BB%9Di_Vi%E1%BB%87t_Nam
     */
    protected static $lastName = [
        'An', 'Ánh',
        'Ân', 'Âu', 'Ấu',
        'Biện', 'Bàng', 'Bành', 'Bá', 'Bì', 'Bình', 'Bùi', 'Bạc', 'Bạch', 'Bảo', 'Bế', 'Bồ', 'Bửu',
        'Ca', 'Cam', 'Cao', 'Chiêm', 'Chu', 'Chung', 'Châu', 'Chương', 'Chế', 'Chử', 'Cung', 'Cái', 'Cát', 'Cù', 'Cấn', 'Cầm', 'Cổ', 'Cự',
        'Danh', 'Diêm', 'Diệp', 'Doãn', 'Dã', 'Dư', 'Dương',
        'Đan', 'Đàm', 'Đào', 'Đái', 'Đặng', 'Đậu', 'Đinh', 'Điền', 'Đoàn', 'Đôn', 'Đồng', 'Đổng', 'Đỗ', 'Đới', 'Đường',
        'Giang', 'Giao', 'Giáp', 'Giả',
        'Hoa', 'Hoàng', 'Huỳnh', 'Hy', 'Hà', 'Hàn', 'Hàng', 'Hán', 'Hình', 'Hùng', 'Hạ', 'Hồ', 'Hồng', 'Hứa',
        'Kha', 'Khoa', 'Khu', 'Khuất', 'Khâu', 'Khúc', 'Khưu', 'Khương', 'Khổng', 'Kim', 'Kiều',
        'La', 'Liễu', 'Lâm', 'Lã', 'Lê', 'Lò', 'Lô', 'Lý', 'Lư', 'Lưu', 'Lương', 'Lạc', 'Lại', 'Lều', 'Lỡ', 'Lục', 'Lữ', 'Lỳ',
        'Ma', 'Mai', 'Mang', 'Mâu', 'Mã', 'Mạc', 'Mạch', 'Mẫn', 'Mộc',
        'Nghiêm', 'Nghị', 'Nguyễn', 'Ngân', 'Ngô', 'Ngụy', 'Nhiệm', 'Nhâm', 'Nhậm', 'Nhữ', 'Ninh', 'Nông',
        'Ong',
        'Ông',
        'Phan', 'Phi', 'Phí', 'Phó', 'Phùng', 'Phương', 'Phạm',
        'Quách', 'Quản',
        'Sơn', 'Sử',
        'Thi', 'Thiều', 'Thào', 'Thái', 'Thân', 'Thôi', 'Thạch', 'Thập', 'Thịnh', 'Tiêu', 'Tiếp', 'Trang', 'Triệu', 'Trà', 'Trác', 'Trình', 'Trưng', 'Trương', 'Trần', 'Trịnh', 'Ty', 'Tào', 'Tòng', 'Tô', 'Tôn', 'Tông', 'Tăng', 'Tạ', 'Tống', 'Từ',
        'Ung', 'Uông',
        'Vi', 'Viên', 'Võ', 'Văn', 'Vũ', 'Vương', 'Vừ', 'Xa',
        'Yên',
    ];

    protected static $titleMale = ['Cụ', 'Ông', 'Bác', 'Chú', 'Anh', 'Em'];

    protected static $titleFemale = ['Cụ', 'Bà', 'Bác', 'Cô', 'Chị', 'Em'];

    public function middleName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::middleNameMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::middleNameFemale();
        }

        return $this->generator->parse(static::randomElement(static::$middleNameFormat));
    }

    public static function middleNameMale()
    {
        return static::randomElement(static::$middleNameMale);
    }

    public static function middleNameFemale()
    {
        return static::randomElement(static::$middleNameFemale);
    }
}
