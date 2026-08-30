<meta name="wa-number" content="{{ wa_number() }}">
<meta name="wa-click-url" content="{{ url('/wa-click') }}">
<script src="{{ asset('js/wa-track.js') }}?v={{ @filemtime(public_path('js/wa-track.js')) ?: time() }}" defer></script>
