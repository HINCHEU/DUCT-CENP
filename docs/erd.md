# Entity Relationship Diagram (ERD)

This document visualizes the database schema and relationships between different entities in the Duct-Cenp Order Management System.

## ERD Diagram

```mermaid
erDiagram
    USER ||--o{ ORDER : "creates"
    USER ||--o{ COMMENT : "writes"
    USER }|--|{ SITE : "assigned to (UserSite)"
    USER }|--|{ ROLE : "has (Spatie)"
    
    SITE ||--o{ ORDER : "has"
    
    ORDER ||--o{ ORDER_ITEM : "contains"
    ORDER ||--o{ COMMENT : "has"
    ORDER }o--|| USER : "confirmed_by (Manager)"
    
    DUCT_TYPE ||--o{ ORDER_ITEM : "categorizes"

    USER {
        bigint id PK
        string name
        string email
        string password
    }

    SITE {
        bigint id PK
        string name
        string project_code
    }

    ORDER {
        bigint id PK
        bigint site_id FK
        bigint user_id FK
        bigint confirmed_by FK
        string status
        timestamp created_at
    }

    ORDER_ITEM {
        bigint id PK
        bigint order_id FK
        bigint duct_type_id FK
        integer quantity
        integer length
        integer width
        integer height
        string remark
    }

    DUCT_TYPE {
        bigint id PK
        string name
        string description
    }

    COMMENT {
        bigint id PK
        bigint order_id FK
        bigint user_id FK
        text content
    }
```

## Entity Descriptions
- **USER:** Represents the system users (Engineers, Managers, Workshop). Handled by Laravel Auth.
- **ROLE:** Represents user roles. Handled via Spatie Laravel Permission.
- **SITE:** Represents a construction or project site where ducts will be delivered/installed.
- **ORDER:** The main entity linking a site, the user who created it, and its current status.
- **ORDER_ITEM:** Details the specific duct pieces required for an order, including dimensions and quantities.
- **DUCT_TYPE:** A catalog of available duct shapes/types (e.g., Rectangular, Spiral).
- **COMMENT:** Communication logs attached to a specific order.
