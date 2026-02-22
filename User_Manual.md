# PIVO Supply Chain & Distribution System
## Comprehensive End-to-End User Manual & User Journeys

Welcome to the PIVO system user manual! This document provides a complete guide to using the PIVO platform, covering every process, user role, and end-to-end journey from product creation to final delivery.

***

### Table of Contents
1. [Introduction to PIVO](#1-introduction-to-pivo)
2. [Role-Specific Guides & Instructions](#2-role-specific-guides--instructions)
   - [2.1 Factory Owner](#21-factory-owner)
   - [2.2 Store Manager](#22-store-manager)
   - [2.3 Shop Owner](#23-shop-owner)
   - [2.4 Sales Representative (Logistics Driver)](#24-sales-representative-logistics-driver)
   - [2.5 Administrator](#25-administrator)
3. [User Journey 1: Factory Production & Catalog Management](#3-user-journey-1-factory-production--catalog-management)
4. [User Journey 2: Store Management & Inventory Preparation](#4-user-journey-2-store-management--inventory-preparation)
5. [User Journey 3: Shop Owner Ordering & Returns](#5-user-journey-3-shop-owner-ordering--returns)
6. [User Journey 4: Sales Representative Dispatch & Delivery](#6-user-journey-4-sales-representative-dispatch--delivery)
7. [User Journey 5: Administrator System Oversight](#7-user-journey-5-administrator-system-oversight)
8. [Advanced Technical Features](#8-advanced-technical-features)

***

### 1. Introduction to PIVO
PIVO is a state-of-the-art dispatch and supply chain management system designed to streamline soft drink distribution. It connects primary manufacturers (Factories) with central storage (Store Managers), dispatchers (Sales Reps), and the final point of sale (Shop Owners).

***

### 2. Role-Specific Guides & Instructions

#### 2.1 Factory Owner
The Factory Owner maintains the highest-level business overview. They manage the primary product catalog, view vast data insights, and oversee operations.

**How to Manage Products:**
1. Navigate to the **Products** tab via the action cards on the Dashboard or the top navigation bar.
2. Click **Add New Product +** to launch a new beverage size.
3. Fill in the details (Name, Size, Price, Description, Product Image Upload). *Note: Typing a completely new Category or Size will dynamically update the system's core memory ENUMs.*
4. To modify an existing product, click **Edit** on the table row, change the details in the popup modal, and click **Save Changes**.

**How to View Business Analytics:**
1. Navigate to the **Business Insights** tab from your dashboard.
2. Scroll to the **Predictive AI Demand Forecast** graph.
3. The AI engine utilizes historic linear regression algorithms to project future monthly volume demands for each product category.

#### 2.2 Store Manager
The Store Manager is the backbone of operations. They control live inventory levels, approve shop orders, process returns, and draw territorial delivery maps.

**How to Update Inventory & View Trends:**
1. Click **Inventory** on the navigation bar.
2. The dynamic Stock Histogram visually flags any product whose stock has dipped below 80 units in bright red.
3. Click the **Edit (Pencil Icon)** on a low-stock row and input the new stock addition (e.g., add 500 units).
4. The system automatically creates a background audit log.
5. Review the **Inventory Trends** Line Chart at the top of the page. This Chart.js graph dynamically plots the historical transaction logs you just created across an interactive timeline!

**How to Draw & Assign Delivery Territories:**
1. Click **Territories** on the navigation bar.
2. Select a specific **Logistics Representative** from the top dropdown menu.
3. Using the large, interactive Leaflet Map, click the **Draw Polygon (Pentagon Icon)** on the left toolbar.
4. Click along streets to outline a secure geometric shape encompassing the driver's delivery district. Connect the last dot to close the shape.
5. Click the blue **Save Territory** button. The GPS coordinates are instantly pushed to the spatial database.

#### 2.3 Shop Owner
The Shop Owner is the customer. They browse the catalog, place orders, monitor delivery status, and log bad returns.

**How to Place an Order:**
1. Click **New Order** securely to browse the graphical product catalog.
2. Type the quantity into the numeric box under your desired product and click **Add to Cart**.
3. Review your Cart on the right-hand panel and click **Submit Order**. 
4. Navigate to **Order History** to watch your delivery state securely move from `Pending` -> `Preparing` -> `Dispatched` -> `Delivered`.

**How to Log a Product Return:**
1. If crated goods are damaged or expired, click **Returns**.
2. Click **Log New Return +**.
3. Select the problematic **Order ID** and the **Product Name & Size** from the dynamic dropdown menus.
4. State your reason and submit. The Store Manager will be notified to collect it.

#### 2.4 Sales Representative (Logistics Driver)
The driver receives dispatched orders dynamically via geofencing and uses GPS to verify physical deliveries.

**How to View & Deliver Orders:**
1. Log in. You are presented with the **Logistics Dashboard**, displaying the immediate list of "Orders to Deliver". 
2. Review the orange Interactive Map on the right. This represents your **Assigned Territory Polygon** (drawn earlier by the Store Manager). 
3. *Note: You will only see orders that originate from Shop Coordinates securely located inside that shape.*
4. Click **Verify Delivery** next to an order.
5. **Security Check:** The browser will request your HTML5 GPS Location. If your exact, real-time Latitude/Longitude is further than exactly 1.0 km from the shop's registered pin, your delivery submission will be securely rejected.

**How to Calculate the "Uber-like" Turn-by-Turn Route:**
1. From the Logistics dashboard, click **Route Map** on the top navigation bar.
2. Click the black **Calculate Best Route 🚗** floating button.
3. The system will securely grab your starting GPS location.
4. Utilizing the **Leaflet Routing Machine (OSRM Backend)**, the map will violently draw a thick solid black directional route, connecting your current location directly to all of your assigned drop-off waypoints perfectly linked!
5. Follow the turn-by-turn instruction box to complete your route.

#### 2.5 Administrator
The Admin is the system guardian. They manage users and handle secure lockouts.

**How to Recover a User's Forgotten Password:**
1. When a user clicks "Forgot Password" on the login screen, they submit a username entry.
2. Log in as the Admin and locate the **Pending Password Reset Tickets** notification box on your dashboard.
3. Verify the employee's ID offline, and click **Resolve Ticket**.
4. Issue them a temporary password securely via phone or email, or manually reset the hash via the main Users panel.

***

### 3. User Journey 1: Factory Production & Catalog Management
**Goal**: The Factory Owner needs to launch a new beverage size.

1.  **Login**: The Factory Owner logs into the portal using their credentials.
2.  **Dashboard Hub**: They are greeted by the Factory Dashboard showing a high-level statistical overview (Total Products, Units in Stock, Low-Stock Warnings).
3.  **Product Management**: They click into the **Products** section.
4.  **Creating a Product**: They click `Add New Product +`.
    *   They fill in standard details (Name, Size, Price, Description, Product Image).
    *   *Dynamic Enums*: The system intuitively updates its database definitions if a completely new Product Name or Size is typed in.
5.  **Analytics Review**: After launching the product, they can click the **Business Insights** tab to view historic distribution line-charts and predictive AI demand modules for the upcoming months.

***

### 4. User Journey 2: Store Management & Inventory Preparation
**Goal**: The Store Manager must restock the warehouse and oversee incoming orders.

1.  **Inventory Check**: The Store Manager logs in and checks the **Inventory** dashboard.
2.  **Visual Highlights**: A dynamic Bar Chart visually flags any product whose stock has dipped below 80 units in red. 
3.  **Stock Update**: The manager clicks "Edit" next to a low-stock product and adds 500 units to the system. The system automatically creates a background `inventory_log` for auditing.
4.  **Order Processing**: 
    *   The manager navigates to the **Orders** section (or checks the main Dashboard feed).
    *   A Shop Owner has requested 200 bottles of Mango Juice.
    *   The Manager checks their stock and clicks **Confirm**. The stock is immediately deducted from the warehouse, and the order is marked `Preparing`.
5.  **Territory Assignment (Geofencing)**:
    *   To assign this order to a driver, the Manager clicks **Territories**.
    *   They select the driver (Sales Rep) from a dropdown list.
    *   Using the interactive Leaflet Map, the Manager draws a geographical Polygon around a specific district and clicks "Save Territory".

***

### 5. User Journey 3: Shop Owner Ordering & Returns
**Goal**: A shop owner needs to order stock for the weekend and return an expired crate.

1.  **Placing an Order**:
    *   The Shop Owner logs in and navigates to **New Order** / **Products**.
    *   They view the highly graphical product catalog.
    *   They input the quantities they want for each item and add them to their Cart.
    *   They submit the Cart. The order enters the `Pending` state.
2.  **Order Tracking**: The shop owner can monitor the live status of the order (`Pending` -> `Preparing` -> `Dispatched` -> `Delivered`) via their Order History dashboard.
3.  **Logging a Return**:
    *   They notice a previous crate of Wood Apple was expired.
    *   They click **Returns**, select the specific Order ID and Product, state the reason ("Expired"), and click submit. 
    *   The Store Manager will be notified to process it.

***

### 6. User Journey 4: Sales Representative Dispatch & Delivery
**Goal**: A delivery driver needs to review their route and physically deliver the goods.

1.  **Dynamic Dispatch**: The Sales Rep logs into the **Logistics Route Map**.
2.  **Geofenced Filtering**: Thanks to the territory polygon drawn by the Store Manager in Journey 2, the map automatically filters out the entire country's orders and *only* displays orders securely located inside their assigned zone!
3.  **Intelligent Routing**: 
    *   The driver clicks "Route Map" and accesses the Uber-style routing UI.
    *   They trigger their local GPS location.
    *   The map automatically generates a thick, directional route covering all connected waypoints for the day so they never get lost.
4.  **Verification Check**:
    *   The driver arrives at the shop.
    *   They click **Verify Delivery** on the dashboard.
    *   *Security*: The browser prompts for GPS permissions. The system captures their live Latitude/Longitude via the HTML5 Geolocation API and sends it to the server.
    *   The backend Haversine formula calculates the distance between the driver's phone and the Shop's registered coordinate. If they are within 1KM, the order is marked `Delivered`!

***

### 7. User Journey 5: Administrator System Oversight
**Goal**: A user forgot their password and the Admin must process the secure recovery ticket.

1.  **Ticket Request**: A user forgets their password and clicks "Forgot Password" on the login screen. They enter their username, which generates a secure `pw_reset_tickets` entry.
2.  **Admin Review**: The Admin logs in and checks the **Password Reset Requests** panel.
3.  **Resolution**: The Admin verifies the user's identity out-of-band (e.g., via phone), clicks "Resolve Ticket", and provides the user with a temporary password or resets it manually in the Users panel.

***

### 8. Advanced Technical Features
*   **Profile Management**: Every user has a dropdown accessible via their colored Avatar in the top right nav bar. They can dynamically change their Name, Password, and contact info.
*   **Predictive AI**: The Factory Analytics panel utilizes historic linear regression algorithms to project future volume demands per product.
*   **Inventory Time-Series Data**: Background audit logs dynamically generate Chart.js graphs scaling the history of all transactions.
*   **Uber-style Map Routing**: Complete native integration with OSRM (Open Source Routing Machine) to calculate live waypoints based on the browser's geographic API payload.
*   **Spatial Database Queries**: True geospatial `ST_Contains()` logic is implemented in the MySQL backend to guarantee mathematically strict geofencing for Logistics routing.

---
*End of Manual*
