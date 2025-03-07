<div class="card shadow-xl p-6">
    <h2 class="text-xl font-semibold mb-4">Opret nyt projekt</h2>

    @if(session()->has('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <select wire:model="customer_id" class="select select-bordered w-full">
            <option value="">Vælg kunde</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>
        @error('customer_id') <span class="text-red-500">{{ $message }}</span> @enderror

        <input wire:model="name" type="text" placeholder="Projektnavn" class="input input-bordered w-full mt-2">
        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror

        <textarea wire:model="description" class="textarea textarea-bordered w-full mt-2" placeholder="Beskrivelse"></textarea>
        @error('description') <span class="text-red-500">{{ $message }}</span> @enderror

        <input wire:model="budget_hours" type="number" step="0.1" placeholder="Budgeterede timer" class="input input-bordered w-full mt-2">
        @error('budget_hours') <span class="text-red-500">{{ $message }}</span> @enderror

        <button type="submit" class="btn btn-primary w-full mt-4">Opret projekt</button>
    </form>
</div>

