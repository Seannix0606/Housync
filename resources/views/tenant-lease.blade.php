<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lease Information - Tenant Portal</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .tenant-nav-item.active { background: #10b981; color: white; }
        .tenant-nav-item:hover { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .tenant-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .tenant-btn-primary { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; transition: all 0.2s ease; }
        .tenant-btn-primary:hover { background: #059669; }
        .lease-card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
        .info-item { padding: 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #10b981; }
        .document-item { display: flex; align-items: center; gap: 12px; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; transition: all 0.2s ease; }
        .document-item:hover { border-color: #10b981; background: #f0fdf4; }
        .status-active { background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-expiring { background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .progress-bar { width: 100%; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; background: #10b981; transition: width 0.3s ease; }
        .timeline-item { display: flex; gap: 16px; margin-bottom: 20px; }
        .timeline-date { min-width: 100px; color: #6b7280; font-size: 14px; padding-top: 2px; }
        .timeline-content { flex: 1; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-content">
                <div class="nav-items-container">
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.dashboard') }}'">
                        <i class="fas fa-home"></i>
                        <span>My Home</span>
                    </div>
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.payments') }}'">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                    </div>
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.maintenance') }}'">
                        <i class="fas fa-tools"></i>
                        <span>Maintenance</span>
                    </div>
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.messages') }}'">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </div>
                    <div class="nav-item tenant-nav-item active">
                        <i class="fas fa-file-contract"></i>
                        <span>Lease Info</span>
                    </div>
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.profile') }}'">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </div>
                </div>
                <div class="nav-bottom">
                    <div class="nav-item logout-item" onclick="handleLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <header class="header tenant-header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="header-center">
                    <h1 class="app-title">Lease Information</h1>
                </div>
                <div class="header-right">
                    <button class="header-btn">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="user-profile">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Juan Karlos" class="profile-avatar">
                        <span class="profile-name">Juan Karlos</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Lease Status Overview -->
                <div class="lease-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
                        <div>
                            <h2 style="margin: 0 0 8px 0; color: #1f2937;">Current Lease Agreement</h2>
                            <p style="margin: 0; color: #6b7280;">Unit 01 - HouSync Apartments</p>
                        </div>
                        <span class="status-active">Active Lease</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: center; margin-bottom: 24px;">
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #6b7280;">Lease Progress</span>
                                <span style="color: #1f2937; font-weight: 600;">7 of 12 months completed</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 58%;"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 12px; color: #6b7280;">
                                <span>Start: Jan 1, 2024</span>
                                <span>End: Dec 31, 2024</span>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 32px; font-weight: 700; color: #1f2937;">147</div>
                            <div style="color: #6b7280; font-size: 14px;">days remaining</div>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">Monthly Rent</div>
                            <div style="font-size: 24px; font-weight: 700; color: #10b981;">₱8,500</div>
                            <div style="font-size: 12px; color: #6b7280;">Due 1st of each month</div>
                        </div>
                        
                        <div class="info-item">
                            <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">Security Deposit</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1f2937;">₱17,000</div>
                            <div style="font-size: 12px; color: #6b7280;">Refundable upon move-out</div>
                        </div>
                        
                        <div class="info-item">
                            <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">Lease Term</div>
                            <div style="font-size: 24px; font-weight: 700; color: #1f2937;">12 Months</div>
                            <div style="font-size: 12px; color: #6b7280;">January 1 - December 31, 2024</div>
                        </div>
                        
                        <div class="info-item">
                            <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">Renewal Option</div>
                            <div style="font-size: 24px; font-weight: 700; color: #f59e0b;">Available</div>
                            <div style="font-size: 12px; color: #6b7280;">30 days notice required</div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Lease Details -->
                    <div>
                        <div class="lease-card">
                            <h3 style="margin: 0 0 20px 0;">Lease Details</h3>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                                <div>
                                    <h4 style="margin: 0 0 12px 0; color: #1f2937;">Property Information</h4>
                                    <div style="margin-bottom: 8px;"><strong>Address:</strong> 123 Main Street, Unit 01</div>
                                    <div style="margin-bottom: 8px;"><strong>City:</strong> Makati, Metro Manila</div>
                                    <div style="margin-bottom: 8px;"><strong>Property Type:</strong> 1 Bedroom Apartment</div>
                                    <div style="margin-bottom: 8px;"><strong>Floor Area:</strong> 35 sqm</div>
                                    <div style="margin-bottom: 8px;"><strong>Furnished:</strong> Partially Furnished</div>
                                </div>
                                
                                <div>
                                    <h4 style="margin: 0 0 12px 0; color: #1f2937;">Landlord Information</h4>
                                    <div style="margin-bottom: 8px;"><strong>Name:</strong> Sarah Chen</div>
                                    <div style="margin-bottom: 8px;"><strong>Company:</strong> HouSync Management</div>
                                    <div style="margin-bottom: 8px;"><strong>Phone:</strong> +63 917 765 4321</div>
                                    <div style="margin-bottom: 8px;"><strong>Email:</strong> sarah.chen@housync.com</div>
                                    <div style="margin-bottom: 8px;"><strong>Property Manager:</strong> Yes</div>
                                </div>
                            </div>
                            
                            <h4 style="margin: 0 0 12px 0; color: #1f2937;">Terms & Conditions</h4>
                            <div style="background: #f9fafb; padding: 16px; border-radius: 8px;">
                                <ul style="margin: 0; padding-left: 20px; color: #4b5563;">
                                    <li>No smoking allowed inside the unit</li>
                                    <li>Pets allowed with additional deposit (₱5,000)</li>
                                    <li>Maximum 2 occupants permitted</li>
                                    <li>Utilities included: Water, Trash collection</li>
                                    <li>Utilities not included: Electricity, Internet</li>
                                    <li>Parking space included (1 slot)</li>
                                    <li>30-day notice required for lease termination</li>
                                    <li>Late payment fee: ₱500 after 5 days grace period</li>
                                </ul>
                            </div>
                        </div>

                        <div class="lease-card">
                            <h3 style="margin: 0 0 20px 0;">Lease Timeline</h3>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-date">Jan 1, 2024</div>
                                    <div class="timeline-content">
                                        <strong>Lease Started</strong>
                                        <p style="margin: 4px 0 0 0; color: #6b7280;">Initial lease agreement signed and security deposit paid</p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-date">July 15, 2024</div>
                                    <div class="timeline-content">
                                        <strong>Mid-term Inspection</strong>
                                        <p style="margin: 4px 0 0 0; color: #6b7280;">Routine property inspection completed - no issues found</p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-date">Nov 1, 2024</div>
                                    <div class="timeline-content">
                                        <strong>Renewal Notice Due</strong>
                                        <p style="margin: 4px 0 0 0; color: #f59e0b;">Decision on lease renewal must be made by this date</p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-date">Dec 31, 2024</div>
                                    <div class="timeline-content">
                                        <strong>Lease Expiration</strong>
                                        <p style="margin: 4px 0 0 0; color: #6b7280;">Current lease agreement ends</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents & Actions -->
                    <div>
                        <div class="lease-card">
                            <h3 style="margin: 0 0 20px 0;">Lease Documents</h3>
                            
                            <div class="document-item" onclick="downloadDocument('lease-agreement')">
                                <i class="fas fa-file-contract" style="color: #10b981; font-size: 24px;"></i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1f2937;">Lease Agreement</div>
                                    <div style="font-size: 12px; color: #6b7280;">Signed January 1, 2024</div>
                                </div>
                                <i class="fas fa-download" style="color: #6b7280;"></i>
                            </div>
                            
                            <div class="document-item" onclick="downloadDocument('move-in-checklist')">
                                <i class="fas fa-clipboard-list" style="color: #10b981; font-size: 24px;"></i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1f2937;">Move-in Checklist</div>
                                    <div style="font-size: 12px; color: #6b7280;">Completed January 1, 2024</div>
                                </div>
                                <i class="fas fa-download" style="color: #6b7280;"></i>
                            </div>
                            
                            <div class="document-item" onclick="downloadDocument('property-rules')">
                                <i class="fas fa-list-ul" style="color: #10b981; font-size: 24px;"></i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1f2937;">Property Rules</div>
                                    <div style="font-size: 12px; color: #6b7280;">Building policies and regulations</div>
                                </div>
                                <i class="fas fa-download" style="color: #6b7280;"></i>
                            </div>
                            
                            <div class="document-item" onclick="downloadDocument('insurance-info')">
                                <i class="fas fa-shield-alt" style="color: #10b981; font-size: 24px;"></i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #1f2937;">Insurance Information</div>
                                    <div style="font-size: 12px; color: #6b7280;">Recommended renters insurance</div>
                                </div>
                                <i class="fas fa-download" style="color: #6b7280;"></i>
                            </div>
                        </div>

                        <div class="lease-card">
                            <h4 style="margin: 0 0 16px 0;">Lease Actions</h4>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="requestRenewal()">
                                <i class="fas fa-redo"></i>
                                Request Lease Renewal
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="scheduleInspection()">
                                <i class="fas fa-search"></i>
                                Schedule Inspection
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="requestModification()">
                                <i class="fas fa-edit"></i>
                                Request Modification
                            </button>
                            <button style="width: 100%; padding: 12px; border: 1px solid #ef4444; background: white; color: #ef4444; border-radius: 6px; cursor: pointer;" onclick="noticeToVacate()">
                                <i class="fas fa-door-open"></i>
                                Notice to Vacate
                            </button>
                        </div>

                        <div class="lease-card">
                            <h4 style="margin: 0 0 16px 0;">Important Reminders</h4>
                            <div style="background: #fef3c7; padding: 16px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <div style="font-weight: 600; color: #92400e; margin-bottom: 8px;">Lease Renewal Decision</div>
                                <div style="color: #92400e; font-size: 14px;">You need to decide on lease renewal by November 1st, 2024 (87 days remaining)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            const sidebarState = localStorage.getItem('sidebarExpanded');
            if (sidebarState === 'true') {
                sidebar.classList.add('collapsed');
            }
            
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                const isExpanded = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarExpanded', isExpanded);
            });
        });
        
        function handleLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '{{ route("login") }}';
            }
        }

        function downloadDocument(docType) {
            const docNames = {
                'lease-agreement': 'Lease Agreement - Unit 01.pdf',
                'move-in-checklist': 'Move-in Checklist - Unit 01.pdf',
                'property-rules': 'Property Rules and Regulations.pdf',
                'insurance-info': 'Renters Insurance Information.pdf'
            };
            
            alert(`Downloading ${docNames[docType]}...`);
        }

        function requestRenewal() {
            if (confirm('Would you like to request a lease renewal? This will send a notification to your landlord.')) {
                alert('Lease renewal request has been sent to your landlord. You will receive a response within 5-7 business days.');
            }
        }

        function scheduleInspection() {
            alert('Opening inspection scheduling form...');
        }

        function requestModification() {
            alert('Opening lease modification request form...');
        }

        function noticeToVacate() {
            if (confirm('Are you sure you want to provide notice to vacate? This action cannot be undone and will start the move-out process.')) {
                alert('Notice to vacate form will be opened. Please note that 30 days notice is required as per your lease agreement.');
            }
        }
    </script>
</body>
</html> 