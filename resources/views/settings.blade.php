@extends('layouts.app')

@section('title', 'Settings - MIE Dashboard')
@section('page-title', 'Settings')

@section('content')
<!-- Account Settings Section -->
            <div class="card mb-24">
                <div class="card-header">
                    <h2>Account Settings</h2>
                </div>
                <div class="settings-form">
                    <div class="form-group">
                        <label for="companyName">Company Name</label>
                        <input type="text" id="companyName" class="form-control" value="Make it easy Ltd">
                    </div>
                    <div class="form-group">
                        <label for="adminEmail">Admin Email</label>
                        <input type="email" id="adminEmail" class="form-control" value="admin@makeiteasy.com">
                    </div>
                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select id="timezone" class="form-control">
                            <option value="UTC">UTC</option>
                            <option value="GMT" selected>GMT (London)</option>
                            <option value="EST">EST (New York)</option>
                            <option value="PST">PST (Los Angeles)</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>

            <!-- Security Settings Section -->
            <div class="card mb-24">
                <div class="card-header">
                    <h2>Security</h2>
                </div>
                <div class="settings-form">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" checked>
                            Enable Two-Factor Authentication
                        </label>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-primary">Update Password</button>
                    </div>
                </div>
            </div>

            <!-- Notification Settings Section -->
            <div class="card mb-24">
                <div class="card-header">
                    <h2>Notifications</h2>
                </div>
                <div class="settings-form">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" checked>
                            Email notifications for new user registrations
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" checked>
                            Email notifications for billing updates
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox">
                            Email notifications for system updates
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" checked>
                            Weekly usage reports
                        </label>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-primary">Save Preferences</button>
                    </div>
                </div>
            </div>

            <!-- API Settings Section -->
            <div class="card">
                <div class="card-header">
                    <h2>API Settings</h2>
                </div>
                <div class="settings-form">
                    <div class="form-group">
                        <label for="apiKey">API Key</label>
                        <div class="api-key-container">
                            <input type="text" id="apiKey" class="form-control" value="sk_live_51NcgGHK..." readonly>
                            <button class="btn btn-outline" onclick="copyApiKey()">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                                Copy
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="webhookUrl">Webhook URL</label>
                        <input type="url" id="webhookUrl" class="form-control" value="https://api.makeiteasy.com/webhooks/incoming">
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-outline" onclick="regenerateApiKey()">Regenerate API Key</button>
                        <button class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
@endsection

@push('scripts')
<script>
        function copyApiKey() {
            const apiKey = document.getElementById('apiKey');
            apiKey.select();
            document.execCommand('copy');
            // Show a success message
        }

        function regenerateApiKey() {
            // Implementation for regenerating API key
            if (confirm('Are you sure you want to regenerate your API key? This will invalidate your existing key.')) {
                // Regenerate API key
            }
        }
    </script>
@endpush
