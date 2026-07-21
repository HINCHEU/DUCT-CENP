# Sprint Backlog

This document breaks down the Product Backlog into manageable sprints to execute the development of the Duct-Cenp system.

## Sprint 1: Foundation & Engineer Workflow
**Goal:** Set up the core framework, authentication, and allow Engineers to successfully create and submit orders.
**Duration:** 2 Weeks

| Task ID | PB Ref | Task Description | Assignee | Status |
|---------|--------|------------------|----------|--------|
| SP1-01 | PB-01 | Install Laravel & set up UI scaffolding (Breeze/Jetstream). | Dev 1 | Done |
| SP1-02 | PB-02 | Install Spatie Permissions, seed Roles (Engineer, Manager, Workshop). | Dev 2 | Done |
| SP1-03 | PB-03 | Create migrations and models for Sites, DuctTypes, Orders, OrderItems. | Dev 1 | Done |
| SP1-04 | PB-04 | Build Engineer Dashboard and "Create Order" form. | Dev 2 | Done |
| SP1-05 | PB-05 | Build dynamic UI to add items to an order and save to DB. | Dev 1 | Done |
| SP1-06 | PB-06 | Add "Submit" button and logic to change status to `submitted`. | Dev 2 | Done |

---

## Sprint 2: Manager Review & Workshop Delivery
**Goal:** Implement the managerial review process, workshop queue, and PDF generation.
**Duration:** 2 Weeks

| Task ID | PB Ref | Task Description | Assignee | Status |
|---------|--------|------------------|----------|--------|
| SP2-01 | PB-07 | Build Manager Dashboard listing only `submitted` orders. | Dev 1 | To Do |
| SP2-02 | PB-08 | Implement Manager review view with item editing & Approve/Reject actions. | Dev 2 | To Do |
| SP2-03 | PB-09 | Build Workshop Dashboard listing `approved` orders. | Dev 1 | To Do |
| SP2-04 | PB-10 | Add status toggle for Workshop (In Progress -> Completed). | Dev 1 | To Do |
| SP2-05 | PB-11 | Install `laravel-dompdf`, design HTML template, and generate grouped PDF reports. | Dev 2 | To Do |
| SP2-06 | PB-12 | Build the polymorphic/standard Comments system and attach it to the order view. | Dev 1 | To Do |
| SP2-07 | N/A | QA Testing across all 3 roles. | QA Team | To Do |
