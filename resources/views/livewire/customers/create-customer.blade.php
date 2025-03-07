<div class="card shadow-xl p-6">
    <h2 class="text-xl font-semibold mb-4">Opret ny kunde</h2>

    <form wire:submit.prevent="submit">
        <input wire:model="name" type="text" placeholder="Kundenavn" class="input input-bordered w-full mb-2">
        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror

        <input wire:model="email" type="email" placeholder="Email (valgfri)" class="input input-bordered w-full mt-2">
        @error('email') <span class="text-red-500">{{ $message }}</span> @enderror

        <input wire:model="phone" type="text" placeholder="Telefon (valgfri)" class="input input-bordered w-full mt-2">
        @error('phone') <span class="text-red-500">{{ $message }}</span> @enderror

        <button wire:click="submit" class="btn btn-primary mt-4">Gem Kunde</button>
</div>

