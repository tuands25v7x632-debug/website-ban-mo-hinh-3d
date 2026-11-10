# Bước 4 – Giỏ hàng PHP Session

## Tính năng
- Giỏ hàng lưu trong `$_SESSION['cart']` theo dạng `product_id => quantity`.
- Giá bán, tên, tồn kho được lấy từ bảng `products` mỗi lần truy cập trang giỏ hàng.
- Trang `/products/1` có form thêm sản phẩm (POST `/cart/add`).
- Trang `/cart` hiển thị danh sách, cập nhật số lượng và xóa sản phẩm.
- Biểu mẫu có CSRF token; cập nhật dữ liệu thông qua POST và chuyển hướng 303.
- Kiểm tra sản phẩm còn hàng, giới hạn mỗi loại tối đa 99 sản phẩm, không lấy giá từ trình duyệt.
- **Chưa có** tạo đơn hàng, thanh toán hay giảm tồn kho; sẽ thực hiện sau.

## Cách tải và chạy trên Windows (PowerShell ở thư mục dự án)
1. Tắt máy chủ PHP bằng `Ctrl+C` ở terminal đang chạy.
2. `git status` để kiểm tra thay đổi cục bộ.
3. `git fetch origin`
4. `git switch feature/customer-cart`
5. `composer install`
6. Kiểm tra MySQL Workbench có database `shop3d` và file cấu hình cục bộ `config/database.php` còn tồn tại.
7. `php -S localhost:8000 -t public`
8. Mở `http://localhost:8000/products/1` và bấm **Thêm vào giỏ hàng**.
9. Mở `http://localhost:8000/cart` để cập nhật và xóa sản phẩm.

## Ca kiểm thử
- Thêm 1 phi hành gia (150.000đ), tăng số lượng lên 2 (300.000đ).
- Thêm 1 mô hình xe (220.000đ) => tổng tiền 520.000đ.
- Xóa mô hình xe => còn 300.000đ.
- Nhập số lượng vượt tồn kho (phi hành gia có 12 trong dữ liệu mẫu) => từ chối.
- Thử dùng GET `/cart/remove` => HTTP 405.
- Nếu gửi form thiếu CSRF token => HTTP 403.
- Xóa sản phẩm cuối cùng => thấy giỏ hàng trống.

**Lưu ý:** Session không đồng bộ giữa hai trình duyệt khác nhau. Giỏ hàng có thể mất khi phiên hết hạn. Chưa triển khai thanh toán.
