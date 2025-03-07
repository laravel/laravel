<div>
    <h2 class="text-xl font-bold mb-2">Dine tidsregistreringer</h2>
    <table class="table w-full">
        <thead>
            <tr>
                <th>Projekt</th>
                <th>Timer</th>
                <th>Kommentar</th>
                <th>Handlinger</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($timeEntries as $entry)
                <tr>
                    <td>{{ $entry->project->name }}</td>
                    <td>{{ $entry->hours }}</td>
                    <td>{{ $entry->comment }}</td>
                    <td>
                        <button wire:click="edit({{ $entry->id }})" class="btn btn-sm btn-primary">Rediger</button>
                        <button wire:click="delete({{ $entry->id }})" class="btn btn-sm btn-error">Slet</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

