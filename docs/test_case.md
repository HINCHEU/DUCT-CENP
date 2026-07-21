# Test Cases

This document outlines key functional test cases for validating the Duct-Cenp Order Management System.

## Test Scope: Authentication
| Test ID | Scenario | Pre-conditions | Test Steps | Expected Result | Pass/Fail |
|---------|----------|----------------|------------|-----------------|-----------|
| TC-01 | Valid Login | User has an active account | 1. Navigate to login page<br>2. Enter valid email & password<br>3. Click Login | Redirected to correct role dashboard. | |
| TC-02 | Invalid Login | User is on login page | 1. Enter incorrect password<br>2. Click Login | Validation error shown ("Credentials do not match"). | |

## Test Scope: Engineer Role
| Test ID | Scenario | Pre-conditions | Test Steps | Expected Result | Pass/Fail |
|---------|----------|----------------|------------|-----------------|-----------|
| TC-03 | Create Draft Order | Logged in as Engineer | 1. Click "New Order"<br>2. Select Site<br>3. Click Save | Order created with status 'draft'. | |
| TC-04 | Add Item to Order | Order is in 'draft' status | 1. Open order<br>2. Select Duct Type<br>3. Input qty and sizes<br>4. Save | Item appears in the order list. | |
| TC-05 | Submit Order | Order has items & is 'draft' | 1. Click "Submit Order" | Order status changes to 'submitted'. | |

## Test Scope: Manager Role
| Test ID | Scenario | Pre-conditions | Test Steps | Expected Result | Pass/Fail |
|---------|----------|----------------|------------|-----------------|-----------|
| TC-06 | Review & Edit Item | Logged in as Manager | 1. Open 'submitted' order<br>2. Change item qty<br>3. Save | Item quantity is updated in DB. | |
| TC-07 | Approve Order | Order is 'submitted' | 1. Open order<br>2. Click "Approve" | Status changes to 'approved', `confirmed_by` is set. | |
| TC-08 | Reject Order | Order is 'submitted' | 1. Open order<br>2. Click "Reject" | Status changes to 'rejected'. | |

## Test Scope: Workshop Role
| Test ID | Scenario | Pre-conditions | Test Steps | Expected Result | Pass/Fail |
|---------|----------|----------------|------------|-----------------|-----------|
| TC-09 | View Approved Order | Logged in as Workshop | 1. Open Dashboard | Only 'approved' or 'in-progress' orders are listed. | |
| TC-10 | Update Status | Order is 'approved' | 1. Open order<br>2. Change status to 'completed' | Order status is updated to 'completed'. | |
| TC-11 | Download PDF | Order is 'approved' | 1. Click "Download Cut List" | A PDF file is generated and downloaded successfully. | |

## Test Scope: Communication
| Test ID | Scenario | Pre-conditions | Test Steps | Expected Result | Pass/Fail |
|---------|----------|----------------|------------|-----------------|-----------|
| TC-12 | Add Comment | Logged in (Any role) | 1. Open an order<br>2. Type in comment box<br>3. Submit | Comment is appended to the order's history. | |
