## Luồng chức năng chính

graph TD
    A[User Login] --> B{Kiểm tra Role}
    
    B -->|Admin| C[Dashboard Admin]
    B -->|Sales| D[Dashboard Sales]
    B -->|Technician| E[Dashboard Technician]
    B -->|Accountant| F[Dashboard Accountant]
    
    C --> C1[Quản lý Users]
    C --> C2[Quản lý Branches]
    C --> C3[Cài đặt hệ thống]
    C --> C4[Báo cáo tổng hợp]
    
    D --> D1[POS - Tạo đơn hàng]
    D --> D2[Quản lý khách hàng]
    D --> D3[Kiểm tra tồn kho]
    D --> D4[KPI cá nhân]
    
    E --> E1[Danh sách lịch bảo trì]
    E --> E2[Nhận ticket bảo hành]
    E --> E3[Cập nhật trạng thái công việc]
    E --> E4[Báo cáo hoàn thành]
    
    F --> F1[Quản lý thanh toán]
    F --> F2[Phiếu thu/chi]
    F --> F3[Công nợ khách hàng]
    F --> F4[Xuất hóa đơn]
    
    D1 --> POS1[Quét barcode/Chọn sản phẩm]
    POS1 --> POS2[Thêm vào giỏ hàng]
    POS2 --> POS3[Tính tổng tiền + VAT]
    POS3 --> POS4[Chọn phương thức thanh toán]
    POS4 --> POS5{Thanh toán thành công?}
    POS5 -->|Yes| POS6[Tạo đơn hàng]
    POS5 -->|No| POS7[Thông báo lỗi]
    POS6 --> POS8[Cập nhật tồn kho]
    POS6 --> POS9[Tạo bảo hành]
    POS8 --> POS10[Gửi hóa đơn cho khách]
    
    E2 --> TECH1[Xem chi tiết ticket]
    TECH1 --> TECH2[Đến địa chỉ khách hàng]
    TECH2 --> TECH3[Thực hiện bảo trì/sửa chữa]
    TECH3 --> TECH4[Cập nhật parts_replaced]
    TECH4 --> TECH5[Hoàn thành ticket]
    TECH5 --> TECH6[Đặt lịch bảo trì tiếp theo]
    
    F1 --> ACC1[Chọn đơn hàng]
    ACC1 --> ACC2[Nhập thông tin thanh toán]
    ACC2 --> ACC3[Cập nhật payment_status]
    ACC3 --> ACC4[Tạo phiếu thu]
    ACC4 --> ACC5[Cập nhật công nợ]
    
    style A fill:#e1f5fe
    style B fill:#fff3e0
    style POS6 fill:#e8f5e8
    style TECH5 fill:#e8f5e8
    style ACC4 fill:#e8f5e8


## Luồng nghiệp vụ chi tiết

%% ==============================================
%% 1. TỔNG QUAN HỆ THỐNG - LUỒNG CHÍNH
%% ==============================================
graph TD
    A[User Login] --> B{Kiểm tra Role}
    
    B -->|Admin| C[Dashboard Admin]
    B -->|Sales| D[Dashboard Sales]
    B -->|Technician| E[Dashboard Technician]
    B -->|Accountant| F[Dashboard Accountant]
    
    C --> C1[Quản lý Users & Roles]
    C --> C2[Quản lý Branches & Warehouses]
    C --> C3[Cài đặt hệ thống]
    C --> C4[Báo cáo tổng hợp]
    
    D --> D1[POS - Tạo đơn hàng]
    D --> D2[Quản lý khách hàng]
    D --> D3[Kiểm tra tồn kho]
    D --> D4[KPI cá nhân]
    
    E --> E1[Danh sách lịch bảo trì]
    E --> E2[Nhận ticket bảo hành]
    E --> E3[Cập nhật trạng thái công việc]
    E --> E4[Báo cáo hoàn thành]
    
    F --> F1[Quản lý thanh toán]
    F --> F2[Phiếu thu/chi]
    F --> F3[Công nợ khách hàng]
    F --> F4[Xuất hóa đơn]
    
    style A fill:#e1f5fe
    style B fill:#fff3e0
    style C fill:#f3e5f5
    style D fill:#e8f5e8
    style E fill:#fff8e1
    style F fill:#fce4ec
end

%% ==============================================
%% 2. LUỒNG BÁN HÀNG POS CHI TIẾT
%% ==============================================
graph TD
    subgraph "LUỒNG BÁN HÀNG POS"
        POS_START[Bắt đầu tạo đơn] --> POS1[Quét barcode/Tìm sản phẩm]
        POS1 --> POS2{Sản phẩm có tồn kho?}
        POS2 -->|Yes| POS3[Thêm vào giỏ hàng]
        POS2 -->|No| POS_STOCK[Thông báo hết hàng]
        
        POS3 --> POS4[Nhập thông tin khách hàng]
        POS4 --> POS5[Áp dụng chiết khấu]
        POS5 --> POS6[Tính tổng tiền + VAT + phí ship]
        
        POS6 --> POS7{Phương thức thanh toán}
        POS7 -->|Cash| POS8A[Thanh toán tiền mặt]
        POS7 -->|Bank Transfer| POS8B[Thanh toán chuyển khoản]
        POS7 -->|E-Wallet| POS8C[VNPAY/Momo]
        POS7 -->|COD| POS8D[Giao hàng thu tiền]
        
        POS8A --> POS9[Tạo Order record]
        POS8B --> POS_WAIT[Chờ xác nhận CK] --> POS9
        POS8C --> POS_API[API thanh toán] --> POS9
        POS8D --> POS9
        
        POS9 --> POS10[Tạo Order Items]
        POS10 --> POS11[Cập nhật Inventory]
        POS11 --> POS12[Tạo Warranty records]
        POS12 --> POS13[Gửi notification khách hàng]
        POS13 --> POS_END[Hoàn thành đơn hàng]
        
        style POS9 fill:#e8f5e8
        style POS12 fill:#fff8e1
        style POS_END fill:#c8e6c9
    end

%% ==============================================
%% 3. LUỒNG BẢO HÀNH BẢO TRÌ
%% ==============================================
graph TD
    subgraph "LUỒNG BẢO HÀNH BẢO TRÌ"
        WR_START[Khách hàng yêu cầu bảo hành] --> WR1[Tạo Warranty Ticket]
        WR1 --> WR2[Phân loại vấn đề]
        WR2 --> WR3[Phân công Technician]
        
        WR3 --> WR4[Technician nhận task]
        WR4 --> WR5[Liên hệ khách hàng]
        WR5 --> WR6[Đến địa chỉ khách hàng]
        
        WR6 --> WR7[Chẩn đoán vấn đề]
        WR7 --> WR8{Cần thay linh kiện?}
        WR8 -->|Yes| WR9[Kiểm tra kho xe]
        WR8 -->|No| WR12[Sửa chữa trực tiếp]
        
        WR9 --> WR10{Có linh kiện?}
        WR10 -->|Yes| WR11[Thay linh kiện]
        WR10 -->|No| WR_ORDER[Đặt hàng linh kiện] --> WR11
        
        WR11 --> WR12
        WR12 --> WR13[Cập nhật Maintenance Record]
        WR13 --> WR14[Khách hàng ký xác nhận]
        WR14 --> WR15[Đặt lịch bảo trì tiếp theo]
        WR15 --> WR16[Cập nhật Maintenance Schedule]
        WR16 --> WR_END[Hoàn thành]
        
        style WR1 fill:#fff8e1
        style WR13 fill:#e8f5e8
        style WR_END fill:#c8e6c9
    end

%% ==============================================
%% 4. LUỒNG KẾ TOÁN THANH TOÁN
%% ==============================================
graph TD
    subgraph "LUỒNG KẾ TOÁN THANH TOÁN"
        ACC_START[Nhận thông báo đơn hàng] --> ACC1[Kiểm tra trạng thái thanh toán]
        ACC1 --> ACC2{Đã thanh toán?}
        
        ACC2 -->|Partial| ACC3[Tạo phiếu thu]
        ACC2 -->|Pending| ACC4[Nhắc nhở khách hàng]
        ACC2 -->|Paid| ACC5[Xác nhận thanh toán]
        
        ACC3 --> ACC6[Cập nhật paid_amount]
        ACC4 --> ACC7[Gửi notification]
        ACC5 --> ACC8[Tạo Invoice]
        
        ACC6 --> ACC9[Cập nhật công nợ]
        ACC7 --> ACC_WAIT[Chờ thanh toán]
        ACC8 --> ACC10[Gửi hóa đơn điện tử]
        
        ACC9 --> ACC11{Thanh toán đủ?}
        ACC11 -->|Yes| ACC8
        ACC11 -->|No| ACC4
        
        ACC10 --> ACC_END[Hoàn thành ghi sổ]
        ACC_WAIT --> ACC1
        
        style ACC3 fill:#fce4ec
        style ACC8 fill:#e8f5e8
        style ACC_END fill:#c8e6c9
    end

%% ==============================================
%% 5. LUỒNG QUẢN LÝ KHO VÀ TỒN KHO
%% ==============================================
graph TD
    subgraph "LUỒNG QUẢN LÝ KHO"
        INV_START[Cần cập nhật tồn kho] --> INV1{Loại giao dịch}
        
        INV1 -->|Import| INV2[Nhập kho]
        INV1 -->|Export| INV3[Xuất kho]
        INV1 -->|Transfer| INV4[Chuyển kho]
        INV1 -->|Adjust| INV5[Điều chỉnh]
        
        INV2 --> INV6[Tạo Import Transaction]
        INV3 --> INV7[Tạo Export Transaction]
        INV4 --> INV8[Tạo Transfer Transaction]
        INV5 --> INV9[Tạo Adjust Transaction]
        
        INV6 --> INV10[Cập nhật Inventory +]
        INV7 --> INV11[Cập nhật Inventory -]
        INV8 --> INV12[Cập nhật 2 Inventory]
        INV9 --> INV13[Cập nhật Inventory]
        
        INV10 --> INV14[Kiểm tra min_stock_alert]
        INV11 --> INV14
        INV12 --> INV14
        INV13 --> INV14
        
        INV14 --> INV15{Tồn kho < mức tối thiểu?}
        INV15 -->|Yes| INV16[Gửi cảnh báo]
        INV15 -->|No| INV_END[Hoàn thành]
        INV16 --> INV_END
        
        style INV6 fill:#e3f2fd
        style INV7 fill:#fce4ec
        style INV16 fill:#fff8e1
        style INV_END fill:#c8e6c9
    end