# Website quản lý bán mô hình in 3D

Dự án nhóm CT275 – Công nghệ Web, học kỳ 1 (2026–2027).

## Phạm vi phiên bản hiện tại
**Bước 1:** khởi tạo MVC, Composer PSR-4, PDO và trang kiểm tra hệ thống. Các chức năng mua hàng chưa hoàn thành.

## Yêu cầu
- PHP 8.3+, extension pdo_mysql
- MySQL hoặc MariaDB (có thể dùng XAMPP)
- Composer

## Cách chạy trên Windows
1. Clone repository và chuyển sang nhánh `feature/customer-setup`.
2. Mở Terminal ở thư mục gốc, chạy `composer install`.
3. Bật MySQL trong XAMPP. Trong phpMyAdmin, vào Import để nhập `database/schema.sql`.
4. Sao chép `config/database.example.php` thành `config/database.php`, chỉnh tài khoản MySQL theo máy bạn.
5. Chạy `php -S localhost:8000 -t public`.
6. Truy cập http://localhost:8000; kiểm tra kết quả **Kết nối thành công** và database **shop3d**.

## Thư mục
- `public/`: document root, điểm vào HTTP.
- `app/Controllers/`: xử lý yêu cầu.
- `app/Models/`: kết nối PDO.
- `app/Views/customer/`: giao diện Bootstrap 5.
- `config/`: cấu hình MySQL; file database.php không đưa lên GitHub.
- `database/schema.sql`: 5 bảng quan hệ và dữ liệu mẫu.

## Bảo mật
Không commit file `config/database.php`; không dùng root/password rỗng trên môi trường triển khai công khai.
