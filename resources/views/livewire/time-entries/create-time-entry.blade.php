<div class="card bg-white shadow-xl p-6">
    <h2 class="text-xl font-bold mb-4">Ny tidsregistrering</h2>

    @if(session()->has('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="grid grid-cols-1 gap-4">
        <select wire:model="project_id" class="select select-bordered w-full">
            <option value="">Vælg projekt</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
        @error('project_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <input wire:model="entry_date" type="date" class="input input-bordered w-full">
        @error('entry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="grid grid-cols-2 gap-4">
            <input wire:model="start_time" type="time" class="input input-bordered w-full" placeholder="Starttid">
            <input wire:model="end_time" type="time" class="input input-bordered w-full" placeholder="Sluttid">
        </div>
        @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <input wire:model="hours" type="number" step="0.25" class="input input-bordered w-full" placeholder="Antal timer">
        @error('hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <textarea wire:model="comment" class="textarea textarea-bordered w-full" placeholder="Kommentar (valgfri)"></textarea>
        @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <button type="submit" class="btn btn-primary w-full mt-2">Gem</button>
    </form>
</div>

