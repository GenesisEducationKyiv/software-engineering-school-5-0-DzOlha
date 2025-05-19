<a href="{{ url('/') }}">
    <button class="homepage-btn">Homepage</button>
</a>

<div class="hero">
    <div class="form-container">
        <h1>{{ $heading }}</h1>

        @if(isset($showMessageDiv) && $showMessageDiv)
            <div id="message" class="message" style="display: none;"></div>
        @endif
    </div>

    <div class="image-section" id="weatherImage">
        <img src="{{ asset('resources/img/hero.png') }}" alt="Weather Hero Image" />
    </div>
</div>

@if(isset($script))
    <script type="module" src="{{ asset($script) }}"></script>
@endif
