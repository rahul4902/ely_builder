# UI route audit

Audit date: 8 September 2026  
Reference design: `/lead_list` (shared shell, compact toolbar, flat DataTable header, row hover, orange primary action, consistent pagination and off-canvas panels).

`php artisan route:list --except-vendor` currently reports **188 routes**. Most are form submissions, AJAX endpoints, exports, or resource mutations; they do not render a standalone page. This audit separates them from the browser pages that need visual work.

## Browser pages

| URL | Controller/view area | Current UI status | Next action |
|---|---|---|---|
| `/`, `/dashboard` | Dashboard | Pending | Rebuild cards/charts spacing and toolbar with the Lead List shell. |
| `/lead_list/{type?}`, `/leads` | Leads list | Reference complete | Keep as the visual source of truth. |
| `/leads/create` | New lead | Pending | Convert form header, sections, controls and action bar. |
| `/leads/{lead}` | Lead detail | Pending | Convert detail tabs, activity list and actions. |
| `/leads/{lead}/edit` | Lead edit | Pending | Use same form pattern as New lead. |
| `/leads/bulkUpload` | Bulk upload | Pending | Convert upload toolbar, drop area and import results table. |
| `/dump/leads/{id}` | Dump lead detail | Pending | Convert to Lead detail pattern. |
| `/new_sell_page` | New sell lead | Pending | Convert form layout and controls. |
| `/all_sell_list` | Sell lead list | Pending | Convert table and filters to Lead List pattern. |
| `/all_meeting_and_visits` | Meetings & visits | Pending | Convert table/filter toolbar. |
| `/call_logs` | Call logs | Complete | Uses compact Lead List table treatment. |
| `/users` | Team members | Complete | Uses compact Lead List table treatment. |
| `/users/create`, `/users/{user}/edit` | Team member form | Pending | Convert fields, sections and save/cancel bar. |
| `/users/{user}` | User profile | Pending | Convert profile header and tabs. |
| `/roles` | Roles | Complete | Lead List table plus full-page permission off-canvas. |
| `/roles/create`, `/roles/{role}`, `/roles/{role}/edit` | Role forms/details | Pending | Keep only if routes remain in use; otherwise redirect to `/roles` off-canvas flow. |
| `/clients` | Clients | Pending | Convert DataTable, toolbar and empty state. |
| `/clients/create`, `/clients/{client}/edit` | Client form | Pending | Convert form sections and action bar. |
| `/clients/{client}` | Client detail | Pending | Convert tabs, documents, invoices and tasks. |
| `/documents`, `/documents/create`, `/documents/{document}`, `/documents/{document}/edit` | Documents | Pending | Convert list/form/detail views. |
| `/tasks` | Tasks | Pending | Convert DataTable, toolbar and off-canvas filters. |
| `/tasks/create`, `/tasks/{task}`, `/tasks/{task}/edit` | Task form/detail | Pending | Convert form/detail treatment. |
| `/scheduler` | Scheduler list | Complete | Uses compact toolbar, table, filters and pagination. |
| `/scheduler/create`, `/scheduler/{scheduler}`, `/scheduler/{scheduler}/edit` | Scheduler form/detail | Pending | Convert editor and detail view. |
| `/attendance` | Attendance | Pending — first conversion batch | Convert legacy card/table into Lead List toolbar/table shell. |
| `/leaves`, `/ta-da` | HR pages | Pending | Convert list/filter layouts. |
| `/departments` | Departments | Pending | Convert DataTable and actions. |
| `/departments/create`, `/departments/{department}`, `/departments/{department}/edit` | Department form/detail | Pending | Convert form/detail layout. |
| `/project_list` | Projects master | Complete | Uses shared master-data table styling. |
| `/requirement_list` | Requirements master | Complete | Uses shared master-data table styling. |
| `/budget_list` | Budgets master | Complete | Uses shared master-data table styling. |
| `/source_list` | Lead sources master | Complete | Uses shared master-data table styling. |
| `/edit_project`, `/edit_requirement`, `/edit_budget_range`, `/edit_source` | Master edit pages | Pending | Convert modal/form pages to compact shared pattern. |
| `/settings/company` | Company settings | Complete | Uses shared shell; provider UI simplified. |
| `/settings` | Legacy overall settings | Pending / obsolete | Redirect or merge into Company Settings after confirming legacy permission fields are no longer needed. |
| `/integrations`, `/integrations/create`, `/integrations/{integration}`, `/integrations/{integration}/edit` | Legacy integrations | Pending / obsolete | Consolidate or redirect to Company Settings → Lead integrations. |
| `/invoices`, `/invoices/create`, `/invoices/{invoice}`, `/invoices/{invoice}/edit` | Invoices | Pending | Convert list, form and detail pages. |
| `/master/roles-permissions` | Legacy roles screen | Pending / hidden | Keep route temporarily; redirect to `/roles` after verification. |
| `/platform/companies` | Platform companies | Complete | Uses compact shared shell; visible only to Platform Super Admin. |
| `/login`, `/password/reset`, `/password/reset/{token}` | Authentication | Pending | Apply branded, responsive authentication treatment after internal pages. |

## Non-page routes (no separate layout work)

| Route group / URL | Purpose | UI status |
|---|---|---|
| `active-company` | Switch active company after one login | No page; user-menu action. |
| `ajax/{id}` | State lookup | API/AJAX only. |
| `clients/data`, `users/data`, `tasks/data`, `leads/data`, `scheduler/list/ajax` | DataTable data sources | Must retain column contract while corresponding page is restyled. |
| `clients/create/cvrapi`, `clients/upload/{id}`, `clients/updateassign/{id}` | Client actions | No page. |
| `tasks/updatestatus/{id}`, `tasks/updateassign/{id}`, `tasks/updatetime/{id}`, `tasks/invoice/{id}`, `tasks/comments/{id}` | Task actions | No page. |
| `leads/GetSeniors`, `leads/MeetingType`, `leads/Leadstatus`, `leads/nextfollowup/{id}` | Lead lookup data | No page. |
| `leads/StoreMeeting/{id}`, `leads/notes/{id}`, `leads/updateassign/{id}`, `leads/updatestatus/{id}`, `leads/updatefollowup/{id}` | Lead actions | No page. |
| `leads/bulk/export`, `leads/bulkUpload/downloadFormat` | Lead downloads | No page. |
| `getLeadDataAjax`, `get_lead`, `get_lead_data`, `get_lead_date_wise`, `get_sell_view`, `get-lead-graph-data`, `leadtype/{id?}` | Lead AJAX/report data | No page. |
| `multiple_ass_lead`, `delete_lead`, `update_lead`, `send_to_dump_lead`, `dump_multiple_ass_lead`, `reverse_*`, `push*`, `send_team_reminders` | Lead administration actions | No page. |
| `delete_*`, `save_*`, `save_edit_*` | Master-data actions | No page. |
| `notifications/*` | Header notification API/actions | No page. |
| `settings/company/general`, `settings/company/integrations*` | Company Settings save/sync actions | No page. |
| `invoices/updatepayment/*`, `invoices/reopenpayment/*`, `invoices/sentinvoice/*`, `invoices/reopensentinvoice/*`, `invoices/newitem/*` | Invoice actions | No page. |
| `curl/scheduler/auto_assign` | Scheduler token endpoint | No page. |
| `/logout`, `/cache`, password form submissions | Session/maintenance/auth actions | No internal-app layout. |

## Conversion order

1. Attendance, Tasks and Clients: frequent table pages that are still using legacy Bootstrap layout.
2. Lead sub-pages: new lead, detail, edit, sell leads, meetings, bulk upload.
3. User, scheduler, master-data and HR forms/details.
4. Finance, documents, departments and legacy settings/integrations consolidation.
5. Authentication screens and final responsive QA.

## Shared acceptance criteria

Every converted browser page must use `layouts.master`, the current sidebar/topbar shell, a zero-gap content workspace, Lead List table colors (`#f7f9fc` header, `#f1f5f9` row borders), orange primary actions, no sidebar underlines, visible active menu state, and responsive horizontal scrolling for wide tables.
