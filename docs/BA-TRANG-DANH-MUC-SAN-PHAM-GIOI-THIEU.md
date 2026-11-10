# Bước 7 bổ sung – Hoàn thiện ba mục Danh mục, Sản phẩm và Giới thiệu

Mã nguồn được triển khai trên nhánh \`feature/customer-pages\` từ \`main\`. Không đổi cấu trúc database \`shop3d\`, giỏ hàng hoặc đặt hàng.

## Chức năng
- \`GET /categories\`: tải danh mục từ bảng \`categories\`, đếm sản phẩm tương ứng và dẫn đến danh sách đã lọc.
- \`GET /products\`: tải sản phẩm từ MySQL bằng PDO prepared statement; hỗ trợ \`q\`, \`category\`, \`available=1\`, \`sort=newest|price_asc|price_desc|name\`. Hiển thị tối đa 48 kết quả.
- \`GET /about\`: trang giới thiệu về mục tiêu và công nghệ dự án CT275; không tự nhận cửa hàng thật, không tự tạo địa chỉ hay số điện thoại.
- Cập nhật menu trang chủ và breadcrumb trong chi tiết sản phẩm đến các URL mới.
- Dùng header/footer Bootstrap 5 chung trong \`app/Views/customer/partials\`.

## Cập nhật mã nguồn (Windows / VS Code)
\`\`\`powershell
cd C:\Users\TuanIT\Desktop\website-ban-mo-hinh-3d
git status
git fetch origin
git switch feature/customer-pages
composer install
php -l app/Controllers/CatalogController.php
php -l app/Controllers/AboutController.php
php -l app/Views/customer/categories.php
php -l app/Views/customer/products.php
php -l app/Views/customer/about.php
php tests/security-smoke.php
php -S localhost:8000 -t public
\`\`\`

Nếu \`git status\` báo thay đổi chưa lưu, phải commit/stash hợp lệ trước khi chuyển nhánh, không xóa tệp cấu hình MySQL cục bộ.

## Đường dẫn cần kiểm thử
| Đường dẫn | Kết quả mong đợi |
|---|---|
| http://localhost:8000/categories | Có danh mục từ MySQL; số lượng sản phẩm theo danh mục |
| http://localhost:8000/products | Hiển thị danh sách sản phẩm |
| http://localhost:8000/products?q=xe | Hiển thị mô hình xe |
| http://localhost:8000/products?category=2 | Lọc danh mục mô hình xe |
| http://localhost:8000/products?sort=price_asc | Sắp giá từ thấp tới cao |
| http://localhost:8000/products?available=1 | Chỉ còn hàng |
| http://localhost:8000/products?q=khongcotensanphamnay | Hiển thị không tìm thấy kết quả |
| http://localhost:8000/about | Giới thiệu dự án và công nghệ |
| http://localhost:8000/products/1 | Trang chi tiết vẫn hoạt động |
| http://localhost:8000/cart | Giỏ hàng vẫn hoạt động |
| http://localhost:8000/orders | Đơn hàng vẫn hoạt động với tài khoản đăng nhập |

## Đối chiếu MySQL Workbench
\`\`\`sql
USE shop3d;
SELECT c.id, c.name, COUNT(p.id) AS product_count
FROM categories c LEFT JOIN products p ON p.category_id = c.id
GROUP BY c.id, c.name ORDER BY c.id;

SELECT id, name, price, stock
FROM products ORDER BY id;
\`\`\`

Không cần import lại \`database/schema.sql\` và không cần thêm bảng. Chưa chạy kiểm thử tích hợp trên máy Windows của người dùng; cần xác minh giao diện/SQL trước khi merge PR vào main.
