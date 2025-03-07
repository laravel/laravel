<div>
    <h2 class="text-xl font-bold mb-2">Kunder</h2>
    <table class="table w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Navn</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Handlinger</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>
                        <button wire:click="edit({{ $customer->id }})" class="btn btn-sm btn-primary">Rediger</button>
                        <button wire:click="delete({{ $customer->id }})" class="btn btn-sm btn-error">Slet</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Ingen kunder fundet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($editing)
        <div class="modal modal-open">
            <div class="modal-box">
                <h2 class="text-lg font-bold mb-4">Rediger Kunde</h2>
                <input wire:model="edit_name" type="text" class="input input-bordered w-full mb-2">
                <input wire:model="edit_email" type="email" class="input input-bordered w-full mb-2">
                <input wire:model="edit_phone" type="text" class="input input-bordered w-full mb-2">
                
                <div class="flex justify-end mt-4">
                    <button wire:click="update" class="btn btn-primary">Gem ændringer</button>
                    <button wire:click="$set('editing', false)" class="btn btn-secondary ml-2">Annuller</button>
                </div>
            </div>
        </div>
    @endif
</div>

