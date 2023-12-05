@aware(['isTailwind', 'isBootstrap', 'tableName', 'component'])
@props(['colCount' => 1])

@php
$customAttributes['loader-wrapper'] = $component->getLoadingPlaceHolderWrapperAttributes();
$customAttributes['loader-icon'] = $component->getLoadingPlaceHolderIconAttributes();
@endphp
@if($this->hasLoadingPlaceholderBlade())
    @include($this->getLoadingPlaceHolderBlade(), ['colCount' => $colCount])
@else

    <tr wire:key="{{ $tableName }}-loader" class="hidden d-none"
    {{
        $attributes->merge($customAttributes['loader-wrapper'])
            ->class(['w-full text-center h-screen place-items-center align-middle' => $isTailwind && ($customAttributes['loader-wrapper']['default'] ?? true)])
            ->class(['w-100 text-center h-100 align-items-center' => $isBootstrap && ($customAttributes['loader-wrapper']['default'] ?? true)]);
    }}
    wire:loading.class.remove="hidden d-none"
    >
        <td colspan="{{ $colCount }}">
            <div class="h-min self-center align-middle text-center">
                <div class="lds-hourglass"
                {{
                        $attributes->merge($customAttributes['loader-icon'])
                            ->class(['lds-hourglass' => $isTailwind && ($customAttributes['loader-icon']['default'] ?? true)])
                            ->class(['lds-hourglass' => $isBootstrap && ($customAttributes['loader-icon']['default'] ?? true)])
                            ->except('default');
                }}
                ></div>
                <div>{{ $component->getLoadingPlaceholderContent() }}</div>
            </div>
        </td>
    </tr>

@endif
