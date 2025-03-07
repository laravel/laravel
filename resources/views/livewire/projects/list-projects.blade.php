<div>
    <h2 class="text-xl font-bold mb-2">Projekter</h2>
    <table class="table w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Navn</th>
                <th>Kunde</th>
                <th>Timer</th>
                <th>Status</th>
                <th>Handlinger</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
                <tr>
                    <td>{{ $project->id }}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->customer->name }}</td>
                    <td>{{ $project->budget_hours ?? '-' }}</td>
                    <td>
                        <button wire:click="toggleActive({{ $project->id }})" 
                                class="btn btn-sm {{ $project->active ? 'btn-success' : 'btn-warning' }}">
                            {{ $project->active ? 'Aktiv' : 'Inaktiv' }}
                        </button>
                    </td>
                    <td>
                        <button wire:click="edit({{ $project->id }})" class="btn btn-sm btn-primary">Rediger</button>
                        <button wire:click="delete({{ $project->id }})" class="btn btn-sm btn-error">Slet</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Ingen projekter fundet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($editing)
        <div class="modal modal-open">
            <div class="modal-box">
                <h2 class="text-lg font-bold mb-4">Rediger Projekt</h2>
                <select wire:model="edit_customer_id" class="select select-bordered w-full">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                <input wire:model="edit_name" type="text" class="input input-bordered w-full mt-2">
                <textarea wire:model="edit_description" class="textarea textarea-bordered w-full mt-2"></textarea>
                <input wire:model="edit_budget_hours" type="number" step="0.1" class="input input-bordered w-full mt-2">

<label class="flex items-center mt-2">
    <input type="checkbox" wire:model.lazy="edit_active" class="toggle">
    <span class="ml-2">{{ $edit_active ? 'Aktivt projekt' : 'Aktivt projekt' }}</span>
</label>
               
                <div class="flex justify-end mt-4">
                    <button wire:click="update" class="btn btn-primary">Gem ændringer</button>
                    <button wire:click="$set('editing', false)" class="btn btn-secondary ml-2">Annuller</button>
                </div>
            </div>
        </div>
    @endif
</div>

