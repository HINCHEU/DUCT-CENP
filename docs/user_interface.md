# User Interface Design

The Duct-Cenp System is built using web-based UI technologies (HTML, CSS, Blade, Tailwind CSS / Bootstrap) to provide a responsive and intuitive experience.

## 1. Global UI Elements
- **Navigation Bar:** Fixed at the top, contains the logo, links to dashboards based on the user's role, and a profile/logout dropdown.
- **Alerts/Toasts:** Used to display success messages (e.g., "Order submitted successfully") or errors.
- **Breadcrumbs:** Helpful for navigating back from nested views (e.g., Home > Orders > Order #123).

## 2. Authentication View (Login Page)
- **Layout:** Centered card on a clean background.
- **Elements:** 
  - Email input field.
  - Password input field.
  - "Remember Me" checkbox.
  - "Login" submit button.
  - "Forgot Password" link.

## 3. Engineer Workspace
### 3.1 Order Listing Page
- **Elements:** A data table displaying the Engineer's orders.
- **Columns:** Order ID, Site Name, Status, Date Created, Actions.
- **Actions:** "Create New Order" button, "View/Edit" link for each row.

### 3.2 Order Details / Edit View
- **Header:** Order Meta Data (Site, Status, Date).
- **Body:** 
  - A form to add new `order_items` (Select Duct Type, Input Length/Width/Height, Quantity).
  - A table listing currently added items. Allows inline editing of quantity and remarks.
- **Footer/Sidebar:** 
  - Comment section to add or read notes.
  - "Submit Order" button (visible if draft).
  - "Download PDF" button.

## 4. Manager Workspace
### 4.1 Pending Approvals View
- **Elements:** Data table focused on orders with the status `submitted`.
- **Columns:** Order ID, Engineer Name, Site Name, Item Count, Actions.

### 4.2 Review View
- **Elements:** Similar to the Engineer's view but with administrative controls.
- **Actions:** 
  - Editable item table (Manager can tweak quantities before approval).
  - "Approve Order" (Green button).
  - "Reject Order" (Red button, prompts for a reason/comment).

## 5. Workshop Workspace
### 5.1 Fabrication Queue
- **Elements:** Dashboard showing `approved` and `in-progress` orders.
- **Actions:** 
  - "Print Cut List" button (Prominent).
  - "Mark as In Progress" or "Mark as Completed" status toggle.

## 6. Report View (PDF Cut List)
- **Format:** A clean, printable A4 PDF.
- **Header:** Company Logo, Order ID, Site Name, Approver Name.
- **Body:** Order items grouped logically by `Duct Type` (e.g., all Straight Ducts grouped together, all Elbows grouped together), accompanied by checkboxes for the workshop staff to tick off as they fabricate them.
