@extends('layouts.app')

@section('title', 'Users - Make it easy')
@section('page-title', 'Users')

@section('content')
<!--USERS_LAYOUT_START-->
      <div class="users-layout">
                <div class="users-actions">
          <button class="users-action-btn btn-camera"><span class="icon">&#128247;</span><span>Camera</span></button>
          <button class="users-action-btn btn-photos"><span class="icon">&#128444;</span><span>My photos</span></button>
          <button class="users-action-btn btn-upload"><span class="icon">&#128228;</span><span>Upload a file</span></button>
          <button class="users-action-btn btn-link"><span class="icon">&#128279;</span><span>Link</span></button>
          <button class="users-action-btn btn-question"><span class="icon">&#10067;</span><span>Ask a question</span></button>
          <button class="users-action-btn btn-paste"><span class="icon">&#128203;</span><span>Paste words</span></button>

          <div class="users-settings">
            <div class="users-settings-dots" aria-hidden="true">
              <span class="dot"></span>
              <span class="dot active"></span>
              <span class="dot"></span>
            </div>
            <div class="users-settings-title">Select</div>
            <div class="level-radio-group" role="radiogroup" aria-label="How easy">
              <input type="radio" id="level-easiest" name="howeasy" value="easiest" class="level-radio">
              <label for="level-easiest" class="level-radio-label" aria-label="Easy English"></label>

              <input type="radio" id="level-easier" name="howeasy" value="easier" class="level-radio" checked>
              <label for="level-easier" class="level-radio-label" aria-label="Easy read"></label>

              <input type="radio" id="level-easy" name="howeasy" value="easy" class="level-radio">
              <label for="level-easy" class="level-radio-label" aria-label="Plain English"></label>
            </div>

            <ul class="users-settings-list">
              <li><span class="emoji">&#128512;</span> Emojis</li>
              <li><span class="emoji">&#9881;</span> Other settings</li>
              <li><span class="emoji">&#127760;</span> Language</li>
              <li><span class="emoji">&#128266;</span> Voices</li>
              <li><span class="emoji">&#128340;</span> History</li>
            </ul>
          </div>
        </div>      </div>

        <section class="users-workspace">
          <div class="workspace-card">
            <div class="workspace-scroll">
              <ul class="workspace-list">
                <li>One bailer or bilge pump</li>
                <li>Fresh water in a safe container</li>
                <li>One waterproof torch</li>
                <li>Two red flares</li>
                <li>Two orange smoke signals</li>
                <li>One distress sheet</li>
                <li>One fire extinguisher (if boat is 5-10 metres)</li>
                <li>One map or chart</li>
                <li>Intermediate Waters</li>
                <li>One life jacket for each person</li>
                <li>One anchor with 3 metres of chain</li>
                <li>Two paddles or oars (if boat is over 10 metres)</li>
              </ul>
            </div>
          </div>

          <div class="workspace-footer">
            <div class="footer-icons"><button aria-label="Add from photos">&#128444;</button><button aria-label="Insert link">&#128279;</button><button aria-label="Add note">&#9997;</button></div>
            <div class="image-preview"><img src="{{ asset('assets/images/Screenshot 2025-06-09 125116.jpg') }}" alt="image preview"><div class="caption">image name.jpg</div></div>
          </div>
        </section>
      </div>
      <!--USERS_LAYOUT_END-->
@endsection

@push('scripts')
<script>
  // Persist and reflect selected reading level using radios
  (function(){
    const KEY = 'mie.choice.howeasy';
    const radios = document.querySelectorAll('input[name="howeasy"]');
    function apply(value){
      radios.forEach(r => { r.checked = (r.value === value); });
    }
    try {
      const saved = localStorage.getItem(KEY);
      if (saved) apply(saved);
    } catch(_){}
    radios.forEach(r => r.addEventListener('change', () => {
      try { localStorage.setItem(KEY, r.value); } catch(_){}
    }));
  })();
  </script>
@endpush
