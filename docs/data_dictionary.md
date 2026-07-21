# Data Dictionary

This document defines the schema structure, data types, and purpose of every table and field in the Duct-Cenp database.

## Table: `users`
| Field | Type | Modifiers | Description |
|-------|------|-----------|-------------|
| id | BIGINT | PK, Auto-Inc | Unique identifier |
| name | VARCHAR(255) | Not Null | Full name of the user |
| email | VARCHAR(255) | Unique, Not Null| User's email address |
| password | VARCHAR(255) | Not Null | Hashed password |
| created_at | TIMESTAMP | Nullable | Creation timestamp |
| updated_at | TIMESTAMP | Nullable | Last update timestamp |

## Table: `sites`
| Field | Type | Modifiers | Description |
|-------|------|-----------|-------------|
| id | BIGINT | PK, Auto-Inc | Unique identifier |
| name | VARCHAR(255) | Not Null | Name of the construction site |
| project_code | VARCHAR(255) | Nullable | Internal project code for the site |
| created_at | TIMESTAMP | Nullable | Creation timestamp |

## Table: `user_sites`
| Field | Type | Modifiers | Description |
|-------|------|-----------|-------------|
| user_id | BIGINT | FK (users.id) | ID of the user |
| site_id | BIGINT | FK (sites.id) | ID of the site |

## Table: `orders`
| Field | Type | Modifiers | Description |
|-------|------|-----------|-------------|
| id | BIGINT | PK, Auto-Inc | Unique identifier |
| site_id | BIGINT | FK (sites.id) | Site the order belongs to |
| user_id | BIGINT | FK (users.id) | Engineer who created the order |
| confirmed_by| BIGINT | FK (users.id), Null| Manager who approved the order |
| status | VARCHAR(50) | Default: draft | Status (draft, submitted, approved, rejected, in-progress, completed) |
| created_at | TIMESTAMP | Nullable | Creation timestamp |
| updated_at | TIMESTAMP | Nullable | Last update timestamp |

## Table: `duct_types`
| Field | Type | Modifiers | Description |
|-------|------|-----------|-------------|
| id | BIGINT | PK, Auto-Inc | Unique identifier |
| name | VARCHAR(255) | Not Null | Name of the duct (e.g., Straight Duct) |
| description| TEXT | Nullable | Optional description |

## Table: `order_items`
| Field | Type | Modifiers | Description |
|-------|------|-----------|-------------|
| id | BIGINT | PK, Auto-Inc | Unique identifier |
| order_id | BIGINT | FK (orders.id) | Associated order |
| duct_type_id| BIGINT | FK (duct_types.id)| Associated duct type |
| quantity | INT | Default: 1 | Number of pieces required |
| length | INT | Nullable | Dimension measurement |
| width | INT | Nullable | Dimension measurement |
| height | INT | Nullable | Dimension measurement |
| remark | VARCHAR(255) | Nullable | Special instructions |

## Table: `comments`
| Field | Type | Modifiers | Description |
|-------|------|-----------|-------------|
| id | BIGINT | PK, Auto-Inc | Unique identifier |
| order_id | BIGINT | FK (orders.id) | Order this comment belongs to |
| user_id | BIGINT | FK (users.id) | User who wrote the comment |
| content | TEXT | Not Null | The message body |
| created_at | TIMESTAMP | Nullable | Timestamp when posted |
