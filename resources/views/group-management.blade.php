@extends('layouts.app')

@section('title', 'Group Management - MIE Dashboard')
@section('page-title', 'Group Management')

@section('content')
<div class="card">
                <div class="card-header">
                    <div class="search-bar">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Search groups...">
                    </div>
                    <div class="actions">
                        <button class="btn btn-primary" onclick="showCreateGroupModal()">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Group
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="group-table">
                        <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Members</th>
                                <th>Total Snaps</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="group-info">
                                        <div class="group-icon">DS</div>
                                        <div class="group-details">
                                            <div class="name">Data Science Team</div>
                                            <div class="description">Data analysis and ML models</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="members-preview">
                                        <div class="member-avatars">
                                            <img src="https://i.pravatar.cc/32?u=1" alt="Member 1">
                                            <img src="https://i.pravatar.cc/32?u=2" alt="Member 2">
                                            <img src="https://i.pravatar.cc/32?u=3" alt="Member 3">
                                            <span class="more-members">+4</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="snaps-info">
                                        <span class="snaps-count">5,000</span>
                                        <div class="snaps-progress" title="60% used">
                                            <div class="progress-bar" style="width: 60%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>21/09/2023</td>
                                <td><span class="status-badge active">Active</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-outline btn-sm" onclick="editGroup(1)">Edit</button>
                                        <button class="btn btn-outline btn-sm" onclick="manageMembers(1)">Members</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="group-info">
                                        <div class="group-icon">MK</div>
                                        <div class="group-details">
                                            <div class="name">Marketing Team</div>
                                            <div class="description">Content and social media</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="members-preview">
                                        <div class="member-avatars">
                                            <img src="https://i.pravatar.cc/32?u=4" alt="Member 4">
                                            <img src="https://i.pravatar.cc/32?u=5" alt="Member 5">
                                            <span class="more-members">+2</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="snaps-info">
                                        <span class="snaps-count">3,000</span>
                                        <div class="snaps-progress" title="40% used">
                                            <div class="progress-bar" style="width: 40%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>15/08/2023</td>
                                <td><span class="status-badge active">Active</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-outline btn-sm" onclick="editGroup(2)">Edit</button>
                                        <button class="btn btn-outline btn-sm" onclick="manageMembers(2)">Members</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
@endsection

@push('scripts')
<!-- Create Group Modal -->
    <div class="modal" id="createGroupModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Group</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="groupName">Group Name</label>
                    <input type="text" id="groupName" class="form-control" placeholder="Enter group name">
                </div>
                <div class="form-group">
                    <label for="groupDescription">Description</label>
                    <textarea id="groupDescription" class="form-control" placeholder="Enter group description"></textarea>
                </div>
                <div class="form-group">
                    <label for="groupSnaps">Snap Allocation</label>
                    <input type="number" id="groupSnaps" class="form-control" placeholder="Enter snap limit">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="hideCreateGroupModal()">Cancel</button>
                <button class="btn btn-primary" onclick="createGroup()">Create Group</button>
            </div>
        </div>
    </div>

    <script>
        function showCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'flex';
        }

        function hideCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'none';
        }

        function createGroup() {
            // Implementation for creating a group
            hideCreateGroupModal();
        }

        function editGroup(groupId) {
            // Implementation for editing a group
        }

        function manageMembers(groupId) {
            // Implementation for managing group members
        }

        // Close modal when clicking outside
        document.getElementById('createGroupModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCreateGroupModal();
            }
        });
    </script>
@endpush
