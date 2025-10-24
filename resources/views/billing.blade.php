@extends('layouts.app')

@section('title', 'Billing - MIE Dashboard')
@section('page-title', 'Billing')

@section('content')
<!-- Current Plan Section -->
            <div class="card mb-24">
                <div class="card-header">
                    <h2>Current Plan</h2>
                    <button class="btn btn-primary" onclick="showUpgradePlanModal()">Upgrade Plan</button>
                </div>
                <div class="plan-details">
                    <div class="plan-info">
                        <div class="plan-name">Enterprise Plan</div>
                        <div class="plan-price">$499/month</div>
                        <div class="plan-period">Next billing date: December 31, 2024</div>
                    </div>
                    <div class="plan-features">
                        <div class="feature-item">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>50,000 Snaps per month</span>
                        </div>
                        <div class="feature-item">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Unlimited users</span>
                        </div>
                        <div class="feature-item">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Priority support</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method Section -->
            <div class="card mb-24">
                <div class="card-header">
                    <h2>Payment Method</h2>
                    <button class="btn btn-outline" onclick="showAddPaymentModal()">Add Payment Method</button>
                </div>
                <div class="payment-methods">
                    <div class="payment-method active">
                        <div class="payment-method-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                                <path d="M2 10h20" stroke-width="2"/>
                            </svg>
                        </div>
                        <div class="payment-method-details">
                            <div class="card-name">Visa ending in 4242</div>
                            <div class="card-expiry">Expires 12/25</div>
                        </div>
                        <div class="payment-method-actions">
                            <button class="btn btn-outline btn-sm">Edit</button>
                            <button class="btn btn-outline btn-sm">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing History Section -->
            <div class="card">
                <div class="card-header">
                    <h2>Billing History</h2>
                    <button class="btn btn-outline" onclick="downloadInvoices()">Download All</button>
                </div>
                <div class="table-container">
                    <table class="billing-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nov 30, 2023</td>
                                <td>Enterprise Plan - December 2023</td>
                                <td>$499.00</td>
                                <td><span class="status-badge active">Paid</span></td>
                                <td>
                                    <button class="btn btn-outline btn-sm">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Oct 31, 2023</td>
                                <td>Enterprise Plan - November 2023</td>
                                <td>$499.00</td>
                                <td><span class="status-badge active">Paid</span></td>
                                <td>
                                    <button class="btn btn-outline btn-sm">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
@endsection

@push('scripts')
<script>
        function showUpgradePlanModal() {
            // Implementation for showing upgrade plan modal
        }

        function showAddPaymentModal() {
            // Implementation for showing add payment method modal
        }

        function downloadInvoices() {
            // Implementation for downloading all invoices
        }
    </script>
@endpush
