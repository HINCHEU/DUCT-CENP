# System Requirements

## 1. Introduction
This document outlines the requirements for the Duct-Cenp Order Management System. The system manages the workflow of duct fabrication orders from engineering to the workshop.

## 2. Hardware Requirements
### 2.1 Server-side Requirements
- **CPU:** Dual Core 2.0GHz or higher
- **RAM:** 4 GB minimum (8 GB recommended)
- **Storage:** 50 GB SSD or higher
- **Network:** High-speed internet connection

### 2.2 Client-side Requirements
- Any modern computer or mobile device.
- Active internet connection.

## 3. Software Requirements
### 3.1 Server-side Requirements
- **OS:** Linux (Ubuntu 20.04+), Windows Server, or macOS.
- **Web Server:** Nginx or Apache
- **Language:** PHP 8.2+
- **Framework:** Laravel 12.x
- **Database:** MySQL 8.0+ or SQLite
- **Dependency Manager:** Composer, npm

### 3.2 Client-side Requirements
- **Web Browser:** Modern browser (Chrome, Firefox, Safari, Edge). JavaScript must be enabled.

## 4. Functional Requirements
### 4.1 Authentication & Authorization
- Users must be able to log in securely.
- Roles and permissions must restrict access (Engineer, Manager, Workshop).

### 4.2 Engineer Module
- Ability to create, edit, and delete draft orders.
- Ability to add items (ducts) with specific dimensions and quantities to an order.
- Ability to submit orders for manager approval.
- Ability to revert an order back to draft if it hasn't been approved yet.

### 4.3 Manager Module
- Ability to view all submitted orders.
- Ability to approve or reject an order.
- Ability to modify order items (quantity, remarks) before approval.

### 4.4 Workshop Module
- Ability to view approved orders.
- Ability to update the fulfillment status of an order.

### 4.5 Common Features
- Ability to download PDF cut lists for orders.
- Ability to add comments to specific orders for communication.

## 5. Non-Functional Requirements
- **Security:** Passwords must be hashed. CSRF protection and SQL injection prevention must be active.
- **Usability:** The interface should be intuitive and responsive on mobile devices.
- **Performance:** Page load times should be under 2 seconds. Reports should generate within 5 seconds.
