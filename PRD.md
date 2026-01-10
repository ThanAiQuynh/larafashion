# PRODUCT REQUIREMENTS DOCUMENT (PRD) - Project: LaraFashion

## 1. Tổng quan
LaraFashion là nền tảng thương mại điện tử chuyên về thời trang.
Điểm nhấn công nghệ: Tích hợp Chatbot AI từ bên thứ 3 (Tudongchat.com) để tự động tư vấn và thu thập khách hàng tiềm năng (Leads), sau đó đồng bộ dữ liệu về Dashboard quản trị.

## 2. Các module chính

### A. Storefront (Giao diện khách hàng)
- **Trải nghiệm mua sắm:** Xem sản phẩm, lọc theo danh mục/thương hiệu, tìm kiếm (MySQL Fulltext), Giỏ hàng, Thanh toán (COD/Chuyển khoản).
- **Trải nghiệm AI:** Khách hàng chat với Bot ở góc màn hình. Bot trả lời dựa trên dữ liệu sản phẩm của web.
- **Tài khoản:** Đăng ký/Đăng nhập, Quản lý đơn hàng, Sổ địa chỉ.

### B. Admin Panel (Giao diện quản trị)
- **Quản lý kho:** Sản phẩm, Danh mục, Thương hiệu.
- **Quản lý đơn hàng:** Xử lý trạng thái đơn hàng.
- **CRM & AI Leads:** Nhận dữ liệu khách hàng từ Chatbot gửi về qua Webhook (Tên, SĐT, Nhu cầu). Gọi điện tư vấn và chốt đơn.
- **Cấu hình:** Cài đặt mã nhúng Chatbot.

## 3. Luồng tích hợp Tudongchat (Integration Flow)
1. **Product Feed:** Hệ thống cung cấp API JSON chứa danh sách sản phẩm (Tên, Giá, Link ảnh, Link chi tiết) để nạp vào Knowledge Base của Tudongchat.
2. **Webhook Receiver:** Khi Bot thu thập được SĐT khách, Bot gọi Webhook về hệ thống -> Lưu vào bảng `chatbot_leads`.