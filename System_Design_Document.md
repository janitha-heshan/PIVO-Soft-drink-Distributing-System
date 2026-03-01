# PIVO Soft Drink Distributing System - System Design Document

## 1. Executive Summary
The PIVO System is a centralized web platform for managing soft drink distribution. It connects Factory Owners, Store Managers, Sales Reps, and Shop Owners to streamline inventory tracking, order fulfillment, and logistics.

---

## 2. Module-Level Design & Data Diagrams

### Module 1: Authentication & User Management
**Description:**  
Manages user identities, secure login, and role-based access control (RBAC). Also handles Shop registration profiles.

**Data Tables:**
*   `users`: Core credentials and roles.
*   `shops`: Extension of user data for Shop Owners.

**Data diagram (ERD):**
```mermaid
erDiagram
    USERS {
        int user_id PK
        string username
        string role "Enum: Admin, Manager, ShopOwner..."
        string password_hash
    }
    SHOPS {
        int shop_id PK
        int owner_id FK "Ref: users.user_id"
        string shop_name
        string address
        string contact_number
        decimal latitude
        decimal longitude
    }
    
    USERS ||--o| SHOPS : owns_profile
```

---

### Module 2: Product & Inventory Management
**Description:**  
Handles product definitions (name, price), dynamic bottle sizes, and real-time stock levels. Tracks all stock adjustments via logs.

**Data Tables:**
*   `products`: Master product list.
*   `sizes`: Normalized bottle volumes.
*   `inventory`: Current stock quantity.
*   `inventory_logs`: Audit trail of changes.

**Data Diagram (ERD):**
```mermaid
erDiagram
    SIZES {
        int size_id PK
        string volume_ml "e.g., 500ml, 1L"
    }
    PRODUCTS {
        int product_id PK
        string product_name
        decimal unit_price
        int size_id FK
    }
    INVENTORY {
        int inventory_id PK
        int product_id FK
        int quantity_in_stock
    }
    INVENTORY_LOGS {
        int log_id PK
        int product_id FK
        int change_amount "+/- qty"
        string reason
        datetime timestamp
    }

    SIZES ||--o{ PRODUCTS : defines_volume
    PRODUCTS ||--o| INVENTORY : has_stock
    PRODUCTS ||--o{ INVENTORY_LOGS : tracked_in
```

---

### Module 3: Ordering & Payments
**Description:**  
Allows Shop Owners to place orders. Managers confirm these orders, which triggers payment recording and status updates.

**Data Tables:**
*   `orders`: Order header (Status, Total).
*   `order_items`: Line items.
*   `payments`: Financial records.
*   `order_tracking_logs`: History of status changes (Pending -> Preparing -> Dispatched).

**Data Diagram (ERD):**
```mermaid
erDiagram
    SHOPS ||--o{ ORDERS : places
    ORDERS {
        int order_id PK
        int shop_id FK
        decimal total_amount
        string delivery_status "Pending, Preparing, etc"
        datetime order_date
    }
    ORDER_ITEMS {
        int order_item_id PK
        int order_id FK
        int product_id FK
        int quantity
        decimal price_at_order
    }
    PAYMENTS {
        int payment_id PK
        int order_id FK
        decimal amount_paid
        string payment_method
        datetime payment_date
    }
    ORDER_TRACKING_LOGS {
        int log_id PK
        int order_id FK
        string status "New Status"
        int changed_by "User ID"
    }

    ORDERS ||--o{ ORDER_ITEMS : contains
    ORDERS ||--o{ PAYMENTS : confirmed_by
    ORDERS ||--o{ ORDER_TRACKING_LOGS : history
```

---

### Module 4: Product Returns
**Description:**  
Manages reverse logistics. Managers log returned items (damaged/expired), which updates inventory and creates a return record.

**Data Tables:**
*   `product_returns`: Details of the return.

**Data Diagram (ERD):**
```mermaid
erDiagram
    ORDERS ||--o{ PRODUCT_RETURNS : source
    PRODUCTS ||--o{ PRODUCT_RETURNS : item
    
    PRODUCT_RETURNS {
        int return_id PK
        int order_id FK
        int product_id FK
        int quantity
        string reason
        string status
        datetime return_date
    }
```

---

## 3. System Architecture

### Technology Stack
*   **Frontend:** HTML5, CSS3, JavaScript (Vanilla).
*   **Backend:** PHP 8.0 (PDO for Database Abstraction).
*   **Database:** MySQL (Relational).

### Overall Block Diagram
```mermaid
graph TD
    subgraph "Frontend Layer"
        UI[User Interface (Browser)]
    end
    
    subgraph "Application Layer"
        Auth[Auth Module]
        Ord[Order Engine]
        Inv[Inventory Engine]
        Ret[Returns Engine]
    end
    
    subgraph "Data Layer"
        DB[(MySQL Database)]
    end

    UI --> Auth
    UI --> Ord
    UI --> Inv
    UI --> Ret
    
    Auth --> DB
    Ord --> DB
    Inv --> DB
    Ret --> DB
```

---

## 4. Overall Database Schema (Global View)
```mermaid
erDiagram
    USERS ||--o{ SHOPS : owns
    USERS ||--o{ ORDERS : manages_logs
    SHOPS ||--o{ ORDERS : places
    ORDERS ||--o{ ORDER_ITEMS : contains
    PRODUCTS ||--o{ ORDER_ITEMS : listed_in
    PRODUCTS }o--|| SIZES : has_size
    PRODUCTS ||--o{ INVENTORY : stock
    ORDERS ||--o{ PAYMENTS : generates
    ORDERS ||--o{ PRODUCT_RETURNS : has_returns
```
