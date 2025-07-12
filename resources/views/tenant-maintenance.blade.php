<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Tenant Portal</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .tenant-nav-item.active { background: #10b981; color: white; }
        .tenant-nav-item:hover { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .tenant-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .tenant-btn-primary { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; transition: all 0.2s ease; }
        .tenant-btn-primary:hover { background: #059669; }
        .maintenance-card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .request-card { border-left: 4px solid #6b7280; }
        .status-open { border-left-color: #f59e0b; }
        .status-progress { border-left-color: #3b82f6; }
        .status-completed { border-left-color: #10b981; }
        .priority-high { background: #fee2e2; color: #dc2626; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-low { background: #d1fae5; color: #065f46; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .file-upload { border: 2px dashed #d1d5db; border-radius: 8px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.2s ease; }
        .file-upload:hover { border-color: #10b981; background: #f0fdf4; }
        .timeline { position: relative; padding-left: 20px; }
        .timeline::before { content: ''; position: absolute; left: 8px; top: 0; bottom: 0; width: 2px; background: #e5e7eb; }
        .timeline-item { position: relative; margin-bottom: 20px; }
        .timeline-item::before { content: ''; position: absolute; left: -16px; top: 6px; width: 12px; height: 12px; border-radius: 50%; background: #10b981; border: 2px solid white; box-shadow: 0 0 0 2px #10b981; }
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
                    <div class="nav-item tenant-nav-item active">
                        <i class="fas fa-tools"></i>
                        <span>Maintenance</span>
                    </div>
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.messages') }}'">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </div>
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.lease') }}'">
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
                    <h1 class="app-title">Maintenance Requests</h1>
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
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Maintenance Requests List -->
                    <div>
                        <div class="maintenance-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                                <h2 style="margin: 0;">Your Maintenance Requests</h2>
                                <button class="tenant-btn-primary" onclick="showNewRequestForm()">
                                    <i class="fas fa-plus"></i>
                                    New Request
                                </button>
                            </div>

                            <!-- Active Request -->
                            <div class="request-card status-progress maintenance-card">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                    <div>
                                        <h4 style="margin: 0 0 8px 0; color: #1f2937;">Leaking Faucet in Kitchen</h4>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Request #MR-2024-015 • July 15, 2024</p>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <span class="status-badge" style="background: #dbeafe; color: #1d4ed8; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">In Progress</span>
                                        <span class="priority-medium" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Medium</span>
                                    </div>
                                </div>
                                <p style="color: #4b5563; margin: 0 0 16px 0;">The kitchen faucet has been leaking for the past few days. Water is dripping constantly even when turned off completely.</p>
                                
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <strong>Technician assigned</strong> - July 16, 2024
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Maintenance technician John Smith has been assigned to your request.</p>
                                    </div>
                                    <div class="timeline-item">
                                        <strong>Request submitted</strong> - July 15, 2024
                                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Your maintenance request has been received and is under review.</p>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 16px;">
                                    <button class="tenant-btn-primary" style="padding: 8px 16px; font-size: 14px; margin-right: 8px;" onclick="contactTechnician()">Contact Technician</button>
                                    <button style="padding: 8px 16px; font-size: 14px; background: #f3f4f6; color: #374151; border: none; border-radius: 4px; cursor: pointer;" onclick="viewDetails('MR-2024-015')">View Details</button>
                                </div>
                            </div>

                            <!-- Completed Requests -->
                            <div class="request-card status-completed maintenance-card">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                    <div>
                                        <h4 style="margin: 0 0 8px 0; color: #1f2937;">Air Conditioning Not Working</h4>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Request #MR-2024-012 • June 28, 2024</p>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <span class="status-badge" style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Completed</span>
                                        <span class="priority-high" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">High</span>
                                    </div>
                                </div>
                                <p style="color: #4b5563; margin: 0 0 16px 0;">Air conditioning unit stopped working completely. Room temperature very high.</p>
                                <p style="color: #10b981; font-weight: 600; margin: 0; font-size: 14px;">✓ Completed on July 2, 2024 - AC unit repaired and tested</p>
                            </div>

                            <div class="request-card status-completed maintenance-card">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                    <div>
                                        <h4 style="margin: 0 0 8px 0; color: #1f2937;">Bathroom Light Bulb Replacement</h4>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Request #MR-2024-008 • June 10, 2024</p>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <span class="status-badge" style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Completed</span>
                                        <span class="priority-low" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Low</span>
                                    </div>
                                </div>
                                <p style="color: #4b5563; margin: 0 0 16px 0;">Main bathroom light bulb burnt out and needs replacement.</p>
                                <p style="color: #10b981; font-weight: 600; margin: 0; font-size: 14px;">✓ Completed on June 12, 2024 - Light bulb replaced</p>
                            </div>
                        </div>
                    </div>

                    <!-- New Request Form & Quick Actions -->
                    <div>
                        <div class="maintenance-card" id="newRequestForm" style="display: none;">
                            <h3 style="margin: 0 0 20px 0;">Submit New Request</h3>
                            <form onsubmit="submitMaintenanceRequest(event)">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select id="category" name="category" required>
                                        <option value="">Select category</option>
                                        <option value="plumbing">Plumbing</option>
                                        <option value="electrical">Electrical</option>
                                        <option value="hvac">HVAC</option>
                                        <option value="appliance">Appliance</option>
                                        <option value="general">General Maintenance</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="priority">Priority Level</label>
                                    <select id="priority" name="priority" required>
                                        <option value="">Select priority</option>
                                        <option value="low">Low - Can wait a few days</option>
                                        <option value="medium">Medium - Should be fixed soon</option>
                                        <option value="high">High - Urgent issue</option>
                                        <option value="emergency">Emergency - Immediate attention</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="title">Issue Title</label>
                                    <input type="text" id="title" name="title" placeholder="Brief description of the issue" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Detailed Description</label>
                                    <textarea id="description" name="description" placeholder="Please provide a detailed description of the issue, when it started, and any other relevant information..." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Attach Photos (Optional)</label>
                                    <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: #9ca3af; margin-bottom: 8px;"></i>
                                        <p style="margin: 0; color: #6b7280;">Click to upload photos</p>
                                        <p style="margin: 4px 0 0 0; color: #9ca3af; font-size: 12px;">Max 5 files, 10MB each</p>
                                    </div>
                                    <input type="file" id="fileInput" style="display: none;" multiple accept="image/*" onchange="handleFileUpload(this)">
                                </div>

                                <div style="display: flex; gap: 12px;">
                                    <button type="button" style="flex: 1; padding: 12px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer;" onclick="hideNewRequestForm()">Cancel</button>
                                    <button type="submit" class="tenant-btn-primary" style="flex: 1;">Submit Request</button>
                                </div>
                            </form>
                        </div>

                        <div class="maintenance-card" id="quickActions">
                            <h4 style="margin: 0 0 16px 0;">Quick Actions</h4>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="showNewRequestForm()">
                                <i class="fas fa-plus"></i>
                                New Maintenance Request
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="viewAllRequests()">
                                <i class="fas fa-list"></i>
                                View All Requests
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="emergencyContact()">
                                <i class="fas fa-exclamation-triangle"></i>
                                Emergency Contact
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%;" onclick="maintenanceGuide()">
                                <i class="fas fa-book"></i>
                                Maintenance Guide
                            </button>
                        </div>

                        <div class="maintenance-card">
                            <h4 style="margin: 0 0 16px 0;">Emergency Contacts</h4>
                            <div style="margin-bottom: 12px;">
                                <strong>Maintenance Emergency:</strong><br>
                                <span style="color: #ef4444;">+63 917 123 4567</span>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <strong>Property Manager:</strong><br>
                                <span style="color: #10b981;">+63 917 765 4321</span>
                            </div>
                            <div>
                                <strong>Hours:</strong><br>
                                <span style="color: #6b7280;">24/7 Emergency<br>Regular: 8AM - 6PM</span>
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

        function showNewRequestForm() {
            document.getElementById('newRequestForm').style.display = 'block';
            document.getElementById('quickActions').style.display = 'none';
        }

        function hideNewRequestForm() {
            document.getElementById('newRequestForm').style.display = 'none';
            document.getElementById('quickActions').style.display = 'block';
        }

        function submitMaintenanceRequest(event) {
            event.preventDefault();
            const title = document.getElementById('title').value;
            const category = document.getElementById('category').value;
            const priority = document.getElementById('priority').value;
            
            alert(`Maintenance request "${title}" has been submitted!\nCategory: ${category}\nPriority: ${priority}\n\nYou will receive a confirmation email shortly.`);
            
            // Reset form and hide
            event.target.reset();
            hideNewRequestForm();
        }

        function handleFileUpload(input) {
            const files = input.files;
            if (files.length > 0) {
                const fileNames = Array.from(files).map(file => file.name).join(', ');
                alert(`Selected files: ${fileNames}`);
            }
        }

        function contactTechnician() {
            alert('Opening contact form for technician John Smith...');
        }

        function viewDetails(requestId) {
            alert(`Viewing detailed information for request ${requestId}...`);
        }

        function viewAllRequests() {
            alert('Loading complete request history...');
        }

        function emergencyContact() {
            if (confirm('Do you need to call emergency maintenance?\nPhone: +63 917 123 4567')) {
                alert('Connecting to emergency maintenance...');
            }
        }

        function maintenanceGuide() {
            alert('Opening maintenance and troubleshooting guide...');
        }
    </script>
</body>
</html> 