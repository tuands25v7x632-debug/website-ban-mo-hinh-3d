# Bước 5 – Tài khoản khách hàng và đơn hàng

## Các URL
- `GET /register`: biểu mẫu đăng ký; `POST /register`: tạo tài khoản.
- `GET /login`: biểu mẫu đăng nhập; `POST /login`: xác thực.
- `POST /logout`: đăng xuất.
- `GET /cart`: giỏ hàng; `GET /checkout`: xác nhận đơn.
- `POST /checkout`: lưu đơn hàng bằng PDO transaction.
- `GET /orders`: lịch sử đơn của tài khoản.
- `GET /orders/{id}`: xem chi tiết đơn thuộc tài khoản.

## Hướng dẫn cài đặt ở Windows (VS Code, PowerShell)
1. Trong terminal đang chạy PHP, nhấn `Ctrl+C`.
2. Ở thư mục gốc dự án chạy `git status`; nếu có thay đổi khác ngoài `config/database.php`, lưu/commit chúng trước khi chuyển nhánh.
3. `git fetch origin`
4. `git switch feature/customer-auth-checkout`
5. `composer install`
6. **Không cần import lại `schema.sql`** nếu database `shop3d` đã có đủ 5 bảng. Giữ file cấu hình cục bộ `config/database.php`.
7. `php -S localhost:8000 -t public`
8. Mở http://localhost:8000/register và đăng ký tài khoản TEST (email mới, mật khẩu dài từ 8 ký tự trở lên).
9. Đăng xuất rồi đăng nhập tại http://localhost:8000/login.
10. Mở http://localhost:8000/products/1, thêm 1 mô hình phi hành gia vào giỏ.
11. Mở http://localhost:8000/cart, nhấn **Tiến hành đặt hàng**, sau đó **Xác nhận đặt hàng**.
12. Kiểm tra trang chi tiết đơn và http://localhost:8000/orders.

## Kiểm thử trong MySQL Workbench
```sql
USE shop3d;
SELECT id, name, email, role FROM users;
SELECT id, user_id, total, status, created_at FROM orders ORDER BY id DESC;
SELECT * FROM order_items ORDER BY id DESC;
SELECT id, name, stock FROM products ORDER BY id;
```

Khi đặt **1 phi hành gia**, với dữ liệu ban đầu giá 150.000đ và stock 12, đơn hàng sẽ ghi `total = 150000`, 1 dòng trong `order_items` và tồn kho còn 11 (nếu chưa có đơn khác).

## Các tình huống bảo mật cần thử
- Khi chưa đăng nhập, truy cập `/checkout` hoặc `/orders` sẽ chuyển tới `/login`.
- `POST /checkout` hoặc các form đăng nhập/đăng ký thiếu CSRF token sẽ trả HTTP 403.
- Khách hàng A không được xem đơn của khách hàng B bằng cách đổi ID trong `/orders/{id}` (HTTP 404).
- Mật khẩu trong bảng `users` được lưu dưới dạng hash.
- Không thể đặt vượt số lượng tồn kho. Nếu có lỗi khi ghi đơn, transaction rollback và không trừ tồn kho.
- Đăng xuất rồi truy cập `/orders` phải yêu cầu đăng nhập trở lại.

## Phạm vi và giới hạn
- Đây là **đơn hàng thử nghiệm**: chưa tích hợp thanh toán, địa chỉ giao hàng, vận chuyển thực tế, email xác nhận.
- Chưa tích hợp tính năng quản trị viên (phần việc của thành viên 2).
- Bản hiện tại chưa có giới hạn số lần thử đăng nhập, xác thực email, đặt lại mật khẩu, triển khai HTTPS. Chỉ dùng localhost để học và kiểm thử, chưa đưa ra Internet công khai.
- Không commit `config/database.php` chứa mật khẩu.
