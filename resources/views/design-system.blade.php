@extends('layouts.app')

@section('title', 'Design System - Make it easy')
@section('page-title', 'Design System')

@push('styles')
<style>
    .ds-container { max-width: 1100px; margin: 24px auto; padding: 0 24px; font-family: 'Mulish', 'Helvetica Neue', Arial, sans-serif; }
    .ds-header { display: flex; align-items: center; gap: 16px; margin-bottom: 12px; }
    .ds-header img { height: 40px; width: auto; }
    .ds-section { margin: 32px 0; }
    .ds-section h2 { margin: 0 0 12px 0; color: var(--intro-blue); }
    .color-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
    .swatch { border: 1px solid var(--border-grey); border-radius: 10px; background: var(--white); padding: 12px; }
    .swatch .box { height: 70px; border-radius: 8px; margin-bottom: 10px; }
    .swatch .meta { font-size: 13px; color: var(--charcoal); }
    .type-scale h1, .type-scale h2, .type-scale h3, .type-scale h4, .type-scale h5, .type-scale h6 { margin: 8px 0; font-family: 'Mulish', 'Helvetica Neue', Arial, sans-serif; }
    .type-scale h1 { font-weight: 700; }
    .type-scale h2 { font-weight: 600; }
    .type-scale h3 { font-weight: 600; }
    .type-scale h4 { font-weight: 500; }
    .type-scale h5 { font-weight: 500; }
    .type-scale h6 { font-weight: 400; letter-spacing: 0.02em; }
    .btn-row { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .btn-block-demo { width: 160px; display: flex; }
    .btn-row .btn-icon svg { width: 18px; height: 18px; }
    .users-btns { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }
    .users-action-btn { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border-grey); background: var(--white); cursor: pointer; font-size: 15px; transition: all 0.2s ease; }
    .users-action-btn:hover { background: rgba(1, 139, 141, 0.08); transform: translateY(-1px); }
    .users-action-btn .icon { font-size: 18px; }
    .ds-note { color: #6b7280; font-size: 13px; }
</style>
@endpush

@section('content')
<div class="ds-container">
    <div class="ds-header">
        <img src="{{ asset('assets/images/MIELogo.jpg') }}" alt="MIE Logo" />
        <h1 style="margin:0">Design System</h1>
    </div>

    <div class="ds-section">
        <h2>Colors</h2>
        <div class="color-grid"></div>
        <p class="ds-note">Values are pulled from the CSS custom properties used across the product.</p>
    </div>

    <div class="ds-section type-scale">
        <h2>Typography (Mulish)</h2>
        <h1>Heading 1 - 700</h1>
        <h2>Heading 2 - 600</h2>
        <h3>Heading 3 - 600</h3>
        <h4>Heading 4 - 500</h4>
        <h5>Heading 5 - 500</h5>
        <h6>Heading 6 - 400</h6>
    </div>

    <div class="ds-section">
        <h2>Buttons</h2>
        <div class="btn-row" style="margin-bottom:12px;">
            <button class="btn btn-primary" type="button">Primary</button>
            <button class="btn btn-secondary" type="button">Secondary</button>
            <button class="btn btn-outline" type="button">Outline</button>
            <button class="btn btn-success" type="button">Success</button>
        </div>
        <div class="btn-row" style="margin-bottom:12px;">
            <button class="btn btn-primary btn-sm" type="button">Primary Small</button>
            <button class="btn btn-outline btn-sm" type="button">Outline Small</button>
            <div class="btn-block-demo">
                <button class="btn btn-outline btn-block" type="button">Block Button</button>
            </div>
            <button class="btn btn-icon" type="button" aria-label="More actions">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                </svg>
            </button>
        </div>

        <h3 style="margin-top:16px;">User Action Buttons</h3>
        <div class="users-btns">
            <button class="users-action-btn btn-camera"><span class="icon">&#128247;</span><span>Camera</span></button>
            <button class="users-action-btn btn-photos"><span class="icon">&#128444;</span><span>My photos</span></button>
            <button class="users-action-btn btn-upload"><span class="icon">&#128228;</span><span>Upload a file</span></button>
            <button class="users-action-btn btn-link"><span class="icon">&#128279;</span><span>Link</span></button>
            <button class="users-action-btn btn-question"><span class="icon">&#10067;</span><span>Ask a question</span></button>
            <button class="users-action-btn btn-paste"><span class="icon">&#128203;</span><span>Paste words</span></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
      const palette = [
        { label: 'Bright Green', var: '--bright-green' },
        { label: 'Orange', var: '--orange' },
        { label: 'Magenta', var: '--magenta' },
        { label: 'Red', var: '--red' },
        { label: 'Dark Blue', var: '--dark-blue' },
        { label: 'Green/Blue', var: '--green-blue' },
        { label: 'Purple', var: '--purple' },
        { label: 'Charcoal', var: '--charcoal' },
        { label: 'Intro Blue', var: '--intro-blue' },
        { label: 'Button Red', var: '--button-red' }
      ];

      const grid = document.querySelector('.color-grid');
      const rootStyles = getComputedStyle(document.documentElement);

      palette.forEach(function (entry) {
        const hex = rootStyles.getPropertyValue(entry.var).trim();
        const swatch = document.createElement('div');
        swatch.className = 'swatch';
        swatch.innerHTML = `
          <div class="box" style="background:${hex}"></div>
          <div class="meta"><strong>${entry.label}</strong><br>HEX: <code>${hex}</code></div>
        `;
        grid.appendChild(swatch);
      });
    });
</script>
@endpush
