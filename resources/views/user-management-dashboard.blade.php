@extends('layouts.app')

@section('title', 'User Management Dashboard')
@section('page-title', 'User Management')

@section('content')
<div class="card">
        <div class="card-header">
            <div class="search-bar">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search users...">
            </div>
            <div class="actions">
                <div class="btn-group">
                    <button class="btn btn-outline" onclick="showUploadModal()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import CSV
                    </button>
                    <button class="btn btn-outline" onclick="exportCSV()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export CSV
                    </button>
                </div>
                <button class="btn btn-primary" onclick="window.location.href='{{ route('users') }}'">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add User
                </button>
            </div>
        </div>
        <div class="filters">
            <button class="btn btn-outline">Organizational unit</button>
            <button class="btn btn-outline">User source</button>
            <button class="btn btn-outline">Status</button>
            <button class="btn btn-outline">Role</button>
        </div>
        <div class="table-container">
          <table class="user-table">
            <thead>
              <tr>
                <th><input type="checkbox"/></th>
                <th>Name</th>
                <th>Status</th>
                <th>Role</th>
                <th>Total Snaps</th>
                <th>Snaps Used</th>
                <th>Plan Renewal</th>
                <th>Updated</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="checkbox"/></td>
                <td>
                    <div class="user-info">
                        <img src="https://i.pravatar.cc/40?u=1" alt="" class="avatar">
                        <div class="user-details">
                            <a href="{{ route('user-details') }}?id=1" class="name">Jeanette Prosacco</a>
                            <div class="email">Arnoldo.Bayer51@yahoo.com</div>
                        </div>
                    </div>
                </td>
                <td><span class="status-badge active">Active</span></td>
                <td>Admin</td>
                <td>
                    <div class="snaps-info">
                        <span class="snaps-count">1000</span>
                        <div class="snaps-progress" title="70% used">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="snaps-used">700</span>
                </td>
                <td>
                    <div class="renewal-info">
                        <span class="renewal-date">Dec 31, 2024</span>
                        <span class="renewal-status upcoming">30 days left</span>
                    </div>
                </td>
                <td>21/09/2022</td>
                <td class="action-menu">
                    <button class="btn btn-icon" aria-label="User actions">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                    </button>
                </td>
              </tr>
              <tr>
                <td><input type="checkbox"/></td>
                <td>
                    <div class="user-info">
                        <img src="https://i.pravatar.cc/40?u=2" alt="" class="avatar">
                        <div class="user-details">
                            <a href="{{ route('user-details') }}?id=2" class="name">John Smith</a>
                            <div class="email">john.smith@example.com</div>
                        </div>
                    </div>
                </td>
                <td><span class="status-badge invited">Invited</span></td>
                <td>User</td>
                <td>
                    <div class="snaps-info">
                        <span class="snaps-count">500</span>
                        <div class="snaps-progress" title="20% used">
                            <div class="progress-bar" style="width: 20%"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="snaps-used">100</span>
                </td>
                <td>
                    <div class="renewal-info">
                        <span class="renewal-date">Jan 15, 2025</span>
                        <span class="renewal-status active">45 days left</span>
                    </div>
                </td>
                <td>22/09/2022</td>
                <td class="action-menu">
                    <button class="btn btn-icon" aria-label="User actions">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                    </button>
                </td>
              </tr>
              <tr>
                <td><input type="checkbox"/></td>
                <td>
                    <div class="user-info">
                        <img src="https://i.pravatar.cc/40?u=3" alt="" class="avatar">
                        <div class="user-details">
                            <a href="{{ route('user-details') }}?id=3" class="name">Sarah Johnson</a>
                            <div class="email">sarah.j@example.com</div>
                        </div>
                    </div>
                </td>
                <td><span class="status-badge inactive">Inactive</span></td>
                <td>User</td>
                <td>
                    <div class="snaps-info inactive">
                        <span class="snaps-count">-</span>
                    </div>
                </td>
                <td>
                    <span class="snaps-dash">-</span>
                </td>
                <td>
                    <div class="renewal-info">
                        <span class="renewal-date">Nov 30, 2024</span>
                        <span class="renewal-status expiring">5 days left</span>
                    </div>
                </td>
                <td>20/09/2022</td>
                <td>
                    <button class="btn btn-outline">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
@endsection

@push('scripts')
<!-- CSV Upload Modal -->
  <div class="upload-modal" id="uploadModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import CSV File</h3>
            <div class="modal-subtitle">
                <a href="#" onclick="downloadTemplate()" class="template-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>
        </div>
        <div class="upload-area">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <p>Drag and drop your CSV file here, or click to browse</p>
            <p class="upload-hint">Accepted format: .csv</p>
            <input type="file" accept=".csv" style="display: none" id="csvInput">
        </div>
        <div class="validation-message" id="validationMessage"></div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="hideUploadModal()">Cancel</button>
            <button class="btn btn-primary" onclick="uploadCSV()" id="uploadButton" disabled>Import</button>
        </div>
    </div>
  </div>

  <script>
    function showUploadModal() {
        document.getElementById('uploadModal').style.display = 'flex';
        resetUploadForm();
    }

    function hideUploadModal() {
        document.getElementById('uploadModal').style.display = 'none';
        resetUploadForm();
    }

    function resetUploadForm() {
        document.getElementById('csvInput').value = '';
        document.getElementById('validationMessage').className = 'validation-message';
        document.getElementById('validationMessage').textContent = '';
        document.getElementById('uploadButton').disabled = true;
        document.querySelector('.upload-area').style.borderColor = '#e5e7eb';
    }

    // Close modal when clicking outside
    document.getElementById('uploadModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideUploadModal();
        }
    });

    // Handle file upload area
    const uploadArea = document.querySelector('.upload-area');
    const fileInput = document.getElementById('csvInput');
    const validationMessage = document.getElementById('validationMessage');
    const uploadButton = document.getElementById('uploadButton');

    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#018b8d';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#e5e7eb';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#e5e7eb';
        const files = e.dataTransfer.files;
        handleFileSelection(files);
    });

    fileInput.addEventListener('change', (e) => {
        handleFileSelection(e.target.files);
    });

    function handleFileSelection(files) {
        if (files.length && files[0].type === 'text/csv') {
            fileInput.files = files;
            validationMessage.className = 'validation-message success';
            validationMessage.textContent = 'File selected: ' + files[0].name;
            uploadButton.disabled = false;
        } else {
            validationMessage.className = 'validation-message error';
            validationMessage.textContent = 'Please select a valid CSV file.';
            uploadButton.disabled = true;
        }
    }

    function uploadCSV() {
        // Here you would implement the actual CSV upload logic
        const file = fileInput.files[0];
        if (file) {
            // Simulate upload
            validationMessage.className = 'validation-message success';
            validationMessage.textContent = 'File uploaded successfully!';
            setTimeout(hideUploadModal, 1500);
        }
    }

    function downloadTemplate() {
        // Create CSV template content with updated headers
        const template = 'First Name,Last Name,Email,Mobile Number,Role,Total Snaps,Snaps Used,Plan Renewal\n';
        
        // Create blob and download
        const blob = new Blob([template], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'user-import-template.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }

    function exportCSV() {
        // Sample data - replace with actual user data
        const data = [
            ['First Name', 'Last Name', 'Email', 'Mobile Number', 'Role', 'Total Snaps', 'Snaps Used', 'Plan Renewal'],
            ['Jeanette', 'Prosacco', 'Arnoldo.Bayer51@yahoo.com', '0412345678', 'Admin', '1000', '700', '2024-12-31']
            // Add more rows as needed
        ];

        // Convert to CSV
        const csv = data.map(row => row.join(',')).join('\n');
        
        // Create blob and download
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'users-export.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }
  </script>
@endpush

