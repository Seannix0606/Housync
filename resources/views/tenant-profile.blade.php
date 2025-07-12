<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Tenant Portal</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .tenant-nav-item.active { background: #10b981; color: white; }
        .tenant-nav-item:hover { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .tenant-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .tenant-btn-primary { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; transition: all 0.2s ease; }
        .tenant-btn-primary:hover { background: #059669; }
        .profile-card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .form-group input[readonly] { background: #f9fafb; color: #6b7280; }
        .profile-avatar-large { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; }
        .avatar-upload { position: relative; display: inline-block; }
        .avatar-upload-btn { position: absolute; bottom: 0; right: 0; background: #10b981; color: white; border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .security-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f9fafb; border-radius: 8px; margin-bottom: 12px; }
        .toggle-switch { position: relative; display: inline-block; width: 50px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #10b981; }
        input:checked + .slider:before { transform: translateX(26px); }
        .notification-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        .notification-item:last-child { border-bottom: none; }
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
                    <div class="nav-item tenant-nav-item" onclick="window.location.href='{{ route('tenant.lease') }}'">
                        <i class="fas fa-file-contract"></i>
                        <span>Lease Info</span>
                    </div>
                    <div class="nav-item tenant-nav-item active">
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
                    <h1 class="app-title">Profile Settings</h1>
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
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <!-- Personal Information -->
                    <div>
                        <div class="profile-card">
                            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px;">
                                <div class="avatar-upload">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Juan Karlos" class="profile-avatar-large">
                                    <button class="avatar-upload-btn" onclick="uploadProfilePhoto()">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                                <div>
                                    <h2 style="margin: 0 0 8px 0; color: #1f2937;">Juan Karlos</h2>
                                    <p style="margin: 0 0 4px 0; color: #6b7280;">Tenant - Unit 01</p>
                                    <p style="margin: 0; color: #10b981; font-weight: 600;">Active Lease</p>
                                </div>
                            </div>

                            <h3 style="margin: 0 0 20px 0;">Personal Information</h3>
                            <form id="personalInfoForm" onsubmit="updatePersonalInfo(event)">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input type="text" id="firstName" name="firstName" value="Juan" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="lastName">Last Name</label>
                                        <input type="text" id="lastName" name="lastName" value="Karlos" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" value="juan.karlos@email.com" required>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" value="+63 917 123 4567" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="birthdate">Date of Birth</label>
                                        <input type="date" id="birthdate" name="birthdate" value="1990-05-15">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="occupation">Occupation</label>
                                    <input type="text" id="occupation" name="occupation" value="Software Developer">
                                </div>

                                <div class="form-group">
                                    <label for="company">Company</label>
                                    <input type="text" id="company" name="company" value="TechCorp Philippines">
                                </div>

                                <button type="submit" class="tenant-btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Information
                                </button>
                            </form>
                        </div>

                        <div class="profile-card">
                            <h3 style="margin: 0 0 20px 0;">Emergency Contacts</h3>
                            
                            <div style="background: #f9fafb; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;">Maria Karlos</div>
                                        <div style="color: #6b7280; font-size: 14px;">Mother</div>
                                    </div>
                                    <button style="color: #6b7280; background: none; border: none; cursor: pointer;" onclick="editEmergencyContact('primary')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                                <div style="color: #4b5563; font-size: 14px;">
                                    <div>📞 +63 917 987 6543</div>
                                    <div>📧 maria.karlos@email.com</div>
                                </div>
                            </div>

                            <div style="background: #f9fafb; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;">Roberto Santos</div>
                                        <div style="color: #6b7280; font-size: 14px;">Friend</div>
                                    </div>
                                    <button style="color: #6b7280; background: none; border: none; cursor: pointer;" onclick="editEmergencyContact('secondary')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                                <div style="color: #4b5563; font-size: 14px;">
                                    <div>📞 +63 917 456 7890</div>
                                    <div>📧 roberto.santos@email.com</div>
                                </div>
                            </div>

                            <button class="tenant-btn-primary" style="width: 100%;" onclick="addEmergencyContact()">
                                <i class="fas fa-plus"></i>
                                Add Emergency Contact
                            </button>
                        </div>
                    </div>

                    <!-- Security & Settings -->
                    <div>
                        <div class="profile-card">
                            <h3 style="margin: 0 0 20px 0;">Account Security</h3>
                            
                            <div class="security-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Two-Factor Authentication</div>
                                    <div style="color: #6b7280; font-size: 14px;">Add extra security to your account</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="toggle2FA(this)">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="security-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Email Notifications</div>
                                    <div style="color: #6b7280; font-size: 14px;">Receive important updates via email</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked onchange="toggleEmailNotifications(this)">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="security-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">SMS Notifications</div>
                                    <div style="color: #6b7280; font-size: 14px;">Receive urgent alerts via SMS</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" onchange="toggleSMSNotifications(this)">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div style="margin-top: 20px;">
                                <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="changePassword()">
                                    <i class="fas fa-key"></i>
                                    Change Password
                                </button>
                                <button style="width: 100%; padding: 12px; border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 6px; cursor: pointer;" onclick="downloadData()">
                                    <i class="fas fa-download"></i>
                                    Download My Data
                                </button>
                            </div>
                        </div>

                        <div class="profile-card">
                            <h3 style="margin: 0 0 20px 0;">Notification Preferences</h3>
                            
                            <div class="notification-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Rent Reminders</div>
                                    <div style="color: #6b7280; font-size: 14px;">Get reminded before rent is due</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Maintenance Updates</div>
                                    <div style="color: #6b7280; font-size: 14px;">Updates on your maintenance requests</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Lease Notifications</div>
                                    <div style="color: #6b7280; font-size: 14px;">Important lease-related communications</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Building Announcements</div>
                                    <div style="color: #6b7280; font-size: 14px;">General building and community updates</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Marketing Communications</div>
                                    <div style="color: #6b7280; font-size: 14px;">Special offers and promotions</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="profile-card">
                            <h3 style="margin: 0 0 20px 0;">Account Information</h3>
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #6b7280;">Account Type:</span>
                                <strong>Tenant</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #6b7280;">Member Since:</span>
                                <strong>January 2024</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #6b7280;">Last Login:</span>
                                <strong>Today, 2:30 PM</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #6b7280;">Account Status:</span>
                                <strong style="color: #10b981;">Active</strong>
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

        function uploadProfilePhoto() {
            alert('Profile photo upload functionality will be implemented.');
        }

        function updatePersonalInfo(event) {
            event.preventDefault();
            alert('Personal information has been updated successfully!');
        }

        function editEmergencyContact(type) {
            alert(`Editing ${type} emergency contact...`);
        }

        function addEmergencyContact() {
            alert('Add new emergency contact form will be opened.');
        }

        function toggle2FA(checkbox) {
            if (checkbox.checked) {
                alert('Two-factor authentication has been enabled. You will receive a verification code via SMS for future logins.');
            } else {
                if (confirm('Are you sure you want to disable two-factor authentication? This will make your account less secure.')) {
                    alert('Two-factor authentication has been disabled.');
                } else {
                    checkbox.checked = true;
                }
            }
        }

        function toggleEmailNotifications(checkbox) {
            if (checkbox.checked) {
                alert('Email notifications have been enabled.');
            } else {
                alert('Email notifications have been disabled.');
            }
        }

        function toggleSMSNotifications(checkbox) {
            if (checkbox.checked) {
                alert('SMS notifications have been enabled.');
            } else {
                alert('SMS notifications have been disabled.');
            }
        }

        function changePassword() {
            alert('Change password form will be opened.');
        }

        function downloadData() {
            if (confirm('This will generate a file containing all your personal data. Do you want to proceed?')) {
                alert('Your data export is being prepared. You will receive an email with the download link shortly.');
            }
        }
    </script>
</body>
</html> 