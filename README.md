# Purchasing System (PPD)

This is a Laravel-based purchasing system with RFQ (Request for Quotation) functionality.

## Features

- Role-Based Access Control (RBAC)
- Request for Quotation (RFQ) management
- Dropdown management (Markets, Order Types, Proposed Methods)
- Bilingual support (English/Arabic)
- Audit trail
- User management
- Vendor management

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed database: `php artisan db:seed`
7. Serve the application: `php artisan serve`

## RFQ Implementation Details

The RFQ system has been implemented to match the structure specified in the Excel template (autocad rfq.xls) with the following header fields:

- Title (Request title)
- Project
- Vessel
- Requesting Department
- Location
- Cost Center
- Contact Name
- Contact Code
- Material Req No
- Requisition Rec Date
- Preparation Date
- Service Delivery Period
- Market (Local/Foreign) - Dropdown
- Partial Order (Accepted/Not Accepted) - Dropdown
- Proposed Method (tender types) - Dropdown
- Reference Order No
- Estimated Budget
- Needed By
- Description
- Priority
- Currency
- Notes

All fields are properly validated and integrated with the existing RFQ workflow (submit → approval → vendor assignment).

Dropdown management is available for:
- Markets (Local/Foreign)
- Order Types (Accepted/Not Accepted)
- Proposed Methods (Various tender types)

Role-based permissions:
- RFQ Requester/Officer: Can view dropdowns and create/edit RFQs
- Vendor List Manager: Can manage dropdowns (create/edit/delete)
- Administrator: Full access

The system is fully bilingual (English/Arabic) with all translations provided.

## Database Structure

The following tables were created/extended:
- markets
- order_types
- proposed_methods
- rfqs (extended with all header fields from Excel template)
- rfq_items
- users, roles, permissions (existing RBAC system)

## Testing

The implementation has been tested with:
- Database migrations and seeding
- Authentication and authorization
- RFQ creation and editing
- Form validation
- Dropdown population
- Language switching

## License

MIT