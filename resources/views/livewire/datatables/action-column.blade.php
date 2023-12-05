<div>
    @isset ($editLink)
        <a wire:navigate href="{{ $editLink }}"><i class="fa-solid fa-pen-to-square me-2"></i></a>
    @endif

    @isset ($viewLink)
        <a wire:navigate href="{{ $viewLink }}"><i class="fa-solid fa-eye me-2"></i></a>
    @endif

    @isset ($deleteLink)
        <a wire:confirm="Are you sure to delete?" wire:click="destroy('{{ $row['id'] }}')"><i class="fa-solid fa-trash me-2"></i></a>
    @endif
</div>
