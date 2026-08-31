<?php

require_once __DIR__.'/vendor/autoload.php';

use function Termwind\render;

render(<<<'HTML'
    <div class="ml-2">
        <pre>
  ─                   ─        ─    ─
│ │                 │ │      │ │  │ │
│ │     ──,         │ │  ──, │ │  │ │  ─
│/ \   /  │  │   │  │/  /  │ │/ \─│/  │/
│   │─/\─/│─/ \─/│─/│──/\─/│─/\─/ │──/│──/
        </pre>
        <div class="px-1 bg-green-300 text-black">by ⚙️ Configured</div>
        <div class="px-1 mt-1 bg-blue-300 text-black">{{ $version }}</div>
        <em class="ml-1">
            Create portable PHP CLI applications w/ PHP Micro
        </em>
    </div>
HTML);
