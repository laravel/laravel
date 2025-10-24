@extends('layouts.app')

@section('title', 'Snaps - MIE Admin')
@section('page-title', 'Snaps')

@section('content')
<div class="card">
            <div class="card-header">
                <h2>All Snaps</h2>
                <div class="filter-section" style="margin-top: 24px; padding: 24px 32px; background: #f8f9fa; border-radius: 12px; border: 1px solid #e9ecef; max-width: none;">
                    <div style="display: grid; grid-template-columns: 3fr 2fr 2.5fr 140px; gap: 24px; align-items: end; width: 100%;">
                        <div>
                            <label for="searchResponse" style="display: block; font-weight: 500; color: #495057; margin-bottom: 6px; font-size: 14px;">Search in Responses</label>
                            <input type="text" id="searchResponse" placeholder="Search response text..." style="width: 100%; padding: 10px 14px; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                        </div>
                        <div>
                            <label for="filterUser" style="display: block; font-weight: 500; color: #495057; margin-bottom: 6px; font-size: 14px;">Filter by User</label>
                            <select id="filterUser" style="width: 100%; padding: 10px 14px; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; transition: border-color 0.2s ease;">
                                <option value="">All Users</option>
                                <option value="Jeanette Prosacco">Jeanette Prosacco</option>
                                <option value="John Smith">John Smith</option>
                                <option value="Alice Johnson">Alice Johnson</option>
                                <option value="Bob Lee">Bob Lee</option>
                                <option value="Carlos Ramirez">Carlos Ramirez</option>
                                <option value="Diana Prince">Diana Prince</option>
                                <option value="Ethan Clark">Ethan Clark</option>
                                <option value="Fatima Ali">Fatima Ali</option>
                                <option value="Grace Hopper">Grace Hopper</option>
                                <option value="Henry Zhao">Henry Zhao</option>
                                <option value="Isabella Rossi">Isabella Rossi</option>
                                <option value="Jack Nguyen">Jack Nguyen</option>
                                <option value="Karen Obasi">Karen Obasi</option>
                                <option value="Liam O'Connor">Liam O'Connor</option>
                                <option value="Maria Silva">Maria Silva</option>
                                <option value="Noah Cohen">Noah Cohen</option>
                                <option value="Olivia Martinez">Olivia Martinez</option>
                                <option value="Priya Patel">Priya Patel</option>
                                <option value="Quentin Dupont">Quentin Dupont</option>
                                <option value="Rosa Kim">Rosa Kim</option>
                            </select>
                        </div>
                        <div style="position: relative;">
                            <label for="dateRange" style="display: block; font-weight: 500; color: #495057; margin-bottom: 6px; font-size: 14px;">Date Range</label>
                            <input type="text" id="dateRange" placeholder="Select date range..." readonly style="width: 100%; padding: 10px 14px; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; cursor: pointer; background: white; transition: border-color 0.2s ease;">
                            <div id="dateRangeCalendar" style="position: absolute; top: 100%; left: 0; min-width: 340px; background: white; border: 1px solid #ced4da; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); z-index: 1000; display: none; padding: 20px; margin-top: 4px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                    <div>
                                        <label style="display: block; font-weight: 500; margin-bottom: 6px; font-size: 12px; color: #495057;">Start Date</label>
                                        <input type="date" id="startDate" style="width: 100%; padding: 8px 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 13px;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-weight: 500; margin-bottom: 6px; font-size: 12px; color: #495057;">End Date</label>
                                        <input type="date" id="endDate" style="width: 100%; padding: 8px 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 13px;">
                                    </div>
                                </div>
                                <div class="date-range-actions">
                                    <button class="btn btn-outline btn-sm" onclick="clearDateRange()">Clear</button>
                                    <button class="btn btn-primary btn-sm" onclick="applyDateRange()">Apply</button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-outline btn-block" onclick="clearFilters()">Clear All</button>
                        </div>
                    </div>
                    <div id="resultsCount" style="margin-top: 20px; font-size: 14px; color: #6c757d; font-weight: 500;"></div>
                </div>
                <style>
                    .filter-section {
                        position: relative;
                    }
                    .date-range-actions {
                        display: flex;
                        justify-content: flex-end;
                        gap: 10px;
                        border-top: 1px solid #e9ecef;
                        padding-top: 16px;
                    }
                    @media (max-width: 1200px) {
                        .filter-section > div:first-child {
                            grid-template-columns: 1fr 1fr !important;
                            grid-row-gap: 20px !important;
                        }
                        .filter-section > div:first-child > div:last-child {
                            grid-column: 1 / -1;
                            justify-self: center;
                        }
                        #dateRangeCalendar {
                            left: 0 !important;
                            right: 0 !important;
                            min-width: auto !important;
                        }
                    }
                    @media (max-width: 768px) {
                        .filter-section > div:first-child {
                            grid-template-columns: 1fr !important;
                        }
                        #dateRangeCalendar {
                            min-width: 280px !important;
                        }
                    }
                </style>
            </div>
            <div class="table-container" style="overflow-x:auto;">
                <table class="user-table min-w-full" aria-label="Snaps list">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Image</th>
                            <th>Function</th>
                            <th>How easy</th>
                            <th>Response</th>
                            <th><span class="sr-only">View details</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>10/06/2024</td>
                            <td>Jeanette Prosacco</td>
                            <td>
                                <img src="{{ asset('assets/images/Screenshot 2025-06-09 125116.jpg') }}" alt="Educational Q&A document snap" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Camera</td>
                            <td>Easiest</td>
                            <td>
                                This is a great look! It is a Girl ...
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 1">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>09/06/2024</td>
                            <td>John Smith</td>
                            <td>
                                <img src="https://i.pravatar.cc/80?u=2" alt="Snap preview for John Smith" class="h-12 w-12 object-cover rounded"/>
                            </td>
                            <td>File upload</td>
                            <td>Easy</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 2">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>08/06/2024</td>
                            <td>Alice Johnson</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap1/120/80" alt="Snap preview for Alice Johnson" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Camera</td>
                            <td>Neutral</td>
                            <td>
                                Quick capture of receipt. Looks clear and legible.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 3">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>08/06/2024</td>
                            <td>Bob Lee</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap2/120/80" alt="Snap preview for Bob Lee" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>File upload</td>
                            <td>Easy</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 4">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>07/06/2024</td>
                            <td>Carlos Ramirez</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap3/120/80" alt="Snap preview for Carlos Ramirez" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Screen capture</td>
                            <td>Hard</td>
                            <td>
                                Text is slightly skewed but still readable.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 5">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>07/06/2024</td>
                            <td>Diana Prince</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap4/120/80" alt="Snap preview for Diana Prince" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>URL import</td>
                            <td>Easiest</td>
                            <td>
                                Imported product page parsed with high confidence.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 6">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>06/06/2024</td>
                            <td>Ethan Clark</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap5/120/80" alt="Snap preview for Ethan Clark" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Camera</td>
                            <td>Easy</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 7">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>06/06/2024</td>
                            <td>Fatima Ali</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap6/120/80" alt="Snap preview for Fatima Ali" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>File upload</td>
                            <td>Neutral</td>
                            <td>
                                Multi-page PDF detected. First page shown.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 8">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>05/06/2024</td>
                            <td>Grace Hopper</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap7/120/80" alt="Snap preview for Grace Hopper" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Screen capture</td>
                            <td>Hardest</td>
                            <td>
                                Low contrast text. OCR adjusted brightness.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 9">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>05/06/2024</td>
                            <td>Henry Zhao</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap8/120/80" alt="Snap preview for Henry Zhao" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>URL import</td>
                            <td>Easy</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 10">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>04/06/2024</td>
                            <td>Isabella Rossi</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap9/120/80" alt="Snap preview for Isabella Rossi" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Camera</td>
                            <td>Easiest</td>
                            <td>
                                Crisp image with accurate detection.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 11">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>04/06/2024</td>
                            <td>Jack Nguyen</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap10/120/80" alt="Snap preview for Jack Nguyen" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>File upload</td>
                            <td>Neutral</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 12">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>03/06/2024</td>
                            <td>Karen Obasi</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap11/120/80" alt="Snap preview for Karen Obasi" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Screen capture</td>
                            <td>Hard</td>
                            <td>
                                Edge content detected; cropping suggested.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 13">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>03/06/2024</td>
                            <td>Liam O'Connor</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap12/120/80" alt="Snap preview for Liam O'Connor" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>URL import</td>
                            <td>Easy</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 14">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>02/06/2024</td>
                            <td>Maria Silva</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap13/120/80" alt="Snap preview for Maria Silva" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Camera</td>
                            <td>Neutral</td>
                            <td>
                                Good lighting; minimal glare observed.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 15">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>02/06/2024</td>
                            <td>Noah Cohen</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap14/120/80" alt="Snap preview for Noah Cohen" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>File upload</td>
                            <td>Easiest</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 16">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>01/06/2024</td>
                            <td>Olivia Martinez</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap15/120/80" alt="Snap preview for Olivia Martinez" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Screen capture</td>
                            <td>Hardest</td>
                            <td>
                                Dense layout; extracted key fields correctly.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 17">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>01/06/2024</td>
                            <td>Priya Patel</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap16/120/80" alt="Snap preview for Priya Patel" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>URL import</td>
                            <td>Easy</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 18">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-blue-50 transition">
                            <td>31/05/2024</td>
                            <td>Quentin Dupont</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap17/120/80" alt="Snap preview for Quentin Dupont" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>Camera</td>
                            <td>Neutral</td>
                            <td>
                                Document rotated; auto-correct applied.
                                <a href="{{ route('snaps') }}" class="response-link" style="color: #124191; text-decoration: underline; cursor: pointer;">Read more</a>
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 19">View details</a>
                            </td>
                        </tr>
                        <tr class="bg-gray-50 hover:bg-blue-50 transition">
                            <td>31/05/2024</td>
                            <td>Rosa Kim</td>
                            <td>
                                <img src="https://picsum.photos/seed/snap18/120/80" alt="Snap preview for Rosa Kim" style="width: 100px; height: auto; object-fit: cover; border-radius: 6px;"/>
                            </td>
                            <td>File upload</td>
                            <td>Easiest</td>
                            <td>
                                &mdash;
                            </td>
                            <td>
                                <a href="{{ route('snaps') }}" class="btn btn-primary btn-sm" aria-label="View details for snap 20">View details</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
@endsection

@push('scripts')
<script>
// Filter and search functionality
function parseDate(dateString) {
    // Parse date in DD/MM/YYYY format
    const parts = dateString.split('/');
    if (parts.length === 3) {
        return new Date(parts[2], parts[1] - 1, parts[0]); // Year, Month (0-indexed), Day
    }
    return null;
}

function formatDateForInput(dateString) {
    // Convert DD/MM/YYYY to YYYY-MM-DD for date input
    const parts = dateString.split('/');
    if (parts.length === 3) {
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
    return '';
}

function filterTable() {
    const searchResponse = document.getElementById('searchResponse').value.toLowerCase();
    const filterUser = document.getElementById('filterUser').value;
    const dateFrom = document.getElementById('startDate').value;
    const dateTo = document.getElementById('endDate').value;
    
    const table = document.querySelector('.user-table tbody');
    const rows = table.querySelectorAll('tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 0) return; // Skip if no cells
        
        const dateCell = cells[0].textContent.trim();
        const userCell = cells[1].textContent.trim();
        const responseCell = cells[5].textContent.trim().toLowerCase();
        
        let showRow = true;
        
        // Filter by response text
        if (searchResponse && !responseCell.includes(searchResponse)) {
            showRow = false;
        }
        
        // Filter by user
        if (filterUser && userCell !== filterUser) {
            showRow = false;
        }
        
        // Filter by date range
        if (dateFrom || dateTo) {
            const rowDate = parseDate(dateCell);
            if (rowDate) {
                if (dateFrom) {
                    const fromDate = new Date(dateFrom);
                    if (rowDate < fromDate) {
                        showRow = false;
                    }
                }
                if (dateTo) {
                    const toDate = new Date(dateTo);
                    toDate.setHours(23, 59, 59, 999); // Include the entire end date
                    if (rowDate > toDate) {
                        showRow = false;
                    }
                }
            }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    // Update results count
    const resultsCount = document.getElementById('resultsCount');
    const totalRows = rows.length;
    resultsCount.textContent = `Showing ${visibleCount} of ${totalRows} snaps`;
}

function clearFilters() {
    document.getElementById('searchResponse').value = '';
    document.getElementById('filterUser').value = '';
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('dateRange').value = '';
    closeDateRangePicker();
    filterTable();
}

function clearDateRange() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('dateRange').value = '';
    filterTable();
}

function applyDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    let rangeText = '';
    if (startDate && endDate) {
        rangeText = `${formatDateDisplay(startDate)} - ${formatDateDisplay(endDate)}`;
    } else if (startDate) {
        rangeText = `From ${formatDateDisplay(startDate)}`;
    } else if (endDate) {
        rangeText = `Until ${formatDateDisplay(endDate)}`;
    }
    
    document.getElementById('dateRange').value = rangeText;
    closeDateRangePicker();
    filterTable();
}

function formatDateDisplay(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB'); // DD/MM/YYYY format
}

function openDateRangePicker() {
    document.getElementById('dateRangeCalendar').style.display = 'block';
}

function closeDateRangePicker() {
    document.getElementById('dateRangeCalendar').style.display = 'none';
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchResponse').addEventListener('input', filterTable);
    document.getElementById('filterUser').addEventListener('change', filterTable);
    
    // Date range picker events
    document.getElementById('dateRange').addEventListener('click', openDateRangePicker);
    document.getElementById('startDate').addEventListener('change', function() {
        // Auto-apply when dates change
        applyDateRange();
    });
    document.getElementById('endDate').addEventListener('change', function() {
        // Auto-apply when dates change
        applyDateRange();
    });
    
    // Close calendar when clicking outside
    document.addEventListener('click', function(event) {
        const dateRangePicker = document.getElementById('dateRangeCalendar');
        const dateRangeInput = document.getElementById('dateRange');
        
        if (!dateRangePicker.contains(event.target) && event.target !== dateRangeInput) {
            closeDateRangePicker();
        }
    });
    
    // Initial count
    filterTable();
});
</script>
@endpush

