# คู่มือ MySQL ฉบับสมบูรณ์ (พื้นฐาน → ระดับใช้งานจริง)

> เอกสารนี้รวบรวมความรู้ MySQL ตั้งแต่แนวคิดพื้นฐานไปจนถึงเทคนิคที่ใช้จริงในระบบ Production พร้อมตัวอย่างโค้ดประกอบทุกหัวข้อ

---

## สารบัญ

1. [MySQL คืออะไร](#1-mysql-คืออะไร)
2. [แนวคิดพื้นฐาน: Database, Table, Row, Column](#2-แนวคิดพื้นฐาน-database-table-row-column)
3. [การติดตั้งและเริ่มต้นใช้งาน](#3-การติดตั้งและเริ่มต้นใช้งาน)
4. [Data Types](#4-data-types)
5. [DDL: การสร้างและแก้ไขโครงสร้างตาราง](#5-ddl-การสร้างและแก้ไขโครงสร้างตาราง)
6. [DML: CRUD พื้นฐาน](#6-dml-crud-พื้นฐาน)
7. [Query ขั้นสูง: WHERE, ORDER BY, JOIN, GROUP BY](#7-query-ขั้นสูง)
8. [Subquery และ CTE](#8-subquery-และ-cte)
9. [Keys และ Relationships](#9-keys-และ-relationships)
10. [Indexing](#10-indexing)
11. [Transactions และ ACID](#11-transactions-และ-acid)
12. [Normalization](#12-normalization)
13. [Stored Procedures, Functions, Triggers](#13-stored-procedures-functions-triggers)
14. [Views](#14-views)
15. [Replication](#15-replication)
16. [Performance Tuning](#16-performance-tuning)
17. [Security](#17-security)
18. [Backup & Restore](#18-backup--restore)
19. [การเชื่อมต่อจากภาษาโปรแกรม (Node.js / Python)](#19-การเชื่อมต่อจากภาษาโปรแกรม)
20. [Best Practices สำหรับ Production](#20-best-practices-สำหรับ-production)
21. [คำสั่งที่ใช้บ่อย (Cheat Sheet)](#21-คำสั่งที่ใช้บ่อย-cheat-sheet)

---

## 1. MySQL คืออะไร

**MySQL** คือระบบจัดการฐานข้อมูลเชิงสัมพันธ์ (**RDBMS - Relational Database Management System**) ที่ใช้ภาษา **SQL (Structured Query Language)** ในการจัดการข้อมูล เป็นหนึ่งใน RDBMS ที่ได้รับความนิยมสูงสุดในโลก โดยเฉพาะกับเว็บแอปพลิเคชัน

### ลักษณะสำคัญ

- **Schema ตายตัว (Fixed Schema)** — ต้องกำหนดโครงสร้างตารางและชนิดข้อมูลล่วงหน้า
- **ข้อมูลจัดเก็บเป็นตาราง (Table)** ที่มีแถว (Row) และคอลัมน์ (Column)
- **รองรับ ACID Transaction** เต็มรูปแบบ ทำให้เหมาะกับงานที่ต้องการความถูกต้องของข้อมูลสูง
- **ใช้ JOIN เชื่อมโยงข้อมูลระหว่างตาราง** ได้อย่างมีประสิทธิภาพ

### เหมาะกับงานแบบไหน

- ระบบที่ข้อมูลมีโครงสร้างชัดเจนและสัมพันธ์กันซับซ้อน เช่น ระบบบัญชี, ธนาคาร, ระบบ E-commerce
- งานที่ต้องการความถูกต้องแม่นยำของข้อมูลสูง (Strong Consistency)
- เว็บแอปพลิเคชันทั่วไป (WordPress, Laravel, Django ฯลฯ ใช้ MySQL เป็นค่าเริ่มต้นได้)

### ไม่เหมาะกับงานแบบไหน

- ข้อมูลที่ไม่มีโครงสร้างแน่นอนหรือเปลี่ยนแปลงบ่อยมาก (เหมาะกับ NoSQL อย่าง MongoDB มากกว่า)
- ระบบที่ต้อง Scale แนวนอน (Horizontal Scale) แบบไม่จำกัดในเครื่องจำนวนมาก (ทำได้ยากกว่า NoSQL บางประเภท)

---

## 2. แนวคิดพื้นฐาน: Database, Table, Row, Column

```
MySQL Server
 └── Database (เช่น "shop_db")
      └── Table (เช่น "users")
           ├── Column (เช่น name, email, age)
           └── Row (ข้อมูล 1 แถว เช่น ผู้ใช้ 1 คน)
```

### ตัวอย่างตาราง `users`

| id | name | email | age | created_at |
|---|---|---|---|---|
| 1 | สมชาย ใจดี | somchai@example.com | 28 | 2026-07-18 |
| 2 | สมหญิง รักเรียน | somying@example.com | 25 | 2026-07-19 |

**จุดสังเกต:**
- ทุกแถวมีโครงสร้างคอลัมน์เหมือนกันเสมอ (ต่างจาก MongoDB)
- `id` มักเป็น **Primary Key** ที่ไม่ซ้ำกันในแต่ละแถว
- ชนิดข้อมูลของแต่ละคอลัมน์ถูกกำหนดตายตัวตั้งแต่สร้างตาราง

---

## 3. การติดตั้งและเริ่มต้นใช้งาน

### วิธีติดตั้ง (สรุปแนวทางหลัก)

1. **ติดตั้งในเครื่อง (Local)** — ดาวน์โหลด MySQL Community Server จากเว็บไซต์ทางการ
2. **ใช้ Docker** (สะดวกที่สุดสำหรับทดสอบ):

```bash
docker run -d --name mysql-db -p 3306:3306 \
  -e MYSQL_ROOT_PASSWORD=password123 \
  -e MYSQL_DATABASE=shop_db \
  mysql:8.0
```

3. **ใช้บริการ Cloud แบบ Managed** เช่น Amazon RDS, PlanetScale, Google Cloud SQL — เหมาะกับ Production เพราะไม่ต้องดูแล Infra เอง

### เชื่อมต่อผ่าน MySQL CLI

```bash
mysql -h localhost -u root -p
```

### คำสั่งเริ่มต้น

```sql
SHOW DATABASES;                 -- แสดงรายชื่อ database ทั้งหมด
CREATE DATABASE shop_db;        -- สร้าง database ใหม่
USE shop_db;                    -- สลับไปใช้ database
SHOW TABLES;                    -- แสดง table ทั้งหมดใน db ปัจจุบัน
DESCRIBE users;                 -- แสดงโครงสร้างตาราง
```

---

## 4. Data Types

### ชนิดข้อมูลหลักที่ใช้บ่อย

| กลุ่ม | ชนิดข้อมูล | ใช้เมื่อ |
|---|---|---|
| ตัวเลข | `INT`, `BIGINT` | จำนวนเต็ม เช่น อายุ, จำนวนสินค้า |
| ตัวเลข | `DECIMAL(10,2)` | เงิน/ทศนิยมที่ต้องแม่นยำ (ห้ามใช้ FLOAT กับเงิน) |
| ตัวเลข | `FLOAT`, `DOUBLE` | ทศนิยมทั่วไปที่ไม่ต้องแม่นยำสูง |
| ข้อความ | `VARCHAR(n)` | ข้อความสั้น ความยาวไม่เกิน n |
| ข้อความ | `TEXT` | ข้อความยาว เช่น เนื้อหาบทความ |
| วันเวลา | `DATE` | วันที่อย่างเดียว |
| วันเวลา | `DATETIME` / `TIMESTAMP` | วันที่ + เวลา |
| อื่นๆ | `BOOLEAN` | จริง/เท็จ (เก็บเป็น TINYINT ภายใน) |
| อื่นๆ | `ENUM('a','b','c')` | ค่าที่จำกัดตัวเลือกไว้ล่วงหน้า |
| อื่นๆ | `JSON` | เก็บข้อมูลแบบ JSON (MySQL 5.7+) |

### ตัวอย่างการใช้งาน

```sql
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  description TEXT,
  status ENUM('active', 'inactive') DEFAULT 'active',
  metadata JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 5. DDL: การสร้างและแก้ไขโครงสร้างตาราง

**DDL (Data Definition Language)** คือคำสั่งที่ใช้จัดการโครงสร้างของ Database

### สร้างตาราง

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  age INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### แก้ไขตาราง

```sql
ALTER TABLE users ADD COLUMN phone VARCHAR(20);           -- เพิ่มคอลัมน์
ALTER TABLE users MODIFY COLUMN age SMALLINT;              -- แก้ชนิดข้อมูล
ALTER TABLE users CHANGE COLUMN phone tel VARCHAR(20);     -- เปลี่ยนชื่อคอลัมน์
ALTER TABLE users DROP COLUMN tel;                          -- ลบคอลัมน์
```

### ลบตาราง / ล้างข้อมูล

```sql
DROP TABLE users;        -- ลบตารางทั้งหมด (โครงสร้าง+ข้อมูล)
TRUNCATE TABLE users;    -- ลบข้อมูลทั้งหมดแต่คงโครงสร้างตารางไว้
```

---

## 6. DML: CRUD พื้นฐาน

**DML (Data Manipulation Language)** คือคำสั่งที่ใช้จัดการข้อมูลภายในตาราง

### 6.1 Create (เพิ่มข้อมูล)

```sql
INSERT INTO users (name, email, age)
VALUES ('สมชาย ใจดี', 'somchai@example.com', 28);

-- เพิ่มหลายแถวพร้อมกัน
INSERT INTO users (name, email, age) VALUES
  ('A', 'a@example.com', 20),
  ('B', 'b@example.com', 22);
```

### 6.2 Read (อ่านข้อมูล)

```sql
SELECT * FROM users;

SELECT name, email FROM users WHERE age >= 25;

SELECT * FROM users ORDER BY age DESC LIMIT 10;
```

### 6.3 Update (แก้ไขข้อมูล)

```sql
UPDATE users
SET age = 26
WHERE email = 'somying@example.com';
```

> **ข้อควรระวัง:** ห้ามลืม `WHERE` ไม่งั้นจะอัปเดตข้อมูล**ทุกแถว**ในตาราง

### 6.4 Delete (ลบข้อมูล)

```sql
DELETE FROM users WHERE age < 18;
```

---

## 7. Query ขั้นสูง

### 7.1 WHERE และเงื่อนไข

```sql
SELECT * FROM users WHERE age BETWEEN 20 AND 30;
SELECT * FROM users WHERE name LIKE 'สม%';        -- ขึ้นต้นด้วย "สม"
SELECT * FROM users WHERE status IN ('active', 'pending');
SELECT * FROM users WHERE email IS NOT NULL;
SELECT * FROM users WHERE age >= 18 AND status = 'active';
```

### 7.2 ORDER BY, LIMIT, DISTINCT

```sql
SELECT DISTINCT city FROM users;
SELECT * FROM products ORDER BY price ASC LIMIT 5 OFFSET 10;  -- แบ่งหน้า (pagination)
```

### 7.3 JOIN — หัวใจสำคัญของ SQL

```sql
-- INNER JOIN: เอาเฉพาะแถวที่จับคู่กันได้ทั้งสองฝั่ง
SELECT orders.id, users.name, orders.total
FROM orders
INNER JOIN users ON orders.user_id = users.id;

-- LEFT JOIN: เอาทุกแถวจากตารางซ้าย แม้ไม่มีคู่ในตารางขวา
SELECT users.name, orders.id
FROM users
LEFT JOIN orders ON users.id = orders.user_id;

-- RIGHT JOIN: ตรงข้ามกับ LEFT JOIN
SELECT users.name, orders.id
FROM users
RIGHT JOIN orders ON users.id = orders.user_id;
```

**เปรียบเทียบ JOIN แบบภาพ:**

| ประเภท | ผลลัพธ์ |
|---|---|
| `INNER JOIN` | เฉพาะข้อมูลที่ match กันทั้งสองตาราง |
| `LEFT JOIN` | ทุกแถวของตารางซ้าย + ข้อมูลที่ match จากตารางขวา (ไม่ match = NULL) |
| `RIGHT JOIN` | ทุกแถวของตารางขวา + ข้อมูลที่ match จากตารางซ้าย |
| `FULL OUTER JOIN` | ทุกแถวจากทั้งสองตาราง (MySQL ไม่มีคำสั่งนี้ตรงๆ ต้องใช้ `UNION` แทน) |

### 7.4 GROUP BY และ Aggregate Functions

```sql
SELECT status, COUNT(*) AS total
FROM orders
GROUP BY status;

SELECT customer_id, SUM(total) AS total_spent, AVG(total) AS avg_order
FROM orders
GROUP BY customer_id
HAVING SUM(total) > 1000        -- HAVING ใช้กรองหลัง GROUP BY (WHERE ใช้กรองก่อน)
ORDER BY total_spent DESC;
```

**Aggregate Functions ที่ใช้บ่อย:** `COUNT()`, `SUM()`, `AVG()`, `MIN()`, `MAX()`

---

## 8. Subquery และ CTE

### Subquery (Query ซ้อน Query)

```sql
-- หาผู้ใช้ที่มียอดสั่งซื้อมากกว่าค่าเฉลี่ย
SELECT name FROM users
WHERE id IN (
  SELECT user_id FROM orders
  GROUP BY user_id
  HAVING SUM(total) > (SELECT AVG(total) FROM orders)
);
```

### CTE (Common Table Expression) — อ่านง่ายกว่า Subquery ซ้อนลึก

```sql
WITH order_totals AS (
  SELECT user_id, SUM(total) AS total_spent
  FROM orders
  GROUP BY user_id
)
SELECT users.name, order_totals.total_spent
FROM users
JOIN order_totals ON users.id = order_totals.user_id
WHERE order_totals.total_spent > 1000;
```

### Window Functions (MySQL 8.0+)

```sql
SELECT
  name,
  total,
  RANK() OVER (ORDER BY total DESC) AS rank_no
FROM orders;
```

---

## 9. Keys และ Relationships

### Primary Key

ค่าที่ไม่ซ้ำกันในแต่ละแถว ใช้ระบุตัวตนของแถวนั้น

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ...
);
```

### Foreign Key

ใช้เชื่อมความสัมพันธ์ระหว่างตาราง และบังคับความถูกต้องของข้อมูล (Referential Integrity)

```sql
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(10,2),
  FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE          -- ถ้า user ถูกลบ order ที่เกี่ยวข้องจะถูกลบตาม
    ON UPDATE CASCADE
);
```

### ประเภทความสัมพันธ์

| ประเภท | ตัวอย่าง | วิธีทำ |
|---|---|---|
| One-to-One | users ↔ user_profiles | Foreign Key + Unique constraint |
| One-to-Many | users → orders | Foreign Key ที่ฝั่ง "Many" |
| Many-to-Many | students ↔ courses | ต้องมีตารางกลาง (Junction Table) เช่น `student_courses` |

```sql
-- ตัวอย่าง Many-to-Many
CREATE TABLE student_courses (
  student_id INT,
  course_id INT,
  PRIMARY KEY (student_id, course_id),
  FOREIGN KEY (student_id) REFERENCES students(id),
  FOREIGN KEY (course_id) REFERENCES courses(id)
);
```

---

## 10. Indexing

Index ช่วยให้การค้นหาเร็วขึ้น โดยไม่ต้องสแกนทุกแถว (Full Table Scan)

### สร้าง Index

```sql
CREATE INDEX idx_email ON users(email);

-- Composite Index (หลายคอลัมน์)
CREATE INDEX idx_status_date ON orders(status, created_at);

-- Unique Index
CREATE UNIQUE INDEX idx_unique_email ON users(email);
```

### ตรวจสอบว่า Query ใช้ Index หรือไม่

```sql
EXPLAIN SELECT * FROM users WHERE email = 'test@example.com';
```

ดูค่าคอลัมน์ `type`:
- `ref` / `const` / `eq_ref` = ใช้ Index ได้ดี
- `ALL` = สแกนทั้งตาราง (Full Table Scan) — ควรหลีกเลี่ยงในตารางใหญ่

### หลักการเลือก Index

- สร้าง Index บนคอลัมน์ที่ใช้ใน `WHERE`, `JOIN`, `ORDER BY` บ่อย
- Composite Index ควรเรียงคอลัมน์จากที่ใช้ Equality (`=`) มากที่สุดไปหาที่ใช้ Range (`>`, `<`) 
- อย่าสร้าง Index มากเกินจำเป็น เพราะทำให้ `INSERT`/`UPDATE`/`DELETE` ช้าลง (ต้องอัปเดต Index ทุกตัวด้วย)

---

## 11. Transactions และ ACID

**Transaction** คือกลุ่มคำสั่งที่ต้อง "สำเร็จทั้งหมด" หรือ "ล้มเหลวทั้งหมด" (Atomic)

### หลักการ ACID

| หลักการ | ความหมาย |
|---|---|
| **A**tomicity | ทำสำเร็จทั้งหมดหรือย้อนกลับทั้งหมด ไม่มีทำครึ่งเดียว |
| **C**onsistency | ข้อมูลต้องถูกต้องตามกฎเสมอ (constraints, foreign keys) |
| **I**solation | Transaction หลายตัวทำงานพร้อมกันโดยไม่รบกวนกัน |
| **D**urability | เมื่อ commit แล้ว ข้อมูลต้องอยู่ถาวรแม้ระบบล่ม |

### ตัวอย่าง: ระบบโอนเงิน

```sql
START TRANSACTION;

UPDATE accounts SET balance = balance - 100 WHERE id = 'A';
UPDATE accounts SET balance = balance + 100 WHERE id = 'B';

COMMIT;
-- ถ้าเกิดข้อผิดพลาดระหว่างทาง ให้เรียก ROLLBACK; แทน COMMIT;
```

### Isolation Levels

```sql
SET TRANSACTION ISOLATION LEVEL READ COMMITTED;
```

| Level | ป้องกันปัญหา | ความเร็ว |
|---|---|---|
| `READ UNCOMMITTED` | ไม่ป้องกันอะไรเลย (อ่านข้อมูลที่ยังไม่ commit ได้ = Dirty Read) | เร็วสุด |
| `READ COMMITTED` | ป้องกัน Dirty Read | เร็ว |
| `REPEATABLE READ` (ค่า default ของ MySQL/InnoDB) | ป้องกัน Dirty Read + Non-repeatable Read | ปานกลาง |
| `SERIALIZABLE` | ป้องกันทุกปัญหา รวมถึง Phantom Read | ช้าสุด |

---

## 12. Normalization

**Normalization** คือกระบวนการออกแบบตารางเพื่อลดข้อมูลซ้ำซ้อน (Redundancy) และป้องกันความผิดปกติของข้อมูล (Anomaly)

### 1NF (First Normal Form)
แต่ละคอลัมน์เก็บค่าเดียว ไม่เก็บหลายค่าในช่องเดียว

```
❌ ไม่ผ่าน 1NF:  tags = "sale,new,hot"
✅ ผ่าน 1NF:     แยกเป็นตาราง product_tags แทน
```

### 2NF (Second Normal Form)
ต้องผ่าน 1NF และทุกคอลัมน์ต้องขึ้นกับ Primary Key ทั้งหมด (ไม่ขึ้นกับแค่บางส่วนของ Composite Key)

### 3NF (Third Normal Form)
ต้องผ่าน 2NF และไม่มีคอลัมน์ที่ขึ้นกับคอลัมน์อื่นที่ไม่ใช่ Primary Key (ไม่มี Transitive Dependency)

```
❌ ไม่ผ่าน 3NF:
orders(id, product_id, product_name, product_price)
-- product_name, product_price ควรอยู่ในตาราง products แยกต่างหาก

✅ ผ่าน 3NF:
orders(id, product_id)
products(id, name, price)
```

### เมื่อไหร่ควร Denormalize (จงใจให้ข้อมูลซ้ำซ้อน)?

บางครั้งใน Production เพื่อความเร็วในการอ่าน อาจยอม Denormalize บางส่วน เช่น เก็บ `product_name` ซ้ำไว้ใน `order_items` เพื่อไม่ต้อง JOIN ทุกครั้งที่แสดงประวัติออเดอร์ — เป็นการ Trade-off ระหว่างความเร็วกับความซ้ำซ้อน

---

## 13. Stored Procedures, Functions, Triggers

### Stored Procedure

```sql
DELIMITER //
CREATE PROCEDURE GetUsersByAge(IN minAge INT)
BEGIN
  SELECT * FROM users WHERE age >= minAge;
END //
DELIMITER ;

CALL GetUsersByAge(18);
```

### Function

```sql
DELIMITER //
CREATE FUNCTION CalculateDiscount(price DECIMAL(10,2), percent INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
  RETURN price - (price * percent / 100);
END //
DELIMITER ;

SELECT CalculateDiscount(1000, 10);   -- ผลลัพธ์: 900.00
```

### Trigger

```sql
CREATE TRIGGER before_order_insert
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
  SET NEW.created_at = NOW();
END;
```

> **ข้อควรระวัง:** Stored Procedure/Trigger ทำให้ Logic กระจายอยู่ในฐานข้อมูล ดูแลและ Debug ยากขึ้น ควรใช้เท่าที่จำเป็น เช่น งานที่ต้องการ Performance สูงมากหรือ Logic ที่ต้อง Atomic กับข้อมูลจริงๆ

---

## 14. Views

**View** คือ Query ที่ถูกบันทึกไว้เป็น "ตารางเสมือน" ใช้ซ้ำได้โดยไม่ต้องเขียน Query ยาวๆ ทุกครั้ง

```sql
CREATE VIEW active_users AS
SELECT id, name, email FROM users WHERE status = 'active';

-- ใช้งานเหมือนตารางปกติ
SELECT * FROM active_users WHERE name LIKE 'สม%';
```

**ประโยชน์:** ซ่อนความซับซ้อนของ Query, ควบคุมสิทธิ์การเข้าถึงข้อมูลบางส่วน (เช่น ไม่ให้เห็นคอลัมน์ password)

---

## 15. Replication

**Replication** คือการคัดลอกข้อมูลจากเครื่องหลัก (Primary/Master) ไปยังเครื่องรอง (Replica/Slave) เพื่อความพร้อมใช้งานสูงและกระจาย Load การอ่าน

```
        ┌──────────────┐
        │   Primary    │  ← รับ Write ทั้งหมด
        └──────┬───────┘
               │  (binlog replication)
      ┌────────┴────────┐
┌─────▼─────┐     ┌─────▼─────┐
│  Replica  │     │  Replica  │  ← รองรับ Read
└───────────┘     └───────────┘
```

### ตั้งค่าเบื้องต้น (สรุปแนวคิด)

**บนเครื่อง Primary:**
```sql
-- เปิด binary log ใน my.cnf: log-bin=mysql-bin, server-id=1
CREATE USER 'replica_user'@'%' IDENTIFIED BY 'password123';
GRANT REPLICATION SLAVE ON *.* TO 'replica_user'@'%';
```

**บนเครื่อง Replica:**
```sql
CHANGE MASTER TO
  MASTER_HOST='primary_host',
  MASTER_USER='replica_user',
  MASTER_PASSWORD='password123',
  MASTER_LOG_FILE='mysql-bin.000001',
  MASTER_LOG_POS=0;

START SLAVE;
SHOW SLAVE STATUS\G     -- ตรวจสอบสถานะ
```

### ประเภท Replication

- **Asynchronous** (ค่าเริ่มต้น) — เร็วแต่ Replica อาจตามหลัง Primary เล็กน้อย (Replication Lag)
- **Semi-synchronous** — Primary รอ Replica อย่างน้อย 1 ตัวยืนยันรับข้อมูลก่อน commit
- **Group Replication / MySQL InnoDB Cluster** — รองรับ Automatic Failover แบบสมบูรณ์กว่า

---

## 16. Performance Tuning

### 16.1 ใช้ `EXPLAIN` วิเคราะห์ Query

```sql
EXPLAIN SELECT * FROM orders WHERE status = 'pending' AND created_at > '2026-01-01';
```

### 16.2 หลักการทั่วไป

- เลือกเฉพาะคอลัมน์ที่ต้องใช้ (`SELECT name, email` แทน `SELECT *`)
- ใช้ `LIMIT` เมื่อไม่จำเป็นต้องดึงข้อมูลทั้งหมด
- หลีกเลี่ยง Query ที่ทำให้ Index ใช้งานไม่ได้ เช่น การใช้ฟังก์ชันครอบคอลัมน์ใน `WHERE` (`WHERE YEAR(created_at) = 2026` → ควรเปลี่ยนเป็นช่วงวันที่แทน)
- ใช้ **Connection Pooling** ฝั่งแอปพลิเคชัน
- พิจารณา **Query Caching / Application-level Caching** (เช่น Redis) สำหรับข้อมูลที่อ่านบ่อยแต่เปลี่ยนไม่บ่อย
- ตรวจสอบ **Slow Query Log**:

```sql
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;   -- บันทึก query ที่ช้ากว่า 1 วินาที
```

### 16.3 การ Optimize ตาราง

```sql
ANALYZE TABLE orders;    -- อัปเดตสถิติให้ Query Optimizer ตัดสินใจแผนการรัน query ได้ดีขึ้น
OPTIMIZE TABLE orders;   -- จัดระเบียบพื้นที่จัดเก็บ ลด Fragmentation
```

---

## 17. Security

### แนวทางความปลอดภัยพื้นฐานสำหรับ Production

1. **สร้าง User แยกตามสิทธิ์การใช้งาน** อย่าใช้ `root` ในแอปพลิเคชัน
```sql
CREATE USER 'app_user'@'%' IDENTIFIED BY 'strongPassword123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON shop_db.* TO 'app_user'@'%';
FLUSH PRIVILEGES;
```

2. **ป้องกัน SQL Injection** — ใช้ Prepared Statement/Parameterized Query เสมอ ห้ามต่อ String SQL ตรงๆ

```javascript
// ❌ อันตราย: SQL Injection ได้
const query = `SELECT * FROM users WHERE email = '${email}'`;

// ✅ ปลอดภัย: ใช้ Placeholder
connection.query('SELECT * FROM users WHERE email = ?', [email]);
```

3. **เข้ารหัสการเชื่อมต่อด้วย SSL/TLS**
4. **จำกัด Network Access** ด้วย Firewall / ไม่เปิด Port 3306 สู่ Internet โดยตรง
5. **เข้ารหัสข้อมูลอ่อนไหว** (เช่น รหัสผ่านต้อง hash ด้วย bcrypt ไม่ใช่เก็บ plain text)
6. **อัปเดต MySQL เป็นเวอร์ชันล่าสุดสม่ำเสมอ** เพื่อรับ security patch

---

## 18. Backup & Restore

### สำรองข้อมูล

```bash
mysqldump -u root -p shop_db > backup_2026-07-18.sql

# สำรองทุก database
mysqldump -u root -p --all-databases > full_backup.sql
```

### กู้คืนข้อมูล

```bash
mysql -u root -p shop_db < backup_2026-07-18.sql
```

### แนวทาง Production

- ตั้งเวลา Backup อัตโนมัติ (Cron job) และเก็บสำรองไว้หลายที่ (เช่น Cloud Storage)
- ทดสอบการ Restore เป็นระยะเพื่อให้มั่นใจว่า Backup ใช้งานได้จริง
- พิจารณาใช้ **Point-in-Time Recovery** ผ่าน Binary Log สำหรับระบบสำคัญ

---

## 19. การเชื่อมต่อจากภาษาโปรแกรม

### Node.js (ใช้ไลบรารี `mysql2`)

```javascript
const mysql = require('mysql2/promise');

async function main() {
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'app_user',
    password: 'strongPassword123!',
    database: 'shop_db'
  });

  const [rows] = await connection.execute(
    'SELECT * FROM users WHERE age >= ?', [18]
  );
  console.log(rows);

  await connection.end();
}

main().catch(console.error);
```

### Python (ใช้ไลบรารี `mysql-connector-python`)

```python
import mysql.connector

conn = mysql.connector.connect(
    host="localhost",
    user="app_user",
    password="strongPassword123!",
    database="shop_db"
)

cursor = conn.cursor()
cursor.execute("SELECT * FROM users WHERE age >= %s", (18,))

for row in cursor.fetchall():
    print(row)

cursor.close()
conn.close()
```

### ตัวอย่างการใช้ ORM (Sequelize ใน Node.js)

```javascript
const { Sequelize, DataTypes } = require('sequelize');

const sequelize = new Sequelize('shop_db', 'app_user', 'strongPassword123!', {
  host: 'localhost',
  dialect: 'mysql'
});

const User = sequelize.define('User', {
  name: { type: DataTypes.STRING, allowNull: false },
  email: { type: DataTypes.STRING, unique: true },
  age: DataTypes.INTEGER
});

await sequelize.sync();
const newUser = await User.create({ name: 'สมชาย', email: 'somchai@example.com', age: 28 });
```

---

## 20. Best Practices สำหรับ Production

1. **ออกแบบ Schema ให้ Normalize อย่างเหมาะสม** (ปกติถึง 3NF) แล้วค่อย Denormalize เฉพาะจุดที่จำเป็นเพื่อ Performance
2. **ใช้ Foreign Key เสมอ** เพื่อรักษาความถูกต้องของข้อมูล (Referential Integrity)
3. **สร้าง Index ให้ตรงกับ Query ที่ใช้บ่อยที่สุด** และตรวจสอบด้วย `EXPLAIN` เป็นประจำ
4. **ใช้ Prepared Statement เสมอ** เพื่อป้องกัน SQL Injection
5. **ใช้ Transaction** สำหรับ operation ที่ต้อง Atomic (เช่น การโอนเงิน)
6. **ตั้ง Replication** เพื่อความพร้อมใช้งานสูงและกระจาย Load การอ่าน
7. **Monitor Slow Query Log** และแก้ไข Query ที่ช้าอย่างสม่ำเสมอ
8. **Backup อัตโนมัติ + ทดสอบ Restore**
9. **จำกัดสิทธิ์ User ตาม Principle of Least Privilege**
10. **ใช้ Connection Pooling** และตั้งค่า Timeout ที่เหมาะสมในฝั่งแอปพลิเคชัน
11. **พิจารณาใช้ Caching Layer** (เช่น Redis) สำหรับข้อมูลที่อ่านบ่อยแต่เปลี่ยนไม่บ่อย เพื่อลดภาระฐานข้อมูล

---

## 21. คำสั่งที่ใช้บ่อย (Cheat Sheet)

```sql
-- Database & Table
SHOW DATABASES;
CREATE DATABASE mydb;
USE mydb;
SHOW TABLES;
DESCRIBE mytable;
DROP TABLE mytable;

-- CRUD
INSERT INTO t (col1, col2) VALUES (v1, v2);
SELECT * FROM t WHERE condition;
UPDATE t SET col1 = v1 WHERE condition;
DELETE FROM t WHERE condition;

-- JOIN
SELECT * FROM a INNER JOIN b ON a.id = b.a_id;
SELECT * FROM a LEFT JOIN b ON a.id = b.a_id;

-- Aggregate
SELECT COUNT(*), SUM(col), AVG(col) FROM t GROUP BY col2 HAVING COUNT(*) > 1;

-- Index
CREATE INDEX idx_name ON t(col);
SHOW INDEX FROM t;
DROP INDEX idx_name ON t;

-- Transaction
START TRANSACTION;
COMMIT;
ROLLBACK;

-- Server info
SHOW PROCESSLIST;
SHOW STATUS;
SHOW VARIABLES;
```

---

## สรุป

MySQL เป็นฐานข้อมูลเชิงสัมพันธ์ที่แข็งแกร่งด้านความถูกต้องของข้อมูล (ACID) และเหมาะกับข้อมูลที่มีโครงสร้างชัดเจนและสัมพันธ์กันซับซ้อน หัวใจสำคัญของการใช้งานจริงคือ **การออกแบบ Schema ให้ Normalize อย่างเหมาะสม**, **สร้าง Index และเขียน Query ให้มีประสิทธิภาพ**, **ใช้ Transaction อย่างถูกต้อง**, และ **วางระบบ Replication/Backup ให้รองรับการเติบโตและความพร้อมใช้งานสูง** ควบคู่กับการดูแลด้าน Security อย่างเคร่งครัด