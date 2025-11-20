{{-- META HTML --}}
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<title>{{ $title }}</title>
@vite(['resources/css/app.css'])
@vite(['resources/css/pwaforwp-main.css'])
@vite(['resources/js/sweetalert.js'])

<style>
  /* GLOBAL MANDATORY STYLES */
  @import url("https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css");
  @import url("https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-flags.min.css");
  @import url("https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-socials.min.css");
  @import url("https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-payments.min.css");
  @import url("https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-vendors.min.css");
  @import url("https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-marketing.min.css");
  @import url("https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-themes.min.css");

  /* ADDITIONALS */
  @import url("https://rsms.me/inter/inter.css");
  @import url("https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css");
  @import url("https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css");
</style>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js" async defer></script>
