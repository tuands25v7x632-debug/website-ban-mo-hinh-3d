# BƯỚC 6 – BẢO MẬT VÀ KIỂM THỬ WEBSITE KHÁCH HÀNG (CT275)

## Đã cải thiện trong nhánh này
1. Bổ sung HTTP header `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy` và `Cache-Control: no-store`.
2. Kiểm tra trạng thái tài khoản và vai trò từ MySQL trên mỗi yêu cầu xem/đặt đơn hàng; tài khoản đã bị xóa sẽ phải đăng nhập lại.
3. Xoay CSRF token khi đăng nhập; các biểu mẫu thay đổi dữ liệu dùng POST + CSRF token.
4. Giới hạn **5 lần đăng nhập thất bại trong 15 phút trong cùng phiên trình duyệt**. Đây **không phải** cơ chế chống brute-force toàn diện (cần thêm giới hạn trên máy chủ theo tài khoản/IP).
5. Truy cập GET /logout trả HTTP 405; đăng xuất chỉ chấp nhận POST kèm CSRF.
6. Khi giỏ hàng không còn hàng, chuyển về giỏ kèm thông báo thay vì lỗi 503.
7. Giữ các biện pháp đã có: PDO prepared statement, khóa dòng `SELECT ... FOR UPDATE` và transaction, kiểm tra quyền sở hữu đơn hàng bằng `user_id`.
8. Tạo `tests/security-smoke.php` và GitHub Actions để kiểm tra cú pháp PHP, Composer PSR-4 và hành vi Session/CSRF.

## Cập nhật dự án bằng VS Code / PowerShell
Tại thư mục `C:\Users\TuanIT\Desktop\website-ban-mo-hinh-3d`:
1. Dừng PHP server hiện tại bằng `Ctrl+C`.
2. `git status`. Nếu còn file thay đổi, commit/stash đúng phần của bạn trước khi chuyển nhánh; **không xóa** `config/database.php`.
3. `git fetch origin`
4. `git switch feature/customer-security`
5. `composer install`
6. `php tests/security-smoke.php`
7. `php -l app/Support/Auth.php`
8. `php -l app/Controllers/AuthController.php`
9. `php -l app/Controllers/OrderController.php`
10. `php -l public/index.php`
11. `php -S localhost:8000 -t public`
12. Mở http://localhost:8000 và kiểm tra.

Không chạy lại `database/schema.sql` nếu đã có dữ liệu và đơn hàng. Giữ nguyên MySQL và mật khẩu trong file cấu hình local.

## Kiểm thử chức năng / bảo mật trên trình duyệt
| Mã | Thao tác | Kết quả mong đợi |
|---|---|---|
| TC01 | Mở trang chủ, /products/1 | Danh sách/chi tiết sản phẩm được tải |
| TC02 | Đăng xuất rồi truy cập /orders | Chuyển tới /login |
| TC03 | Đăng xuất rồi truy cập /checkout | Chuyển tới /login |
| TC04 | GET http://localhost:8000/logout | HTTP 405 (không tự đăng xuất) |
| TC05 | Đăng nhập bằng mật khẩu sai 5 lần, thử lần thứ 6 | Bị hạn chế đăng nhập trong phiên khoảng 15 phút |
| TC06 | Mở /orders/999999 khi đã đăng nhập | HTTP 404 |
| TC07 | Khách A đặt đơn, khách B đăng nhập và thử /orders/ID-của-A | HTTP 404; không lộ chi tiết đơn A |
| TC08 | Gửi POST đặt hàng không có CSRF token | HTTP 403 |
| TC09 | Chọn số lượng vượt stock và bấm đặt hàng | Không có đơn mới, stock không bị trừ |
| TC10 | Đặt một sản phẩm hợp lệ | Có đơn, có order_items, stock giảm tương ứng |
| TC11 | Đăng nhập thành công rồi đăng xuất | Không còn xem /orders được nếu chưa đăng nhập |

**Lưu ý:** TC05 nên dùng tài khoản thử nghiệm riêng; không thử trên tài khoản thật. Đối với TC08, có thể dùng DevTools để bỏ trường ẩn `csrf_token` trên form rồi gửi, chỉ trên localhost của bạn.

## Kiểm tra database bằng MySQL Workbench (chỉ SELECT)
```sql
USE shop3d;
SELECT id, email, role, LEFT(password_hash, 7) AS hash_prefix FROM users;
SELECT id, user_id, total, status FROM orders ORDER BY id DESC LIMIT 10;
SELECT order_id, product_id, quantity, unit_price FROM order_items ORDER BY id DESC LIMIT 20;
SELECT id, name, stock FROM products ORDER BY id;
```

Mật khẩu phải là **chuỗi hash**, không phải mật khẩu thật. Không chia sẻ hay chụp cột `password_hash` đầy đủ.

Để kiểm tra riêng đơn #4 của bạn:
```sql
SELECT id, user_id, total, status FROM orders WHERE id = 4;
SELECT order_id, product_id, quantity, unit_price FROM order_items WHERE order_id = 4;
```

## Bằng chứng báo cáo cá nhân
Chụp ảnh trang đăng nhập, lịch sử đơn, trang 404 khi nhập ID không hợp lệ, ảnh thông báo CSRF bị từ chối, màn hình PASS của `php tests/security-smoke.php` và kết quả SQL SELECT trong Workbench. Che email cá nhân khi nộp nếu cần.

## Giới hạn chưa đáp ứng để triển khai Internet
- HTTPS/TLS và cấu hình web server production chưa có.
- Giới hạn đăng nhập mới ở mức Session, chưa giới hạn theo IP/tài khoản tại máy chủ.
- Chưa có xác thực email, đặt lại mật khẩu, chính sách mật khẩu nâng cao hay nhật ký kiểm toán.
- Chưa có quy trình kiểm thử tự động đầy đủ với MySQL và kiểm thử tải đồng thời.
- Cần tích hợp phần Admin, upload ảnh có kiểm tra loại file, cấu hình caching và triển khai theo tiêu chí giảng viên.

**Không tự nhận là an toàn tuyệt đối hoặc đã hoàn thiện toàn bộ dự án; đây là bước gia cố và bộ kiểm thử cho môi trường học tập.**
