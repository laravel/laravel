<div>
    @if($currentPage === 'dashboard')
        @livewire('dashboard')
    @elseif($currentPage === 'timeEntries')
        @livewire('time-entries.list-and-edit-time-entries')
    @elseif($currentPage === 'projects')
        @livewire('projects.list-projects')
    @elseif($currentPage === 'customers')
        @livewire('customers.list-customers')
    @elseif($currentPage === 'profile')
        <h2 class="text-2xl font-bold">⚙️ Min Profil</h2>
        <p>Her kan du opdatere dine profiloplysninger...</p>
    @endif
</div>

