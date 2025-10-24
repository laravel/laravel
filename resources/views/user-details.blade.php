@extends('layouts.app')

@section('title', 'User Details - MIE Admin Dashboard')
@section('page-title', 'User Details')

@push('styles')
<style>
        .user-detailed-main {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            padding: 40px 32px;
            max-width: 1200px;
            margin: 40px auto 24px auto;
        }
        .user-detailed-left {
            flex: 0 0 360px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 300px;
        }
        .user-detailed-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--border-grey);
            margin-bottom: 16px;
        }
        .user-detailed-name {
            font-size: 22px;
            font-weight: 600;
            color: var(--intro-blue);
            margin-bottom: 4px;
            text-align: center;
        }
        .user-detailed-role {
            color: #888;
            font-size: 14px;
            margin-bottom: 24px;
            text-align: center;
            padding: 4px 12px;
            background: #f8f9fa;
            border-radius: 16px;
            text-transform: capitalize;
        }
        .user-detailed-info-list {
            width: 100%;
            margin-bottom: 24px;
        }
        .user-detailed-info-list dt {
            font-weight: 500;
            color: var(--charcoal);
            font-size: 14px;
            margin-bottom: 4px;
        }
        .user-detailed-info-list dd {
            margin: 0 0 16px 0;
            font-size: 15px;
            color: #444;
            word-break: break-word;
        }
        .user-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        .user-status.active {
            color: #10b981;
        }
        .user-status.inactive {
            color: #ef4444;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .status-dot.active {
            background-color: #10b981;
        }
        .status-dot.inactive {
            background-color: #ef4444;
        }
        .user-detailed-actions {
            margin-top: 24px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .user-detailed-right {
            flex: 1 1 500px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 32px;
            min-width: 400px;
            display: flex;
            flex-direction: column;
        }
        .user-detailed-section-title {
            color: var(--intro-blue);
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .snaps-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .snap-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .snap-item:hover {
            background: #eef2ff;
            transform: translateY(-2px);
        }
        .snap-item-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
        }
        .snap-item-image {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
        }
        .snap-item-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .snap-item-date {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        .snap-item-function {
            font-size: 13px;
            color: var(--intro-blue);
            background: rgba(18, 65, 145, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 4px;
        }
        .snap-item-response {
            font-size: 14px;
            color: #444;
            line-height: 1.5;
            margin-top: 8px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        .tabs {
            display: flex;
            gap: 16px;
            margin: 16px 0 0 32px;
        }
        .tabs a {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            color: #4b5563;
            text-decoration: none;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .tabs a:hover {
            background: rgba(18, 65, 145, 0.1);
        }
        .tabs a.active {
            background: var(--intro-blue);
            color: #fff;
        }
        
        /* Edit mode styles */
        .edit-mode .user-detailed-left {
            flex: 1 1 100%;
            min-width: 0;
        }
        .edit-mode .user-detailed-right {
            display: none;
        }
        .edit-form {
            display: none;
            width: 100%;
            max-width: 600px;
        }
        .edit-mode .edit-form {
            display: block;
        }
        .edit-mode .view-mode {
            display: none;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--charcoal);
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--intro-blue);
            box-shadow: 0 0 0 3px rgba(18, 65, 145, 0.1);
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        .avatar-upload {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .avatar-upload input[type="file"] {
            display: none;
        }
        .avatar-upload label {
            cursor: pointer;
            padding: 8px 16px;
            background: var(--intro-blue);
            color: white;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }
        .avatar-upload label:hover {
            background: #0e3d82;
        }
        
        @media (max-width: 900px) {
            .tabs {
                margin-left: 0;
                padding: 0 16px;
            }
            .user-detailed-main {
                flex-direction: column;
                gap: 24px;
                padding: 24px 16px;
            }
            .user-detailed-right,
            .user-detailed-left {
                min-width: 0;
                width: 100%;
                padding: 24px 16px;
            }
        }
    </style>
@endpush

@section('content')
<div id="mainContainer">
    <div class="tabs">
        <a href="{{ route('user-management.dashboard') }}">Users</a>
        <a href="{{ route('user-details') }}" class="active">User Details</a>
    </div>

    <div class="user-detailed-main">
        <div class="user-detailed-left">
            <!-- View Mode -->
            <div class="view-mode">
                <img id="userAvatar" src="https://i.pravatar.cc/120?u=1" alt="User Avatar" class="user-detailed-avatar" />
                <div id="userName" class="user-detailed-name">Jeanette Prosacco</div>
                <div id="userRole" class="user-detailed-role">Administrator</div>

                <dl class="user-detailed-info-list">
                    <dt>Email</dt>
                    <dd id="userEmail">Arnoldo.Bayer51@yahoo.com</dd>
                    <dt>Phone</dt>
                    <dd id="userPhone">+1234567890</dd>
                    <dt>Department</dt>
                    <dd id="userDepartment">Administration</dd>
                    <dt>Location</dt>
                    <dd id="userLocation">New York</dd>
                    <dt>Status</dt>
                    <dd>
                        <span id="userStatus" class="user-status active">
                            <span class="status-dot active"></span>
                            Active
                        </span>
                    </dd>
                    <dt>Member Since</dt>
                    <dd id="userJoinDate">January 15, 2023</dd>
                    <dt>Last Login</dt>
                    <dd id="userLastLogin">December 10, 2024</dd>
                </dl>

                <div class="user-detailed-actions">
                    <button class="btn btn-primary" onclick="toggleEditMode()" id="editBtn">
                        <i data-feather="edit-2"></i>
                        Edit User Details
                    </button>
                    <button class="btn btn-outline" onclick="resetPassword()">
                        <i data-feather="key"></i>
                        Reset Password
                    </button>
                    <a href="{{ route('user-management.dashboard') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i>
                        Back to Users
                    </a>
                </div>
            </div>

            <!-- Edit Mode -->
            <div class="edit-form">
                <form id="userEditForm" onsubmit="return false;">
                    <div class="form-group">
                        <label for="editAvatar">Profile Image</label>
                        <div class="avatar-upload">
                            <img id="editAvatarPreview" src="https://i.pravatar.cc/120?u=1" alt="Profile Image" class="user-detailed-avatar">
                            <input type="file" id="editAvatar" accept="image/*" onchange="previewImage(this)">
                            <label for="editAvatar">Choose Photo</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editFirstName">First Name</label>
                        <input type="text" id="editFirstName" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="editLastName">Last Name</label>
                        <input type="text" id="editLastName" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="editEmail">Email Address</label>
                        <input type="email" id="editEmail" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="editPhone">Phone Number</label>
                        <input type="tel" id="editPhone" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="editRole">Role</label>
                        <select id="editRole" class="form-control" required>
                            <option value="user">User</option>
                            <option value="admin">Administrator</option>
                            <option value="manager">Manager</option>
                            <option value="moderator">Moderator</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editDepartment">Department</label>
                        <input type="text" id="editDepartment" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="editLocation">Location</label>
                        <input type="text" id="editLocation" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="editPassword">New Password (leave blank to keep current)</label>
                        <input type="password" id="editPassword" class="form-control" placeholder="Enter new password">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="editActive">
                            Active User
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="cancelEdit()">
                            <i data-feather="x"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" onclick="saveUserDetails()">
                            <i data-feather="save"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="user-detailed-right">
            <div class="user-detailed-section-title">
                <i data-feather="image"></i>
                User's Snaps
            </div>
            <div id="snapsList" class="snaps-list">
                <!-- Snaps will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
        // Initialize Feather icons
        feather.replace();

        const snapDetailsBaseUrl = '{{ route('snaps') }}';

        // Global variables
        let currentUserId = null;
        let isEditMode = false;

        // Mock data
        const mockUserData = {
            1: {
                firstName: 'Jeanette',
                lastName: 'Prosacco',
                email: 'Arnoldo.Bayer51@yahoo.com',
                phone: '+1234567890',
                role: 'admin',
                department: 'Administration',
                location: 'New York',
                active: true,
                profileImage: 'https://i.pravatar.cc/120?u=1',
                joinDate: 'January 15, 2023',
                lastLogin: 'December 10, 2024'
            },
            2: {
                firstName: 'John',
                lastName: 'Smith',
                email: 'john.smith@example.com',
                phone: '+1987654321',
                role: 'user',
                department: 'Sales',
                location: 'Los Angeles',
                active: true,
                profileImage: 'https://i.pravatar.cc/120?u=2',
                joinDate: 'March 22, 2023',
                lastLogin: 'December 9, 2024'
            },
            3: {
                firstName: 'Sarah',
                lastName: 'Johnson',
                email: 'sarah.j@example.com',
                phone: '+1122334455',
                role: 'manager',
                department: 'Marketing',
                location: 'Chicago',
                active: false,
                profileImage: 'https://i.pravatar.cc/120?u=3',
                joinDate: 'July 8, 2023',
                lastLogin: 'November 30, 2024'
            }
        };

        const mockUserSnaps = {
            1: [
                {
                    id: 1,
                    date: '10/06/2024',
                    function: 'Camera',
                    image: "{{ asset('assets/images/Screenshot 2025-06-09 125116.jpg') }}",
                    response: 'dY?� Where Do Kids Live? This is a comprehensive guide about housing for children...',
                    howEasy: 'Easiest'
                },
                {
                    id: 2,
                    date: '08/06/2024',
                    function: 'File Upload',
                    image: 'https://i.pravatar.cc/80?u=snap2',
                    response: 'This document contains information about educational programs...',
                    howEasy: 'Easy'
                },
                {
                    id: 3,
                    date: '05/06/2024',
                    function: 'Camera',
                    image: 'https://i.pravatar.cc/80?u=snap3',
                    response: 'Quick response about daily activities and schedules...',
                    howEasy: 'Medium'
                }
            ],
            2: [
                {
                    id: 4,
                    date: '09/06/2024',
                    function: 'Voice Recording',
                    image: 'https://i.pravatar.cc/80?u=snap4',
                    response: 'Audio transcription and analysis results...',
                    howEasy: 'Easy'
                }
            ],
            3: []
        };

        // Load user details when page loads
        function loadUserDetails() {
            const urlParams = new URLSearchParams(window.location.search);
            currentUserId = urlParams.get('id') || '1';

            const userData = mockUserData[currentUserId];
            if (userData) {
                updateUserDisplay(userData);
                loadUserSnaps(currentUserId);
            } else {
                console.error('User not found');
            }
        }

        // Update user display
        function updateUserDisplay(userData) {
            document.getElementById('userAvatar').src = userData.profileImage;
            document.getElementById('editAvatarPreview').src = userData.profileImage;
            document.getElementById('userName').textContent = `${userData.firstName} ${userData.lastName}`;
            document.getElementById('userRole').textContent = getRoleDisplayName(userData.role);
            document.getElementById('userEmail').textContent = userData.email;
            document.getElementById('userPhone').textContent = userData.phone;
            document.getElementById('userDepartment').textContent = userData.department;
            document.getElementById('userLocation').textContent = userData.location;
            document.getElementById('userJoinDate').textContent = userData.joinDate;
            document.getElementById('userLastLogin').textContent = userData.lastLogin;

            // Update status
            const statusElement = document.getElementById('userStatus');
            const statusDot = statusElement.querySelector('.status-dot');
            if (userData.active) {
                statusElement.className = 'user-status active';
                statusDot.className = 'status-dot active';
                statusElement.innerHTML = '<span class="status-dot active"></span>Active';
            } else {
                statusElement.className = 'user-status inactive';
                statusDot.className = 'status-dot inactive';
                statusElement.innerHTML = '<span class="status-dot inactive"></span>Inactive';
            }

            // Populate edit form
            document.getElementById('editFirstName').value = userData.firstName;
            document.getElementById('editLastName').value = userData.lastName;
            document.getElementById('editEmail').value = userData.email;
            document.getElementById('editPhone').value = userData.phone;
            document.getElementById('editRole').value = userData.role;
            document.getElementById('editDepartment').value = userData.department;
            document.getElementById('editLocation').value = userData.location;
            document.getElementById('editActive').checked = userData.active;
        }

        // Load user's snaps
        function loadUserSnaps(userId) {
            const snaps = mockUserSnaps[userId] || [];
            const snapsList = document.getElementById('snapsList');

            if (snaps.length === 0) {
                snapsList.innerHTML = `
                    <div class="empty-state">
                        <i data-feather="image"></i>
                        <h3>No Snaps Found</h3>
                        <p>This user hasn't created any snaps yet.</p>
                    </div>
                `;
                feather.replace();
                return;
            }

            snapsList.innerHTML = snaps.map(snap => `
                <div class="snap-item" onclick="viewSnapDetails(${snap.id})">
                    <div class="snap-item-header">
                        <img src="${snap.image}" alt="Snap preview" class="snap-item-image">
                        <div class="snap-item-info">
                            <div class="snap-item-date">${snap.date}</div>
                            <div class="snap-item-function">${snap.function}</div>
                        </div>
                    </div>
                    <div class="snap-item-response">
                        ${snap.response.length > 100 ? snap.response.substring(0, 100) + '...' : snap.response}
                    </div>
                </div>
            `).join('');
        }

        // Toggle edit mode
        function toggleEditMode() {
            isEditMode = !isEditMode;
            const mainContainer = document.getElementById('mainContainer');

            if (isEditMode) {
                mainContainer.classList.add('edit-mode');
            } else {
                mainContainer.classList.remove('edit-mode');
            }

            feather.replace();
        }

        // Cancel edit
        function cancelEdit() {
            toggleEditMode();
            // Reload original data
            loadUserDetails();
        }

        // Save user details
        function saveUserDetails() {
            const formData = {
                id: currentUserId,
                firstName: document.getElementById('editFirstName').value,
                lastName: document.getElementById('editLastName').value,
                email: document.getElementById('editEmail').value,
                phone: document.getElementById('editPhone').value,
                role: document.getElementById('editRole').value,
                department: document.getElementById('editDepartment').value,
                location: document.getElementById('editLocation').value,
                active: document.getElementById('editActive').checked,
                password: document.getElementById('editPassword').value
            };

            // Validate required fields
            if (!formData.firstName || !formData.lastName || !formData.email) {
                alert('Please fill in all required fields.');
                return;
            }

            // Update mock data
            const userData = mockUserData[currentUserId];
            Object.assign(userData, formData);

            // Update display
            updateUserDisplay(userData);
            toggleEditMode();

            console.log('Saving user details:', formData);
            alert('User details saved successfully!');
        }

        // Preview uploaded image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('editAvatarPreview').src = event.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Reset password
        function resetPassword() {
            if (confirm('Are you sure you want to reset this user\\'s password? A new temporary password will be sent to their email.')) {
                console.log('Resetting password for user:', currentUserId);
                alert('Password reset email sent successfully!');
            }
        }

        // View snap details
        function viewSnapDetails(snapId) {
            window.location.href = `${snapDetailsBaseUrl}?id=${snapId}`;
        }

        // Get role display name
        function getRoleDisplayName(role) {
            const roleMap = {
                user: 'User',
                admin: 'Administrator',
                manager: 'Manager',
                moderator: 'Moderator'
            };
            return roleMap[role] || role;
        }

        // Initialize page
        window.addEventListener('load', loadUserDetails);
</script>
@endpush
