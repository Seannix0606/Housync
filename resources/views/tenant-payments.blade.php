<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Tenant Portal</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .tenant-nav-item.active { background: #10b981; color: white; }
        .tenant-nav-item:hover { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .tenant-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .tenant-btn-primary { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; transition: all 0.2s ease; }
        .tenant-btn-primary:hover { background: #059669; }
        .payment-card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .payment-due { border-left: 4px solid #f59e0b; }
        .payment-paid { border-left: 4px solid #10b981; }
        .payment-overdue { border-left: 4px solid #ef4444; }
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .status-due { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
        .amount-large { font-size: 32px; font-weight: 700; color: #1f2937; }
        .payment-method { display: flex; align-items: center; gap: 12px; padding: 16px; border: 2px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s ease; }
        .payment-method:hover { border-color: #10b981; }
        .payment-method.selected { border-color: #10b981; background: #f0fdf4; }
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
                    <div class="nav-item tenant-nav-item active">
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
                    <h1 class="app-title">Payments</h1>
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
                <!-- Current Payment Due -->
                <div class="payment-card payment-due">
                    <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h2 style="margin: 0 0 8px 0; color: #1f2937;">Current Payment Due</h2>
                            <p style="margin: 0; color: #6b7280;">Due Date: August 1, 2024</p>
                        </div>
                        <span class="status-badge status-due">Due Soon</span>
                    </div>
                    <div class="amount-large">₱8,500.00</div>
                    <p style="color: #6b7280; margin: 8px 0 20px 0;">Monthly Rent - Unit 01</p>
                    <button class="tenant-btn-primary" onclick="openPaymentModal()">
                        <i class="fas fa-credit-card"></i>
                        Pay Now
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Payment History -->
                    <div class="payment-card">
                        <h3 style="margin: 0 0 20px 0;">Payment History</h3>
                        <div class="data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>July 1, 2024</td>
                                        <td>Monthly Rent - Unit 01</td>
                                        <td>₱8,500.00</td>
                                        <td><span class="status-badge status-paid">Paid</span></td>
                                        <td><button class="tenant-btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="downloadReceipt('July2024')">Receipt</button></td>
                                    </tr>
                                    <tr>
                                        <td>June 1, 2024</td>
                                        <td>Monthly Rent - Unit 01</td>
                                        <td>₱8,500.00</td>
                                        <td><span class="status-badge status-paid">Paid</span></td>
                                        <td><button class="tenant-btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="downloadReceipt('June2024')">Receipt</button></td>
                                    </tr>
                                    <tr>
                                        <td>May 1, 2024</td>
                                        <td>Monthly Rent - Unit 01</td>
                                        <td>₱8,500.00</td>
                                        <td><span class="status-badge status-paid">Paid</span></td>
                                        <td><button class="tenant-btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="downloadReceipt('May2024')">Receipt</button></td>
                                    </tr>
                                    <tr>
                                        <td>Apr 1, 2024</td>
                                        <td>Monthly Rent - Unit 01</td>
                                        <td>₱8,500.00</td>
                                        <td><span class="status-badge status-paid">Paid</span></td>
                                        <td><button class="tenant-btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="downloadReceipt('Apr2024')">Receipt</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div>
                        <div class="payment-card">
                            <h4 style="margin: 0 0 16px 0;">Payment Summary</h4>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span>Monthly Rent:</span>
                                <strong>₱8,500</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span>Security Deposit:</span>
                                <strong>₱17,000</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span>Last Payment:</span>
                                <strong>July 1, 2024</strong>
                            </div>
                            <hr style="margin: 16px 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Next Due:</span>
                                <strong style="color: #f59e0b;">Aug 1, 2024</strong>
                            </div>
                        </div>

                        <div class="payment-card">
                            <h4 style="margin: 0 0 16px 0;">Quick Actions</h4>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="setupAutoPayment()">
                                <i class="fas fa-sync-alt"></i>
                                Setup Auto-Payment
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="paymentSettings()">
                                <i class="fas fa-cog"></i>
                                Payment Settings
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%;" onclick="downloadAllReceipts()">
                                <i class="fas fa-download"></i>
                                Download All Receipts
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 12px; padding: 32px; width: 90%; max-width: 500px;">
            <h3 style="margin: 0 0 24px 0;">Pay Rent - ₱8,500.00</h3>
            
            <h4 style="margin: 0 0 16px 0;">Select Payment Method</h4>
            <div class="payment-method selected" onclick="selectPaymentMethod(this)">
                <i class="fas fa-credit-card" style="color: #10b981;"></i>
                <div>
                    <div style="font-weight: 600;">Credit/Debit Card</div>
                    <div style="font-size: 12px; color: #6b7280;">Instant payment</div>
                </div>
                <i class="fas fa-check" style="color: #10b981; margin-left: auto;"></i>
            </div>
            
            <div class="payment-method" onclick="selectPaymentMethod(this)">
                <i class="fas fa-university" style="color: #6b7280;"></i>
                <div>
                    <div style="font-weight: 600;">Bank Transfer</div>
                    <div style="font-size: 12px; color: #6b7280;">1-2 business days</div>
                </div>
            </div>
            
            <div class="payment-method" onclick="selectPaymentMethod(this)">
                <i class="fas fa-mobile-alt" style="color: #6b7280;"></i>
                <div>
                    <div style="font-weight: 600;">GCash</div>
                    <div style="font-size: 12px; color: #6b7280;">Instant payment</div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button style="flex: 1; padding: 12px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer;" onclick="closePaymentModal()">Cancel</button>
                <button class="tenant-btn-primary" style="flex: 1;" onclick="processPayment()">Pay Now</button>
            </div>
        </div>
    </div>

    <script>
        // Sidebar functionality
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

        function openPaymentModal() {
            document.getElementById('paymentModal').style.display = 'block';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        function selectPaymentMethod(element) {
            document.querySelectorAll('.payment-method').forEach(method => {
                method.classList.remove('selected');
                method.querySelector('.fas.fa-check').style.display = 'none';
            });
            element.classList.add('selected');
            element.querySelector('.fas.fa-check').style.display = 'block';
        }

        function processPayment() {
            const selectedMethod = document.querySelector('.payment-method.selected').querySelector('div div').textContent;
            alert(`Processing payment of ₱8,500.00 via ${selectedMethod}...`);
            closePaymentModal();
            // Simulate payment success
            setTimeout(() => {
                alert('Payment successful! Your receipt has been sent to your email.');
            }, 2000);
        }

        function downloadReceipt(period) {
            alert(`Downloading receipt for ${period}...`);
        }

        function setupAutoPayment() {
            alert('Auto-payment setup functionality will be implemented.');
        }

        function paymentSettings() {
            alert('Payment settings functionality will be implemented.');
        }

        function downloadAllReceipts() {
            alert('Downloading all receipts...');
        }
    </script>
</body>
</html> 