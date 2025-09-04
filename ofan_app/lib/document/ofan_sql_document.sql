

CREATE TABLE users (
    id INT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE,
    address VARCHAR(255) UNIQUE,
    phone VARCHAR(20) UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(500),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    fcm_token VARCHAR(255) UNIQUE,
    user_token VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id INT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    level INT,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_roles (
    id INT PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    role_id CHAR(36) NOT NULL,
    branch_id CHAR(36),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customers (
    id INT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255),
    address TEXT,
    -- province_code VARCHAR(10),
    -- district_code VARCHAR(10),
    customer_type ENUM('individual', 'business') DEFAULT 'individual',
    assigned_salesperson_id CHAR(36),
    referral_code CHAR(36) -- Mã giới thiệu
    loyalty_points INT DEFAULT 0, -- Tích điểm
    status ENUM('active', 'inactive', 'blacklisted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (assigned_salesperson_id) REFERENCES users(id) ON DELETE SET NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id INT PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    status ENUM('success', 'failed'),
    type VARCHAR(50) NOT NULL,    -- Loại thông báo (SMS, ZaloOA, Push)
    title VARCHAR(255) NOT NULL,
    content TEXT,
    requested_data JSON,
    response_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE product_categories (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL UNIQUE,
    code VARCHAR(50) UNIQUE,
    slug VARCHAR(255) UNIQUE,               -- Đường dẫn SEO
    description TEXT,
    parent_id CHAR(36) NULL,                -- Danh mục cha
    image_url VARCHAR(500),                 -- Ảnh đại diện danh mục
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES product_categories(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    category_id CHAR(36) NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,       -- Mã sản phẩm quản lý kho
    barcode VARCHAR(100) UNIQUE,            -- Mã vạch
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    cost_price DECIMAL(15,2) NOT NULL DEFAULT 0,   -- Giá nhập
    wholesale_price DECIMAL(15,2),                 -- Giá bán buôn
    price_sale DECIMAL(15,2) DEFAULT 0,            -- Giá khuyến mãi
    model VARCHAR(100),
    filter_stages INT DEFAULT NULL,                -- Số cấp lọc
    specifications JSON,                           -- Thông tin lõi lọc (nếu có)
    (
        id INT PRIMARY KEY
        filter_product_id CHAR(36) NOT NULL
        name VARCHAR(255) NOT NULL,
        image VARCHAR(500),
        start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        end_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        replacement_time INT DEFAULT 6,
    )
    accessories JSON,                              -- Thông tin phụ kiện (nếu có)
    (
        id INT PRIMARY KEY,
        product_accessory_id CHAR(36) NOT NULL,
        quantity INT DEFAULT 1,
    )
    warranty_months INT DEFAULT 12,                -- Thời gian bảo hành
    maintenance_cycle_months INT DEFAULT 6,        -- Chu kỳ bảo trì (lõi lọc)
    images JSON,                                   -- Nhiều ảnh
    unit VARCHAR(20) DEFAULT 'cái',                -- Đơn vị (Bộ, Cái, Mét,...)
    min_stock_alert INT DEFAULT 10,
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_code VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    branch_id CHAR(36) NOT NULL,
    salesperson_id CHAR(36) NOT NULL,
    order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned') DEFAULT 'draft',
    amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    vat_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    shipping_fee DECIMAL(15,2) NOT NULL DEFAULT 0,
    point INT DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'e_wallet', 'installment') NOT NULL,
    payment_status ENUM('pending', 'partial', 'paid') DEFAULT 'pending',
    paid_amount DECIMAL(15,2) DEFAULT 0,
    delivery_address TEXT,
    delivery_date TIMESTAMP NULL,
    delivery_note TEXT,
    note TEXT,
    internal_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (salesperson_id) REFERENCES users(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id CHAR(36) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_rate DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE returns (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    order_id CHAR(36) NOT NULL,
    customer_id CHAR(36) NOT NULL,
    status ENUM('requested','approved','rejected','completed') DEFAULT 'requested',
    reason TEXT,
    note TEXT,
    refund_amount DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);

CREATE TABLE return_items (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    return_id CHAR(36) NOT NULL,
    order_item_id CHAR(36) NOT NULL,
    quantity INT NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id)
);

CREATE TABLE payments (
    id INT PRIMARY KEY,
    payment_code VARCHAR(20) UNIQUE NOT NULL,
    customer_id CHAR(36) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'e_wallet') NOT NULL,
    payment_type ENUM('single_order', 'bulk_payment') NOT NULL DEFAULT 'single_order',
    payment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    files JSON,
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    note TEXT,
    processed_by CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (processed_by) REFERENCES users(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payment_orders (
    id INT PRIMARY KEY,
    payment_id CHAR(36) NOT NULL,
    order_id CHAR(36) NOT NULL,
    allocated_amount DECIMAL(15,2) NOT NULL, -- Số tiền được phân bổ cho các đơn hàng
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_payment_order (payment_id, order_id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE warranties (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    warranty_code VARCHAR(20) UNIQUE NOT NULL,
    order_item_id CHAR(36) NOT NULL,
    customer_id CHAR(36) NOT NULL,
    product_id CHAR(36) NOT NULL,
    warranty_period INT DEFAULT 60,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'voided') DEFAULT 'active',
    installation_date DATE,
    installation_address TEXT,
    product_filter_cores JSON,  -- Thông tin lõi lọc
    (
        id INT PRIMARY KEY
        filter_product_id CHAR(36) NOT NULL
        name VARCHAR(255) NOT NULL,
        image VARCHAR(500),
        start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        end_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        replacement_time INT DEFAULT 6,
    )
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE SET NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE maintenance_schedules (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    warranty_id CHAR(36) NOT NULL,
    scheduled_date DATE NOT NULL,
    type ENUM('maintenance', 'filter_change', 'repair', 'install', 'transfer', 'other') NOT NULL,  -- VS Bảo dưỡng, Thay lõi + VS, Sửa máy + VS, Lắp máy, Chuyển máy, Khác.
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'deleted') DEFAULT 'scheduled',
    assigned_technician_id CHAR(36),
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    notes TEXT,
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (warranty_id) REFERENCES warranties(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_technician_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE warranty_tickets (
    id CHAR(36) PRIMARY KEY,
    ticket_code VARCHAR(20) UNIQUE NOT NULL,
    warranty_id CHAR(36),
    customer_id CHAR(36),
    
    issue_category ENUM('WATER_QUALITY', 'LOW_PRESSURE', 'NOISE', 'LEAK', 'ELECTRICAL', 'FILTER', 'OTHER') NOT NULL,
    issue_description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    
    status ENUM('OPEN', 'ASSIGNED', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'CANCELLED') DEFAULT 'OPEN',
    assigned_technician_id CHAR(36) REFERENCES users(id),    
    notes TEXT,
    created_by CHAR(36) REFERENCES users(id), -- Ai tạo ticket
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (warranty_id) REFERENCES warranties(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_technician_id) REFERENCES users(id) ON DELETE SET NULL,
);

CREATE TABLE maintenance_records (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    maintenance_schedule_id CHAR(36) NOT NULL,
    warranty_id CHAR(36) NOT NULL,
    technician_id CHAR(36) NOT NULL,
    visit_date TIMESTAMP NOT NULL,
    work_description TEXT,
    parts_replaced JSON, -- Danh sách các lõi thay thế
    cost_amount DECIMAL(15,2),
    files JSON,
    status ENUM('completed', 'partial', 'failed') DEFAULT 'completed',
    next_maintenance_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maintenance_schedule_id) REFERENCES maintenance_schedules(id),

    FOREIGN KEY (warranty_id) REFERENCES warranties(id),
    FOREIGN KEY (technician_id) REFERENCES users(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


// Quản lý các chi nhánh
CREATE TABLE branches (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('hq', 'shop', 'agent') NOT NULL,
    parent_id CHAR(36),
    manager_id CHAR(36),
    phone VARCHAR(20),
    address TEXT,
    province_code VARCHAR(10),
    district_code VARCHAR(10),
    commission_rate DECIMAL(5,2) DEFAULT 0, -- Tỷ lệ hoa hồng
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES branches(id),
    FOREIGN KEY (manager_id) REFERENCES users(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Kho
CREATE TABLE warehouses (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    address TEXT,
    branch_id INT,
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

    FOREIGN KEY (branch_id) REFERENCES branches(id),
);

CREATE TABLE inventory (
    id INT PRIMARY KEY,
    warehouse_id INT,
    product_id BIGINT,
    quantity INTEGER DEFAULT 0,
    reserved_quantity INTEGER DEFAULT 0, -- Đã bán chưa giao
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
);

CREATE TABLE inventory_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id CHAR(36) NOT NULL,
    warehouse_id INT
    type ENUM('import', 'export', 'transfer') NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2),
    reference_type VARCHAR(50),      -- Loại tham chiếu (VD: đơn bán hàng, đơn thay lõi, đơn trả hàng,...)
    reference_id CHAR(36),           -- ID của đơn hàng/phiếu liên quan
    supplier_name VARCHAR(255),      -- Nhà cung cấp
    total_amount DECIMAL(15,2) DEFAULT 0,
    note TEXT,
    status ENUM('draft', 'approved', 'completed') DEFAULT 'draft',
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;