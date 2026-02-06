Anti-Gravity Soft Drink Distributing System
Project Overview
The Anti-Gravity system is a comprehensive web-based platform designed for M/S Pivo Holdings (pvt) Ltd. It serves two primary functions:

Internal Management: Automating manual documentation for inventory, sales, and logistics with high-precision geographical tracking.

Marketing & Customer Portal: A public-facing site where shop owners can browse products, manage multiple retail outlets, and track orders in real-time.

Core System Functionalities
1. Advanced Inventory & Product Management
Dynamic Product Catalog: Supports various drink types (e.g., Mix Fruit, Mango) with multiple volume sizes and high-quality images.

Inventory Control: Real-time tracking of dispatched products and automated reorder alerts for low-stock items.

Returns Management: Tracks "Collected" or "Stored" product returns with specific reason logging.

2. Geofenced Logistics & Delivery
Polygon Geofencing: Sales Supervisors can define precise geographical boundaries on a map to assign territories to Sales Representatives.

GPS-Verified Delivery: Sales Reps can only mark an order as "Delivered" if their current GPS coordinates are within a 50-meter radius of the shop's registered location.

Route Optimization: The Sales Rep dashboard provides the fastest and shortest routes for deliveries, similar to ride-hailing applications.

3. Order Tracking & Escalation
End-to-End Tracking: Shop owners can see real-time status updates (Pending → Preparing → Dispatched → Delivered).

Critical Order Alerts: Any order older than 5 days is automatically flagged as Critical for both the Sales Rep and Supervisor to ensure no delivery lag.

Multi-Shop Management: Shop owners can register and manage multiple outlets, each with its own GPS coordinates and order history.

4. Analytics & Reporting
Sales Snap: Generates quick summaries of daily revenue and transaction volume for internal review.

Demand Prediction: Uses historical data to predict future product demand with confidence scores.

Audit Logs: Complete tracking of user activity and status changes for every order.

User Roles & Page Privileges
Role	Accessible Modules/Pages	Key Privileges
System Admin	Global Dashboard, User Mgmt, System Settings	Full CRUD on all users, system configurations, and global audit logs.
Sales Supervisor	Geofencing Map, Rep Performance, Critical Alerts	Create/assign territory polygons; monitor all reps in their area; manage critical orders.
Sales Rep	Delivery Dashboard, Route Map, Area Orders	View assigned orders; access GPS routing; confirm delivery at shop location.
Store Manager	Inventory UI, Order Confirmation, Returns UI	Confirm/reject orders; update stock levels; manage product returns.
Shop Owner	Product Gallery, My Shops, Order History	Register multiple shops; place orders; track real-time delivery status.
Factory Owner	Financial Reports, Sales Predictions, Gallery	View high-level business analytics and marketing content.
IT Support	System Logs, Error Tracking, DB Maintenance	Monitor system health and troubleshoot technical issues.
Technical Architecture
Database Schema
The system utilizes a relational database (pivo_holdings_db) with the following key tables:

users: Manages role-based authentication.

shops: Stores GPS coordinates and links to owners.

orders & order_items: Tracks transaction details and product breakdown.

geofenced_areas: Stores the spatial data (polygons) for territories.

inventory: Manages current stock levels per product variant.

Integration Standards
Platform: Windows-based web environment.

Communication: FTP for data transfer.

Compliance: ISO standards and ICTA Sri Lanka regulatory guidelines.

Installation & Setup

Environment: Ensure PHP 8.0+ is installed (as per the SQL dump header).

API Keys: Configure Google Maps API for geofencing and routing features.

Brand Compliance: All UI elements must strictly adhere to the M/S Pivo Holdings official logo and brand guidelines.