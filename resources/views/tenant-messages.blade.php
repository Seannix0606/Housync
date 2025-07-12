<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Tenant Portal</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .tenant-nav-item.active { background: #10b981; color: white; }
        .tenant-nav-item:hover { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .tenant-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .tenant-btn-primary { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; transition: all 0.2s ease; }
        .tenant-btn-primary:hover { background: #059669; }
        .messages-container { display: grid; grid-template-columns: 300px 1fr; gap: 24px; height: calc(100vh - 200px); }
        .conversations-list { background: white; border-radius: 12px; padding: 0; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .conversation-item { padding: 16px; border-bottom: 1px solid #e5e7eb; cursor: pointer; transition: all 0.2s ease; }
        .conversation-item:hover { background: #f9fafb; }
        .conversation-item.active { background: #10b981; color: white; }
        .chat-container { background: white; border-radius: 12px; display: flex; flex-direction: column; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .chat-header { padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px; }
        .chat-messages { flex: 1; padding: 20px; overflow-y: auto; }
        .message { margin-bottom: 16px; }
        .message-received { display: flex; align-items: start; gap: 12px; }
        .message-sent { display: flex; justify-content: flex-end; }
        .message-bubble { max-width: 70%; padding: 12px 16px; border-radius: 12px; }
        .message-bubble.received { background: #f3f4f6; color: #1f2937; }
        .message-bubble.sent { background: #10b981; color: white; }
        .message-time { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .chat-input { padding: 20px; border-top: 1px solid #e5e7eb; }
        .input-container { display: flex; gap: 12px; }
        .message-input { flex: 1; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; resize: none; max-height: 100px; }
        .attachment-btn { padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; background: white; cursor: pointer; }
        .send-btn { padding: 12px 20px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; }
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
                    <div class="nav-item tenant-nav-item active">
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
                    <h1 class="app-title">Messages</h1>
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
                <div class="messages-container">
                    <!-- Conversations List -->
                    <div class="conversations-list">
                        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                            <h3 style="margin: 0;">Conversations</h3>
                        </div>
                        
                        <div class="conversation-item active" onclick="loadConversation('property-manager')">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face" alt="Property Manager" style="width: 40px; height: 40px; border-radius: 50%;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 4px;">Property Manager</div>
                                    <div style="font-size: 12px; color: #6b7280;">Thanks for the payment confirmation...</div>
                                </div>
                                <div style="font-size: 12px; color: #6b7280;">2h</div>
                            </div>
                        </div>

                        <div class="conversation-item" onclick="loadConversation('maintenance')">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Maintenance Team" style="width: 40px; height: 40px; border-radius: 50%;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 4px;">Maintenance Team</div>
                                    <div style="font-size: 12px; color: #6b7280;">Your kitchen faucet repair is scheduled...</div>
                                </div>
                                <div style="font-size: 12px; color: #6b7280;">1d</div>
                            </div>
                        </div>

                        <div class="conversation-item" onclick="loadConversation('landlord')">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?w=150&h=150&fit=crop&crop=face" alt="Landlord" style="width: 40px; height: 40px; border-radius: 50%;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 4px;">Sarah Chen (Landlord)</div>
                                    <div style="font-size: 12px; color: #6b7280;">Lease renewal documents attached</div>
                                </div>
                                <div style="font-size: 12px; color: #6b7280;">3d</div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Container -->
                    <div class="chat-container">
                        <div class="chat-header">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face" alt="Property Manager" style="width: 50px; height: 50px; border-radius: 50%;">
                            <div>
                                <div style="font-weight: 600; color: #1f2937;">Property Manager</div>
                                <div style="font-size: 12px; color: #6b7280;">HouSync Management</div>
                            </div>
                            <div style="margin-left: auto;">
                                <button class="attachment-btn" onclick="startVideoCall()">
                                    <i class="fas fa-video"></i>
                                </button>
                                <button class="attachment-btn" onclick="startPhoneCall()">
                                    <i class="fas fa-phone"></i>
                                </button>
                            </div>
                        </div>

                        <div class="chat-messages" id="chatMessages">
                            <div class="message message-received">
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face" alt="Property Manager" style="width: 32px; height: 32px; border-radius: 50%;">
                                <div>
                                    <div class="message-bubble received">
                                        Hello Juan! I hope you're settling in well to Unit 01. Please let me know if you have any questions or concerns about your rental.
                                    </div>
                                    <div class="message-time">Yesterday, 10:30 AM</div>
                                </div>
                            </div>

                            <div class="message message-sent">
                                <div>
                                    <div class="message-bubble sent">
                                        Hi! Thank you for checking in. Everything is going great so far. The unit is exactly as described and I'm very happy with it.
                                    </div>
                                    <div class="message-time" style="text-align: right;">Yesterday, 11:15 AM</div>
                                </div>
                            </div>

                            <div class="message message-received">
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face" alt="Property Manager" style="width: 32px; height: 32px; border-radius: 50%;">
                                <div>
                                    <div class="message-bubble received">
                                        That's wonderful to hear! I wanted to confirm that we received your rent payment for this month. Thank you for the prompt payment.
                                    </div>
                                    <div class="message-time">Today, 9:45 AM</div>
                                </div>
                            </div>

                            <div class="message message-sent">
                                <div>
                                    <div class="message-bubble sent">
                                        Perfect! Yes, I set up auto-payment so it should be on time every month. Also, I submitted a maintenance request for the kitchen faucet - it's been dripping.
                                    </div>
                                    <div class="message-time" style="text-align: right;">Today, 10:20 AM</div>
                                </div>
                            </div>

                            <div class="message message-received">
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face" alt="Property Manager" style="width: 32px; height: 32px; border-radius: 50%;">
                                <div>
                                    <div class="message-bubble received">
                                        Thanks for the heads up! I can see the maintenance request in our system. Our technician John will be in touch with you shortly to schedule a time that works for you.
                                    </div>
                                    <div class="message-time">Today, 2:15 PM</div>
                                </div>
                            </div>
                        </div>

                        <div class="chat-input">
                            <div class="input-container">
                                <button class="attachment-btn" onclick="attachFile()">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <textarea class="message-input" id="messageInput" placeholder="Type your message..." rows="1" onkeypress="handleKeyPress(event)"></textarea>
                                <button class="send-btn" onclick="sendMessage()">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
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

            // Auto-resize textarea
            const messageInput = document.getElementById('messageInput');
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
        
        function handleLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '{{ route("login") }}';
            }
        }

        function loadConversation(type) {
            // Remove active class from all conversations
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked conversation
            event.target.closest('.conversation-item').classList.add('active');
            
            // Update chat header and messages based on conversation type
            const chatMessages = document.getElementById('chatMessages');
            
            if (type === 'maintenance') {
                updateChatHeader('Maintenance Team', 'John Smith - Technician');
                chatMessages.innerHTML = `
                    <div class="message message-received">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Maintenance" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div>
                            <div class="message-bubble received">Hi Juan, I'm John from the maintenance team. I'll be handling your kitchen faucet repair request.</div>
                            <div class="message-time">Yesterday, 2:30 PM</div>
                        </div>
                    </div>
                    <div class="message message-sent">
                        <div>
                            <div class="message-bubble sent">Hi John! Thanks for getting in touch. When would be a good time to come take a look?</div>
                            <div class="message-time" style="text-align: right;">Yesterday, 3:45 PM</div>
                        </div>
                    </div>
                    <div class="message message-received">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Maintenance" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div>
                            <div class="message-bubble received">I'm available tomorrow morning between 9 AM and 12 PM. Would that work for you?</div>
                            <div class="message-time">Today, 8:15 AM</div>
                        </div>
                    </div>
                `;
            } else if (type === 'landlord') {
                updateChatHeader('Sarah Chen (Landlord)', 'Property Owner');
                chatMessages.innerHTML = `
                    <div class="message message-received">
                        <img src="https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?w=150&h=150&fit=crop&crop=face" alt="Landlord" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div>
                            <div class="message-bubble received">Hi Juan, I hope you're enjoying the apartment! I wanted to reach out about lease renewal since we're getting close to the end date.</div>
                            <div class="message-time">3 days ago, 10:00 AM</div>
                        </div>
                    </div>
                    <div class="message message-sent">
                        <div>
                            <div class="message-bubble sent">Hi Sarah! Yes, I love living here and would definitely be interested in renewing. What are the next steps?</div>
                            <div class="message-time" style="text-align: right;">3 days ago, 11:30 AM</div>
                        </div>
                    </div>
                    <div class="message message-received">
                        <img src="https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?w=150&h=150&fit=crop&crop=face" alt="Landlord" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div>
                            <div class="message-bubble received">Great! I've attached the renewal documents to this message. The terms remain the same except for a small rent increase as discussed. Please review and let me know if you have any questions.</div>
                            <div class="message-time">3 days ago, 2:15 PM</div>
                        </div>
                    </div>
                `;
            }
        }

        function updateChatHeader(name, subtitle) {
            const chatHeader = document.querySelector('.chat-header');
            chatHeader.innerHTML = `
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="${name}" style="width: 50px; height: 50px; border-radius: 50%;">
                <div>
                    <div style="font-weight: 600; color: #1f2937;">${name}</div>
                    <div style="font-size: 12px; color: #6b7280;">${subtitle}</div>
                </div>
                <div style="margin-left: auto;">
                    <button class="attachment-btn" onclick="startVideoCall()">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="attachment-btn" onclick="startPhoneCall()">
                        <i class="fas fa-phone"></i>
                    </button>
                </div>
            `;
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (message) {
                const chatMessages = document.getElementById('chatMessages');
                const now = new Date();
                const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                const messageElement = document.createElement('div');
                messageElement.className = 'message message-sent';
                messageElement.innerHTML = `
                    <div>
                        <div class="message-bubble sent">${message}</div>
                        <div class="message-time" style="text-align: right;">Today, ${timeString}</div>
                    </div>
                `;
                
                chatMessages.appendChild(messageElement);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                messageInput.value = '';
                messageInput.style.height = 'auto';
            }
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        function attachFile() {
            alert('File attachment functionality will be implemented.');
        }

        function startVideoCall() {
            alert('Starting video call...');
        }

        function startPhoneCall() {
            alert('Starting phone call...');
        }
    </script>
</body>
</html> 