# คู่มือ MongoDB ฉบับสมบูรณ์ (พื้นฐาน → ระดับใช้งานจริง)

> เอกสารนี้รวบรวมความรู้ MongoDB ตั้งแต่แนวคิดพื้นฐานไปจนถึงเทคนิคที่ใช้จริงในระบบ Production พร้อมตัวอย่างโค้ดประกอบทุกหัวข้อ

---

## สารบัญ

1. [MongoDB คืออะไร](#1-mongodb-คืออะไร)
2. [แนวคิดพื้นฐาน: Database, Collection, Document](#2-แนวคิดพื้นฐาน-database-collection-document)
3. [การติดตั้งและเริ่มต้นใช้งาน](#3-การติดตั้งและเริ่มต้นใช้งาน)
4. [CRUD พื้นฐาน](#4-crud-พื้นฐาน)
5. [Query Operators](#5-query-operators)
6. [Aggregation Framework](#6-aggregation-framework)
7. [Indexing](#7-indexing)
8. [Schema Design](#8-schema-design)
9. [Relationships: Embedding vs Referencing](#9-relationships-embedding-vs-referencing)
10. [Transactions](#10-transactions)
11. [Replication (Replica Set)](#11-replication-replica-set)
12. [Sharding](#12-sharding)
13. [Performance Tuning](#13-performance-tuning)
14. [Security](#14-security)
15. [Backup & Restore](#15-backup--restore)
16. [Monitoring](#16-monitoring)
17. [การเชื่อมต่อจากภาษาโปรแกรม (Node.js / Python)](#17-การเชื่อมต่อจากภาษาโปรแกรม)
18. [Best Practices สำหรับ Production](#18-best-practices-สำหรับ-production)
19. [คำสั่งที่ใช้บ่อย (Cheat Sheet)](#19-คำสั่งที่ใช้บ่อย-cheat-sheet)

---

## 1. MongoDB คืออะไร

**MongoDB** คือฐานข้อมูลประเภท **NoSQL (Not Only SQL)** แบบ **Document-Oriented** ที่เก็บข้อมูลในรูปแบบคล้าย JSON เรียกว่า **BSON (Binary JSON)**

### เปรียบเทียบกับ SQL แบบดั้งเดิม

| SQL (เช่น MySQL) | MongoDB |
|---|---|
| Database | Database |
| Table | Collection |
| Row | Document |
| Column | Field |
| Primary Key | `_id` |
| JOIN | `$lookup` หรือ Embedding |
| Schema ตายตัว | Schema ยืดหยุ่น (Schema-less) |

### จุดเด่นของ MongoDB

- **Schema ยืดหยุ่น** — แต่ละ document ในคอลเลกชันเดียวกันมีโครงสร้างต่างกันได้
- **Scale ได้ง่าย** — รองรับ Horizontal Scaling ผ่าน Sharding
- **เร็วสำหรับงานอ่าน/เขียนจำนวนมาก** — เหมาะกับข้อมูลปริมาณสูง
- **โครงสร้างใกล้เคียงกับ Object ในโปรแกรม** — ลด Impedance Mismatch ระหว่าง DB กับโค้ด

### เหมาะกับงานแบบไหน

- แอปที่ข้อมูลมีโครงสร้างเปลี่ยนแปลงบ่อย (เช่น Content Management, Catalog สินค้า)
- ระบบ Real-time Analytics, Logging
- แอปที่ต้องการ Scale แนวนอนในอนาคต

### ไม่เหมาะกับงานแบบไหน

- ระบบที่ต้องการ Transaction ซับซ้อนข้ามหลายตารางแบบเข้มงวด (เช่น ระบบธนาคารแบบดั้งเดิม) — แม้ MongoDB จะรองรับ Transaction แล้ว แต่ SQL ยังทำได้เป็นธรรมชาติกว่า
- ข้อมูลที่มีความสัมพันธ์ซับซ้อนมาก (Many-to-Many หนาแน่น) แบบที่ต้อง JOIN ตลอดเวลา

---

## 2. แนวคิดพื้นฐาน: Database, Collection, Document

```
MongoDB Server
 └── Database (เช่น "shop_db")
      └── Collection (เช่น "users")
           └── Document (เช่น ผู้ใช้ 1 คน)
```

### ตัวอย่าง Document

```json
{
  "_id": "64f1a2b3c4d5e6f7a8b9c0d1",
  "name": "สมชาย ใจดี",
  "age": 28,
  "email": "somchai@example.com",
  "tags": ["premium", "newsletter"],
  "address": {
    "city": "กรุงเทพฯ",
    "zipcode": "10110"
  },
  "createdAt": "2026-07-18T10:00:00Z"
}
```

**จุดสังเกต:**
- `_id` คือ Primary Key ที่ MongoDB สร้างให้อัตโนมัติ (ประเภท `ObjectId`) ถ้าไม่ระบุเอง
- ฟิลด์ `address` เป็น **Embedded Document** (ข้อมูลซ้อนอยู่ภายใน)
- ฟิลด์ `tags` เป็น **Array**
- แต่ละ Document ในคอลเลกชันเดียวกัน **ไม่จำเป็นต้องมีฟิลด์เหมือนกันทุกอัน**

---

## 3. การติดตั้งและเริ่มต้นใช้งาน

### วิธีติดตั้ง (สรุปแนวทางหลัก)

1. **ติดตั้งในเครื่อง (Local)** — ดาวน์โหลด MongoDB Community Server จากเว็บไซต์ทางการ
2. **ใช้ Docker** (สะดวกที่สุดสำหรับทดสอบ):

```bash
docker run -d --name mongodb -p 27017:27017 \
  -e MONGO_INITDB_ROOT_USERNAME=admin \
  -e MONGO_INITDB_ROOT_PASSWORD=password123 \
  mongo:latest
```

3. **ใช้ MongoDB Atlas** — บริการ Cloud แบบ Managed ของ MongoDB เอง เหมาะกับการเริ่มต้นและ Production โดยไม่ต้องดูแล Infra เอง

### เชื่อมต่อผ่าน mongosh (Mongo Shell)

```bash
mongosh "mongodb://localhost:27017"
```

```bash
# เชื่อมต่อ Atlas
mongosh "mongodb+srv://cluster0.xxxxx.mongodb.net/" --username myUser
```

### คำสั่งเริ่มต้นใน Shell

```javascript
show dbs                  // แสดงรายชื่อ database ทั้งหมด
use shop_db               // สลับ/สร้าง database
show collections          // แสดง collection ทั้งหมดใน db ปัจจุบัน
db.stats()                // สถิติของ database
```

---

## 4. CRUD พื้นฐาน

### 4.1 Create (เพิ่มข้อมูล)

```javascript
// เพิ่มทีละ 1 document
db.users.insertOne({
  name: "สมหญิง รักเรียน",
  age: 25,
  email: "somying@example.com"
})

// เพิ่มหลาย document พร้อมกัน
db.users.insertMany([
  { name: "A", age: 20 },
  { name: "B", age: 22 }
])
```

### 4.2 Read (อ่านข้อมูล)

```javascript
// ดึงทั้งหมด
db.users.find()

// ดึงแบบมีเงื่อนไข
db.users.find({ age: { $gte: 25 } })

// ดึงเอกสารเดียว
db.users.findOne({ email: "somying@example.com" })

// เลือกเฉพาะบางฟิลด์ (Projection)
db.users.find({ age: { $gte: 25 } }, { name: 1, email: 1, _id: 0 })

// เรียงลำดับ + จำกัดจำนวน
db.users.find().sort({ age: -1 }).limit(10)
```

### 4.3 Update (แก้ไขข้อมูล)

```javascript
// แก้ไข document เดียว
db.users.updateOne(
  { email: "somying@example.com" },
  { $set: { age: 26 } }
)

// แก้ไขหลาย document
db.users.updateMany(
  { age: { $lt: 18 } },
  { $set: { status: "minor" } }
)

// ถ้าไม่เจอ ให้สร้างใหม่ (Upsert)
db.users.updateOne(
  { email: "new@example.com" },
  { $set: { name: "New User" } },
  { upsert: true }
)

// แทนที่ document ทั้งฉบับ
db.users.replaceOne(
  { email: "somying@example.com" },
  { name: "สมหญิง", age: 26, email: "somying@example.com" }
)
```

### 4.4 Delete (ลบข้อมูล)

```javascript
db.users.deleteOne({ email: "somying@example.com" })
db.users.deleteMany({ age: { $lt: 18 } })
```

---

## 5. Query Operators

### Comparison Operators

| Operator | ความหมาย | ตัวอย่าง |
|---|---|---|
| `$eq` | เท่ากับ | `{ age: { $eq: 25 } }` |
| `$ne` | ไม่เท่ากับ | `{ age: { $ne: 25 } }` |
| `$gt` / `$gte` | มากกว่า / มากกว่าเท่ากับ | `{ age: { $gt: 18 } }` |
| `$lt` / `$lte` | น้อยกว่า / น้อยกว่าเท่ากับ | `{ age: { $lt: 60 } }` |
| `$in` | อยู่ในกลุ่มค่า | `{ status: { $in: ["A", "B"] } }` |
| `$nin` | ไม่อยู่ในกลุ่มค่า | `{ status: { $nin: ["C"] } }` |

### Logical Operators

```javascript
// AND (ค่าเริ่มต้นเมื่อใส่หลายเงื่อนไข)
db.users.find({ age: { $gte: 18 }, status: "active" })

// OR
db.users.find({ $or: [{ age: { $lt: 18 } }, { status: "vip" }] })

// AND + OR ผสมกัน
db.users.find({
  status: "active",
  $or: [{ age: { $lt: 18 } }, { age: { $gt: 60 } }]
})

// NOT
db.users.find({ age: { $not: { $gte: 18 } } })
```

### Element & Array Operators

```javascript
// ฟิลด์ต้องมีอยู่จริง
db.users.find({ phone: { $exists: true } })

// ตรวจสอบชนิดข้อมูล
db.users.find({ age: { $type: "int" } })

// Array มีค่าที่ต้องการ
db.products.find({ tags: "sale" })

// Array มีค่าทุกตัวที่ระบุ
db.products.find({ tags: { $all: ["sale", "new"] } })

// จำนวนสมาชิกใน Array
db.products.find({ tags: { $size: 3 } })

// ค้นหาแบบ Regex (คล้าย LIKE)
db.users.find({ name: { $regex: "^สม", $options: "i" } })
```

---

## 6. Aggregation Framework

Aggregation Pipeline คือระบบประมวลผลข้อมูลแบบ "ท่อ" (Pipeline) ที่ข้อมูลไหลผ่านหลายขั้นตอน (Stage) ต่อเนื่องกัน — ใช้แทน `GROUP BY`, `JOIN`, และการคำนวณซับซ้อนใน SQL

### โครงสร้างพื้นฐาน

```javascript
db.orders.aggregate([
  { $match: { status: "completed" } },      // กรองข้อมูล (เหมือน WHERE)
  { $group: {                                 // จัดกลุ่ม (เหมือน GROUP BY)
      _id: "$customerId",
      totalSpent: { $sum: "$amount" },
      orderCount: { $sum: 1 }
  }},
  { $sort: { totalSpent: -1 } },              // เรียงลำดับ
  { $limit: 10 }                              // จำกัดจำนวน
])
```

### Stage ที่ใช้บ่อย

| Stage | หน้าที่ |
|---|---|
| `$match` | กรองข้อมูล (คล้าย `find`) |
| `$group` | จัดกลุ่มและคำนวณสรุปผล |
| `$sort` | เรียงลำดับ |
| `$project` | เลือก/สร้างฟิลด์ใหม่ |
| `$limit` / `$skip` | จำกัดจำนวน / ข้าม |
| `$lookup` | เชื่อมข้อมูลข้าม Collection (คล้าย JOIN) |
| `$unwind` | แตก Array ให้เป็นหลาย Document |

### ตัวอย่าง `$lookup` (JOIN)

```javascript
db.orders.aggregate([
  {
    $lookup: {
      from: "customers",       // collection ที่จะ join
      localField: "customerId",
      foreignField: "_id",
      as: "customerInfo"
    }
  },
  { $unwind: "$customerInfo" }   // แปลง array 1 ตัวให้เป็น object
])
```

### ตัวอย่างการคำนวณยอดขายรายเดือน

```javascript
db.orders.aggregate([
  { $match: { status: "completed" } },
  {
    $group: {
      _id: { $dateToString: { format: "%Y-%m", date: "$createdAt" } },
      totalSales: { $sum: "$amount" },
      avgOrderValue: { $avg: "$amount" }
    }
  },
  { $sort: { _id: 1 } }
])
```

---

## 7. Indexing

Index ช่วยให้การค้นหาเร็วขึ้นมาก โดยไม่ต้องสแกนทุก Document (Collection Scan)

### สร้าง Index

```javascript
// Single Field Index
db.users.createIndex({ email: 1 })   // 1 = ascending, -1 = descending

// Compound Index (หลายฟิลด์)
db.orders.createIndex({ customerId: 1, createdAt: -1 })

// Unique Index (ห้ามค่าซ้ำ)
db.users.createIndex({ email: 1 }, { unique: true })

// Text Index (สำหรับค้นหาข้อความ)
db.articles.createIndex({ title: "text", content: "text" })

// TTL Index (ลบข้อมูลอัตโนมัติหลังเวลาที่กำหนด — เหมาะกับ session/log)
db.sessions.createIndex({ createdAt: 1 }, { expireAfterSeconds: 3600 })
```

### ตรวจสอบ Index ที่มี

```javascript
db.users.getIndexes()
```

### ตรวจสอบว่า Query ใช้ Index หรือไม่

```javascript
db.users.find({ email: "test@example.com" }).explain("executionStats")
```

ดูค่า `winningPlan.stage`:
- `IXSCAN` = ใช้ Index (ดี)
- `COLLSCAN` = สแกนทั้ง Collection (ควรหลีกเลี่ยงในตารางใหญ่)

### หลักการเลือก Index

- Index บนฟิลด์ที่ใช้ค้นหาบ่อย (`$match`, `find`) และใช้เรียงลำดับ (`sort`)
- Compound Index ควรเรียงฟิลด์ตามลำดับ **Equality → Sort → Range** (กฎ ESR)
- อย่าสร้าง Index เกินความจำเป็น เพราะแต่ละ Index กิน RAM และทำให้การเขียนข้อมูลช้าลง

---

## 8. Schema Design

แม้ MongoDB จะไม่บังคับ Schema แต่การออกแบบโครงสร้างข้อมูลที่ดียังสำคัญมาก

### แนวทางออกแบบด้วย Schema Validation (บังคับกฎบางอย่าง)

```javascript
db.createCollection("users", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["name", "email"],
      properties: {
        name: { bsonType: "string" },
        email: { bsonType: "string", pattern: "^.+@.+$" },
        age: { bsonType: "int", minimum: 0 }
      }
    }
  }
})
```

### หลักคิดในการออกแบบ

1. **ออกแบบตาม Query Pattern ไม่ใช่ตามโครงสร้างข้อมูลดิบ** — คิดก่อนว่าแอปจะ "อ่าน" ข้อมูลแบบไหนบ่อยที่สุด แล้วออกแบบให้ query นั้นเร็วที่สุด
2. **ข้อมูลที่อ่านพร้อมกันบ่อย ควรอยู่ Document เดียวกัน** (Embed)
3. **ข้อมูลที่โตไม่จำกัดหรือใช้ร่วมกันหลายที่ ควรแยก Collection** (Reference)

---

## 9. Relationships: Embedding vs Referencing

### 9.1 Embedding (ฝังข้อมูลไว้ใน Document เดียว)

```json
{
  "_id": "order001",
  "customerName": "สมชาย",
  "items": [
    { "product": "เสื้อยืด", "qty": 2, "price": 250 },
    { "product": "กางเกง", "qty": 1, "price": 450 }
  ]
}
```

**ใช้เมื่อ:** ข้อมูลมีความสัมพันธ์แบบ "เป็นส่วนหนึ่งของ" (1-to-Few) และอ่านพร้อมกันเสมอ เช่น ที่อยู่ของผู้ใช้, รายการสินค้าในออเดอร์

**ข้อดี:** อ่านข้อมูลครั้งเดียวจบ ไม่ต้อง join, เร็ว
**ข้อเสีย:** ถ้าข้อมูลฝังโตไม่จำกัด (เช่น คอมเมนต์นับหมื่น) จะทำให้ Document ใหญ่เกินไป (ขีดจำกัด 16MB/document)

### 9.2 Referencing (อ้างอิงด้วย ID แยก Collection)

```json
// collection: orders
{ "_id": "order001", "customerId": "cust001", "items": [...] }

// collection: customers
{ "_id": "cust001", "name": "สมชาย", "email": "somchai@example.com" }
```

ดึงข้อมูลร่วมกันด้วย `$lookup` (ดูหัวข้อ Aggregation)

**ใช้เมื่อ:** ข้อมูลมีความสัมพันธ์แบบ 1-to-Many จำนวนมาก หรือ Many-to-Many, หรือข้อมูลถูกใช้ร่วมกันในหลายที่และเปลี่ยนแปลงอิสระจากกัน

### สรุปการเลือกใช้

| สถานการณ์ | แนะนำ |
|---|---|
| 1-to-Few (เช่น ที่อยู่ 2-3 แห่ง) | Embed |
| 1-to-Many ขนาดใหญ่ (เช่น โพสต์กับคอมเมนต์นับพัน) | Reference |
| Many-to-Many | Reference |
| ข้อมูลเปลี่ยนบ่อยและใช้ร่วมกันหลายที่ | Reference |
| ข้อมูลอ่านพร้อมกันเสมอ ไม่ค่อยเปลี่ยน | Embed |

---

## 10. Transactions

MongoDB รองรับ **Multi-document ACID Transactions** ตั้งแต่เวอร์ชัน 4.0 เป็นต้นไป (ต้องใช้กับ Replica Set หรือ Sharded Cluster)

```javascript
const session = db.getMongo().startSession()
session.startTransaction()

try {
  const accounts = session.getDatabase("bank").accounts
  accounts.updateOne({ _id: "A" }, { $inc: { balance: -100 } })
  accounts.updateOne({ _id: "B" }, { $inc: { balance: 100 } })

  session.commitTransaction()
} catch (error) {
  session.abortTransaction()
  throw error
} finally {
  session.endSession()
}
```

**ข้อควรระวัง:** Transaction มี Overhead ด้านประสิทธิภาพ ควรใช้เท่าที่จำเป็นจริงๆ (เช่น ระบบโอนเงิน) — ถ้าออกแบบ Schema ให้ Embed ข้อมูลที่เกี่ยวข้องกันได้ มักจะเลี่ยง Transaction ได้เลย เพราะการอัปเดต Document เดียวเป็น Atomic โดยธรรมชาติอยู่แล้ว

---

## 11. Replication (Replica Set)

**Replica Set** คือกลุ่มของ MongoDB Server ที่เก็บข้อมูลชุดเดียวกัน เพื่อความพร้อมใช้งานสูง (High Availability) และป้องกันข้อมูลสูญหาย

### โครงสร้าง

```
        ┌──────────────┐
        │   Primary    │  ← รับ Write ทั้งหมด
        └──────┬───────┘
      ┌─────────┴─────────┐
┌─────▼─────┐       ┌─────▼─────┐
│ Secondary │       │ Secondary │  ← Sync ข้อมูลจาก Primary, รองรับ Read
└───────────┘       └───────────┘
```

- ถ้า **Primary ล่ม** ระบบจะเลือก Secondary ตัวใหม่ขึ้นเป็น Primary อัตโนมัติ (Automatic Failover)
- แนะนำให้มีอย่างน้อย **3 nodes** (เพื่อให้มี Quorum ในการโหวตเลือก Primary)

### ตั้งค่าเบื้องต้น

```javascript
rs.initiate({
  _id: "myReplicaSet",
  members: [
    { _id: 0, host: "mongo1:27017" },
    { _id: 1, host: "mongo2:27017" },
    { _id: 2, host: "mongo3:27017" }
  ]
})

rs.status()   // ตรวจสอบสถานะ
```

### Connection String สำหรับ Replica Set

```
mongodb://mongo1:27017,mongo2:27017,mongo3:27017/mydb?replicaSet=myReplicaSet
```

---

## 12. Sharding

**Sharding** คือการกระจายข้อมูลออกเป็นหลายเครื่อง (Horizontal Scaling) เมื่อข้อมูลใหญ่เกินกว่าเครื่องเดียวจะรับไหว

### องค์ประกอบ

```
        Client
          │
    ┌─────▼─────┐
    │  mongos    │  ← Query Router
    └─────┬─────┘
          │
    ┌─────▼──────────────────┐
    │  Config Servers          │  ← เก็บ metadata ว่าข้อมูลอยู่ shard ไหน
    └──────────────────────────┘
          │
  ┌───────┼───────┐
┌─▼──┐  ┌─▼──┐  ┌─▼──┐
│Shard1│ │Shard2│ │Shard3│  ← แต่ละ Shard คือ Replica Set ย่อย
└────┘  └────┘  └────┘
```

### เลือก Shard Key

Shard Key คือฟิลด์ที่ใช้ตัดสินว่าข้อมูลไปอยู่ Shard ไหน — เลือกผิดจะทำให้ข้อมูลกระจุกตัว (Hot Shard)

```javascript
sh.enableSharding("shop_db")

sh.shardCollection("shop_db.orders", { customerId: "hashed" })
```

**หลักการเลือก Shard Key ที่ดี:**
- มีค่าหลากหลายสูง (High Cardinality)
- กระจาย Write ได้สม่ำเสมอ ไม่กระจุกที่ค่าใดค่าหนึ่ง
- ตรงกับรูปแบบ Query ที่ใช้บ่อย เพื่อให้ mongos ส่ง query ไปแค่ Shard ที่เกี่ยวข้อง

> Sharding เหมาะกับระบบที่มีข้อมูลระดับหลายร้อย GB ขึ้นไปเท่านั้น — ถ้าข้อมูลยังไม่ใหญ่มาก Replica Set เพียงอย่างเดียวก็เพียงพอ

---

## 13. Performance Tuning

### 13.1 ใช้ `explain()` วิเคราะห์ Query

```javascript
db.orders.find({ status: "pending" }).explain("executionStats")
```

ดูค่าเหล่านี้:
- `totalDocsExamined` ควรใกล้เคียงกับ `nReturned` (ถ้าห่างมากแปลว่า Index ไม่ตรงกับ Query)
- `executionTimeMillis`

### 13.2 หลักการทั่วไป

- ใช้ **Projection** ดึงเฉพาะฟิลด์ที่ต้องใช้ ไม่ใช่ทั้ง Document
- หลีกเลี่ยง Query ที่ทำให้เกิด `COLLSCAN` ในตารางขนาดใหญ่
- ใช้ `limit()` เสมอเมื่อไม่จำเป็นต้องดึงข้อมูลทั้งหมด
- ระวัง `$regex` ที่ไม่ได้ขึ้นต้นด้วย `^` เพราะใช้ Index ไม่ได้เต็มประสิทธิภาพ
- ใช้ **Connection Pooling** ในฝั่งแอปพลิเคชัน (Driver ส่วนใหญ่ทำให้อัตโนมัติ)
- พิจารณา **Read Preference** เพื่อกระจาย Load การอ่านไปที่ Secondary (สำหรับข้อมูลที่ไม่ต้อง Real-time เป๊ะ)

```javascript
db.orders.find().readPref("secondaryPreferred")
```

---

## 14. Security

### แนวทางความปลอดภัยพื้นฐานสำหรับ Production

1. **เปิดใช้ Authentication เสมอ** — อย่าเปิด MongoDB แบบไม่มีรหัสผ่านสู่ Internet
```javascript
use admin
db.createUser({
  user: "appUser",
  pwd: "strongPassword123!",
  roles: [{ role: "readWrite", db: "shop_db" }]
})
```

2. **ใช้ Role-Based Access Control (RBAC)** — ให้สิทธิ์เท่าที่จำเป็น (Principle of Least Privilege)
3. **เข้ารหัสการเชื่อมต่อด้วย TLS/SSL**
4. **จำกัด Network Access** ด้วย Firewall / IP Whitelist (โดยเฉพาะบน Atlas ต้องตั้งค่า Network Access)
5. **เข้ารหัสข้อมูลที่ Rest** (Encryption at Rest) สำหรับข้อมูลอ่อนไหว
6. **อย่า hardcode credentials ในโค้ด** — ใช้ Environment Variables หรือ Secret Manager

---

## 15. Backup & Restore

### สำรองข้อมูล

```bash
mongodump --uri="mongodb://localhost:27017" --db=shop_db --out=/backup/2026-07-18
```

### กู้คืนข้อมูล

```bash
mongorestore --uri="mongodb://localhost:27017" --db=shop_db /backup/2026-07-18/shop_db
```

### แนวทาง Production

- ตั้งเวลา Backup อัตโนมัติ (Cron job หรือใช้ฟีเจอร์ Automated Backup ของ Atlas)
- ทดสอบการ Restore เป็นระยะ อย่ารอจนถึงวันที่ข้อมูลพังจริงแล้วค่อยรู้ว่า Backup ใช้ไม่ได้
- พิจารณาใช้ **Point-in-Time Recovery** สำหรับระบบสำคัญ

---

## 16. Monitoring

สิ่งที่ควรติดตามในระบบ Production:

- **จำนวน Connection ปัจจุบัน** เทียบกับ Connection Pool สูงสุด
- **Query ที่ทำงานช้า (Slow Query)** — เปิด Profiler:
```javascript
db.setProfilingLevel(1, { slowms: 100 })   // บันทึก query ที่ช้ากว่า 100ms
db.system.profile.find().sort({ ts: -1 }).limit(10)
```
- **การใช้ RAM/Disk/CPU** ของเซิร์ฟเวอร์
- **Replication Lag** ระหว่าง Primary กับ Secondary
- เครื่องมือที่นิยมใช้: **MongoDB Atlas Monitoring**, **MongoDB Compass**, **Prometheus + Grafana** (ผ่าน mongodb_exporter)

---

## 17. การเชื่อมต่อจากภาษาโปรแกรม

### Node.js (ใช้ไลบรารี `mongodb`)

```javascript
const { MongoClient } = require("mongodb");

const uri = "mongodb://localhost:27017";
const client = new MongoClient(uri);

async function main() {
  await client.connect();
  const db = client.db("shop_db");
  const users = db.collection("users");

  // Insert
  await users.insertOne({ name: "สมชาย", age: 28 });

  // Find
  const result = await users.find({ age: { $gte: 18 } }).toArray();
  console.log(result);

  await client.close();
}

main().catch(console.error);
```

### Python (ใช้ไลบรารี `pymongo`)

```python
from pymongo import MongoClient

client = MongoClient("mongodb://localhost:27017")
db = client["shop_db"]
users = db["users"]

# Insert
users.insert_one({"name": "สมชาย", "age": 28})

# Find
for user in users.find({"age": {"$gte": 18}}):
    print(user)

client.close()
```

### ตัวอย่างการใช้ Mongoose (ODM ยอดนิยมใน Node.js)

```javascript
const mongoose = require("mongoose");

await mongoose.connect("mongodb://localhost:27017/shop_db");

const userSchema = new mongoose.Schema({
  name: { type: String, required: true },
  email: { type: String, required: true, unique: true },
  age: Number
});

const User = mongoose.model("User", userSchema);

const newUser = await User.create({ name: "สมชาย", email: "somchai@example.com", age: 28 });
```

---

## 18. Best Practices สำหรับ Production

1. **ออกแบบ Schema ตาม Query Pattern** ไม่ใช่แค่ตามความสัมพันธ์ของข้อมูล
2. **สร้าง Index ให้ตรงกับ Query ที่ใช้บ่อยที่สุด** และหมั่นตรวจสอบด้วย `explain()`
3. **ใช้ Replica Set เสมอใน Production** เพื่อความพร้อมใช้งานสูง
4. **จำกัดขนาด Document** ไม่ให้ใกล้ขีดจำกัด 16MB — ถ้าใกล้ ให้พิจารณาแยก Collection
5. **ใช้ Connection Pooling** และตั้งค่า Timeout ให้เหมาะสม
6. **เปิด Authentication + TLS เสมอ**
7. **Monitor Slow Query และ Replication Lag** อย่างสม่ำเสมอ
8. **Backup อัตโนมัติ + ทดสอบ Restore**
9. **ใช้ Schema Validation** เพื่อป้องกันข้อมูลผิดรูปแบบ แม้ MongoDB จะยืดหยุ่นก็ตาม
10. **วางแผน Sharding ล่วงหน้า** ถ้าคาดว่าข้อมูลจะโตเกินขีดความสามารถของเครื่องเดียวในอนาคต

---

## 19. คำสั่งที่ใช้บ่อย (Cheat Sheet)

```javascript
// Database & Collection
show dbs
use mydb
show collections
db.createCollection("myCollection")
db.myCollection.drop()

// CRUD
db.col.insertOne({...})
db.col.find({...})
db.col.updateOne({...}, { $set: {...} })
db.col.deleteOne({...})

// Count
db.col.countDocuments({ status: "active" })

// Distinct values
db.col.distinct("status")

// Index
db.col.createIndex({ field: 1 })
db.col.getIndexes()
db.col.dropIndex("field_1")

// Aggregation
db.col.aggregate([{ $match: {...} }, { $group: {...} }])

// Server info
db.serverStatus()
db.stats()
rs.status()
```

---

## สรุป

MongoDB เป็นฐานข้อมูล NoSQL ที่ยืดหยุ่นและ Scale ได้ดี เหมาะกับแอปพลิเคชันสมัยใหม่ที่ข้อมูลมีโครงสร้างเปลี่ยนแปลงได้และต้องการความเร็วสูง หัวใจสำคัญของการใช้งานจริงคือ **การออกแบบ Schema ให้สอดคล้องกับรูปแบบการ Query**, **สร้าง Index ให้เหมาะสม**, และ **วางระบบ Replication/Sharding ให้รองรับการเติบโต** ควบคู่กับการดูแลด้าน Security และ Monitoring อย่างสม่ำเสมอ
