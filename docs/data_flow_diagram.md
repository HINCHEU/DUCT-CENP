# Data Flow Diagram (DFD)

This document contains the Data Flow Diagrams representing the flow of information in the Duct-Cenp System.

## Level 0 DFD (Context Diagram)
The Context Diagram shows the system as a single process interacting with external entities (Users).

```mermaid
flowchart TD
    E[Engineer] -->|Order Details| S(Duct-Cenp System)
    M[Manager] -->|Approval/Rejection| S
    W[Workshop] -->|Status Updates| S
    
    S -->|Order Status| E
    S -->|Pending Orders| M
    S -->|Approved Orders & PDF| W
```

## Level 1 DFD
This level breaks down the main system into detailed sub-processes.

```mermaid
flowchart TD
    E[Engineer] -->|1. Draft Order & Add Items| P1(Order Creation Process)
    P1 --> D1[(Database: Orders & Items)]
    
    E -->|2. Submit Order| P2(Order Submission)
    P2 --> D1
    
    D1 -->|3. Fetch Pending Orders| P3(Manager Review Process)
    M[Manager] -->|4. Approve/Reject| P3
    P3 --> D1
    
    D1 -->|5. Fetch Approved Orders| P4(Workshop Fulfillment)
    W[Workshop] -->|6. Update Status| P4
    P4 --> D1
    
    D1 -->|7. Generate Cut List| P5(PDF Generator)
    P5 -->|Download PDF| W
    P5 -->|Download PDF| M
    P5 -->|Download PDF| E
```

## DFD Process Descriptions
- **1. Order Creation Process:** Validates and stores the draft order and its line items.
- **2. Order Submission:** Changes the status of the order from 'draft' to 'submitted'.
- **3. Manager Review Process:** Displays orders awaiting approval to managers and processes their modifications/decisions.
- **4. Workshop Fulfillment:** Allows workshop staff to track their manufacturing pipeline and update order statuses.
- **5. PDF Generator:** Queries the order and items, groups them by duct type, and renders a PDF cut-list.
