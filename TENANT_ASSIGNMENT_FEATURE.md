# Tenant Assignment Feature

## Overview

The Tenant Assignment feature allows landlords to automatically assign tenants to specific units/rooms and create user accounts for them. This eliminates the hassle for tenants by providing them with immediate access to their dashboard and streamlining the document upload process.

## Features

### For Landlords

1. **Assign Tenants to Units**
   - Select any available unit from your properties
   - Fill in tenant information (name, phone, address)
   - Set lease terms (start date, end date, rent amount, security deposit)
   - System automatically creates tenant user account with generated email/password

2. **Manage Tenant Assignments**
   - View all tenant assignments with filtering options
   - Track document upload status
   - Verify uploaded documents
   - Update assignment status (active, terminated)

3. **Statistics Dashboard**
   - Total assignments count
   - Active assignments
   - Pending document uploads
   - Total revenue from active assignments

### For Tenants

1. **Automatic Account Creation**
   - Email: `{name-slug}@housesync.tenant` (e.g., john-doe@housesync.tenant)
   - Password: Automatically generated 8-character password
   - Immediate access to tenant dashboard

2. **Document Upload System**
   - Upload required documents (Government ID, Proof of Income, etc.)
   - Multiple file formats supported (PDF, JPG, JPEG, PNG)
   - File size limit: 5MB per document
   - Real-time status tracking

3. **Dashboard Access**
   - View assignment details (unit, apartment, lease terms)
   - Track document verification status
   - Access to uploaded documents
   - Quick actions for common tasks

## Database Structure

### New Tables

1. **tenant_assignments**
   - Links tenants to units
   - Stores lease information
   - Tracks document status
   - Manages assignment lifecycle

2. **tenant_documents**
   - Stores uploaded document metadata
   - Tracks verification status
   - Links documents to assignments

### Updated Models

- **User**: Added relationships for tenant assignments
- **Unit**: Added relationship to current tenant
- **Apartment**: No changes needed

## Workflow

### 1. Landlord Assignment Process

1. Landlord navigates to Units → clicks "Assign" on available unit
2. Fills tenant assignment form with:
   - Tenant name, phone, address
   - Lease start/end dates
   - Rent amount and security deposit
   - Additional notes
3. System automatically:
   - Creates tenant user account
   - Generates unique email and password
   - Creates tenant assignment record
   - Updates unit status to "occupied"
4. Landlord receives credentials to share with tenant

### 2. Tenant Onboarding Process

1. Tenant receives login credentials from landlord
2. Tenant logs in and sees assignment details
3. Tenant uploads required documents:
   - Government ID
   - Proof of Income
   - Bank Statement
   - Character Reference
   - Rental History (if applicable)
4. System marks documents as uploaded
5. Landlord reviews and verifies documents
6. Assignment status becomes "active"

### 3. Document Management

1. **Upload**: Tenants can upload multiple documents with type selection
2. **Review**: Landlords can view and download uploaded documents
3. **Verification**: Landlords can verify documents and add notes
4. **Status Tracking**: Real-time status updates for both parties

## Routes

### Landlord Routes
- `GET /landlord/tenant-assignments` - View all assignments
- `GET /landlord/units/{unitId}/assign-tenant` - Assignment form
- `POST /landlord/units/{unitId}/assign-tenant` - Create assignment
- `GET /landlord/tenant-assignments/{id}` - Assignment details
- `PUT /landlord/tenant-assignments/{id}/status` - Update status
- `POST /landlord/tenant-assignments/{id}/verify-documents` - Verify documents

### Tenant Routes
- `GET /tenant/dashboard` - Tenant dashboard
- `GET /tenant/upload-documents` - Document upload form
- `POST /tenant/upload-documents` - Store documents
- `GET /tenant/download-document/{id}` - Download document

## Security Features

1. **Role-based Access**: Only landlords can assign tenants, only tenants can upload their documents
2. **Document Access Control**: Users can only access documents related to their assignments
3. **File Validation**: Server-side validation for file types and sizes
4. **Secure Storage**: Documents stored in public disk with proper access controls

## Benefits

### For Landlords
- Streamlined tenant onboarding
- Automated user account creation
- Centralized document management
- Better tracking of tenant status
- Reduced administrative overhead

### For Tenants
- Immediate dashboard access
- Clear document requirements
- Real-time status updates
- Easy document upload process
- No need to remember complex apartment/unit details

## Future Enhancements

1. **Email Notifications**: Automatic emails for status changes
2. **Document Templates**: Pre-filled document requirements
3. **Bulk Assignment**: Assign multiple tenants at once
4. **Lease Renewal**: Automated lease renewal process
5. **Payment Integration**: Link to payment system
6. **Mobile App**: Native mobile application support

## Technical Notes

- Uses Laravel's file storage system for document management
- Implements Firebase sync for real-time updates
- Follows Laravel best practices for models and controllers
- Responsive design for mobile compatibility
- Bootstrap 5 for UI components 