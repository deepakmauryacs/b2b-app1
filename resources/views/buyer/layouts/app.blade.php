@include('buyer.layouts.partials.header')

@yield('content')

@include('buyer.layouts.partials.footer')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@stack('scripts')
