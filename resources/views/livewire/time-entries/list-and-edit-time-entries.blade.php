<div>
    <h2 class="text-xl font-bold mb-4">Dine tidsregistreringer</h2>

    @if(session()->has('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="table w-full">
<thead>
    <tr>
        <th>Dato</th>
        <th>Medarbejder</th>
        <th>Kunde</th>
        <th>Projekt</th>
        <th>Timer</th>
        <th>Kommentar</th>
        <th>Handlinger</th>
    </tr>
</thead>
<tbody>
    @forelse($timeEntries as $entry)
        <tr>
            <td>{{ $entry->entry_date }}</td>
            <td>{{ $entry->user->name }}</td> <!-- Medarbejderens navn -->
            <td>{{ $entry->project->customer->name }}</td> <!-- Kundens navn -->
            <td>{{ $entry->project->name }}</td> <!-- Projektets navn -->
            <td>{{ $entry->hours }}</td>
            <td>{{ $entry->comment }}</td>
            <td>
                <button wire:click="edit({{ $entry->id }})" class="btn btn-sm btn-primary">Rediger</button>
                <button wire:click="delete({{ $entry->id }})" class="btn btn-sm btn-error">Slet</button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center">Ingen tidsregistreringer fundet.</td>
        </tr>
    @endforelse
</tbody>

           </table>

    {{ $timeEntries->links() }}

    @if ($editing)
        <div class="modal modal-open">
            <div class="modal-box">
                <h2 class="text-lg font-bold mb-4">Rediger tidsregistrering</h2>

                <select wire:model="edit_project_id" class="select select-bordered w-full">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                <input wire:model="edit_entry_date" type="date" class="input input-bordered w-full mt-2">
                <input wire:model="edit_hours" type="number" step="0.25" class="input input-bordered w-full mt-2">
                <textarea wire:model="edit_comment" class="textarea textarea-bordered w-full mt-2"></textarea>

                <div class="flex justify-end mt-4">
                    <button wire:click="update" class="btn btn-primary">Gem ændringer</button>
                    <button wire:click="$set('editing', false)" class="btn btn-secondary ml-2">Annuller</button>
                </div>
            </div>
        </div>
    @endif
</div>

