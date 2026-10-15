-- ============================================================
-- 物业管理台账 PHP 版 · MySQL 表结构（InnoDB + utf8mb4）
-- 列名与 HTML 版 JS 对象属性名一一对应（camelCase），
-- 仅 store 名 billItems 对应表名 bill_items（API 层做映射）。
-- 日期/时间统一存字符串（'yyyy-MM-dd' / 'yyyy-MM-dd HH:mm'），
-- 金额用 DOUBLE（源数据为 JS number，避免 DECIMAL 舍入差异），
-- 布尔用 TINYINT(1)，由 API 层转回 true/false。
-- 不加外键级联：删除逻辑由前端控制，服务器只按指令删。
-- ============================================================

CREATE TABLE IF NOT EXISTS properties (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  type TINYINT NOT NULL DEFAULT 0,
  address VARCHAR(500) NULL,
  managementContact VARCHAR(200) NULL,
  utilityContact VARCHAR(200) NULL,
  note TEXT NULL,
  createdAt VARCHAR(32) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rooms (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  propertyId INT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  area DOUBLE NULL,
  defaultRent DOUBLE NOT NULL DEFAULT 0,
  note TEXT NULL,
  isArchived TINYINT(1) NOT NULL DEFAULT 0,
  createdAt VARCHAR(32) NULL,
  KEY idx_rooms_property (propertyId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tenants (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  phone VARCHAR(50) NULL,
  idCard VARCHAR(50) NULL,
  note TEXT NULL,
  createdAt VARCHAR(32) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leases (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  roomId INT UNSIGNED NOT NULL,
  tenantId INT UNSIGNED NOT NULL,
  startDate CHAR(10) NOT NULL,
  endDate CHAR(10) NULL,
  rentAmount DOUBLE NOT NULL,
  houseDeposit DOUBLE NOT NULL DEFAULT 0,
  utilityDeposit DOUBLE NOT NULL DEFAULT 0,
  paymentDay TINYINT NOT NULL DEFAULT 1,
  paymentCycle TINYINT NOT NULL DEFAULT 0,
  escMode TINYINT NOT NULL DEFAULT 0,
  escValue DOUBLE NOT NULL DEFAULT 0,
  note TEXT NULL,
  status TINYINT NOT NULL DEFAULT 0,
  createdAt VARCHAR(32) NULL,
  KEY idx_leases_room (roomId),
  KEY idx_leases_tenant (tenantId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bills (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  leaseId INT UNSIGNED NOT NULL,
  roomId INT UNSIGNED NOT NULL,
  tenantId INT UNSIGNED NOT NULL,
  periodStart CHAR(10) NOT NULL,
  periodEnd CHAR(10) NOT NULL,
  dueDate CHAR(10) NOT NULL,
  rentAmount DOUBLE NOT NULL DEFAULT 0,
  totalAmount DOUBLE NOT NULL DEFAULT 0,
  paidAmount DOUBLE NOT NULL DEFAULT 0,
  status TINYINT NOT NULL DEFAULT 0,
  paidAt VARCHAR(32) NULL,
  note TEXT NULL,
  isManual TINYINT(1) NOT NULL DEFAULT 0,
  createdAt VARCHAR(32) NULL,
  KEY idx_bills_lease (leaseId),
  KEY idx_bills_room (roomId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bill_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  billId INT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  amount DOUBLE NOT NULL DEFAULT 0,
  remark VARCHAR(500) NULL,
  KEY idx_items_bill (billId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  billId INT UNSIGNED NOT NULL,
  amount DOUBLE NOT NULL,
  paidAt VARCHAR(32) NOT NULL,
  method TINYINT NOT NULL DEFAULT 0,
  note VARCHAR(500) NULL,
  createdAt VARCHAR(32) NULL,
  KEY idx_pays_bill (billId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS knowledge (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(300) NOT NULL,
  content TEXT NULL,
  category TINYINT NOT NULL DEFAULT 0,
  leaseId INT UNSIGNED NULL,
  createdAt VARCHAR(32) NULL,
  updatedAt VARCHAR(32) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  userName VARCHAR(100) NOT NULL,
  displayName VARCHAR(100) NOT NULL DEFAULT '',
  isAdmin TINYINT(1) NOT NULL DEFAULT 0,
  isActive TINYINT(1) NOT NULL DEFAULT 1,
  passwordHash VARCHAR(255) NOT NULL,
  features VARCHAR(255) NOT NULL DEFAULT '',
  lastLoginAt VARCHAR(32) NULL,
  createdAt VARCHAR(32) NULL,
  UNIQUE KEY uk_users_name (userName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kv (
  `key` VARCHAR(100) NOT NULL PRIMARY KEY,
  `value` LONGTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
