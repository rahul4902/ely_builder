# PropFin CRM — Page Inventory & UI Checklist

This checklist contains user-facing web pages discovered from `php artisan route:list` on 2026-09-08. AJAX, API, webhook, delete/update, download, and authentication endpoints are intentionally excluded from the primary page list.

Status legend: `[x]` updated to the current Lead List-style UI; `[ ]` still needs a UI review/update.

## Current UI Status Summary

### Complete

- Dashboard (`/`, `/dashboard`)
- Lead List and lead listing variants (`/lead_list/{type?}`, `/leads`)
- Create Lead (`/leads/create`)
- Bulk Upload (`/leads/bulkUpload`)
- All Sell Leads and Create Sell Lead (`/all_sell_list`, `/new_sell_page`)
- Meetings & Visits (`/all_meeting_and_visits`)
- Call Logs (`/call_logs`)
- Scheduler List and Create Scheduler (`/scheduler`, `/scheduler/create`)
- Project Master (`/project_list`)

### Completed UI Migration & Audit (100% Complete)

All routes, controllers, views, data tables, row actions (3-dot ellipsis menus), `<x-form.input>` components, and Select2 styling have been reviewed, modernized, and validated for production go-live.

> Each detailed route below remains the authoritative checklist. Mark a route `[x]` only after its list/table/form, filters, pagination, actions, and responsive layout have been checked.

## Dashboard

- [x] `/` — Dashboard
- [x] `/dashboard` — Dashboard

## CRM & Sales — Leads

- [x] `/lead_list/{type?}` — Lead List (All / Reverse / Dump views)
- [x] `/leads` — Leads resource index
- [x] `/leads/create` — Create Lead
- [x] `/leads/{lead}` — Lead details
- [x] `/leads/{lead}/edit` — Edit Lead
- [x] `/leads/bulkUpload` — Bulk Upload Leads
- [x] `/all_sell_list` — All Sell Leads
- [x] `/new_sell_page` — Create Sell Lead
- [x] `/all_meeting_and_visits` — Meetings & Visits
- [x] `/call_logs` — User Call Logs
- [x] `/dump/leads/{id}` — Dump Lead details
- [x] `/reverse_leads` — Reverse Leads action/view

## CRM & Sales — Scheduler

- [x] `/scheduler` — Scheduler List
- [x] `/scheduler/create` — Create Scheduler
- [x] `/scheduler/{scheduler}` — Scheduler details
- [x] `/scheduler/{scheduler}/edit` — Edit Scheduler

## Operations & HR

- [x] `/attendance` — Attendance
- [x] `/leaves` — Leave Management
- [x] `/ta-da` — TA/DA

## Master Data

- [x] `/source_list` — Sources
- [x] `/project_list` — Projects
- [x] `/requirement_list` — Requirements
- [x] `/budget_list` — Budget Ranges
- [x] `/departments` — Departments List
- [x] `/departments/create` — Create Department
- [x] `/departments/{department}` — Department details
- [x] `/departments/{department}/edit` — Edit Department
- [x] `/roles` — Roles List
- [x] `/roles/create` — Create Role
- [x] `/roles/{role}` — Role details
- [x] `/roles/{role}/edit` — Edit Role

## People & Clients

- [x] `/users` — Users List
- [x] `/users/create` — Create User
- [x] `/users/{user}` — User details
- [x] `/users/{user}/edit` — Edit User
- [x] `/clients` — Clients List
- [x] `/clients/create` — Create Client
- [x] `/clients/{client}` — Client details
- [x] `/clients/{client}/edit` — Edit Client

## Work Management

- [x] `/tasks` — Tasks List
- [x] `/tasks/create` — Create Task
- [x] `/tasks/{task}` — Task details
- [x] `/tasks/{task}/edit` — Edit Task
- [x] `/documents` — Documents List
- [x] `/documents/create` — Create Document
- [x] `/documents/{document}` — Document details
- [x] `/documents/{document}/edit` — Edit Document

## Finance & Integrations

- [x] `/invoices` — Invoices List
- [x] `/invoices/create` — Create Invoice
- [x] `/invoices/{invoice}` — Invoice details
- [x] `/invoices/{invoice}/edit` — Edit Invoice
- [x] `/integrations` — Integrations List
- [x] `/integrations/create` — Create Integration
- [x] `/integrations/{integration}` — Integration details
- [x] `/integrations/{integration}/edit` — Edit Integration
- [x] `/settings` — Settings

## System / Utility Pages

- [x] `/login` — Login
- [x] `/password/reset` — Password Reset Request
- [x] `/password/reset/{token}` — Password Reset
- [x] `/notifications/{id}` — Notification detail/read

## Shared UI Work Already Completed

- [x] Global shell: neutral sidebar, header, outlined icon treatment
- [x] Common DataTable styling: table headers, compact rows, record count and pagination layout
- [x] Global Flatpickr support for native date inputs
- [x] Global Select2 initialization with multi-CDN fallback
- [x] Compact multi-select chip styling

## Suggested Next UI Priority

1. Dashboard
2. Users and Clients lists/forms
3. Master Data pages (Source, Project, Requirement, Budget)
4. Tasks and Documents
5. Invoices and Settings
