<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>@yield('title', 'Dashboard | Deal24hours')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="A fully responsive premium admin dashboard template" />
<meta name="author" content="Techzaa" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

<!-- Vendor css -->
<link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Icons css -->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- App css -->
<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />

<!-- Gridjs Plugin css -->
<link href="{{ asset('assets/vendor/gridjs/theme/mermaid.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Theme Config js -->
<script src="{{ asset('assets/js/config.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .bi-bell,
    .bi-gear {
        font-size: 20px;
    }
    .scrollbar .nav-icon i,
    #navbar-nav .nav-icon i {
        font-size: 20px !important;
        line-height: 1;
        vertical-align: middle;
    }
    /* Preloader styles */
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #ffffff;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease;
    }
    #preloader .spinner {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    #preloader.hidden {
        opacity: 0;
        visibility: hidden;
    }
</style>

@stack('styles')
</head>
<body>
<!-- Preloader -->
<div id="preloader">
    <div class="spinner"></div>
</div>
<div class="wrapper">
    <!-- Topbar -->
    <header class="topbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <div class="d-flex align-items-center">
                    <div class="topbar-item">
                        <button type="button" class="button-toggle-menu me-2">
                            <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                        </button>
                    </div>
                    <div class="topbar-item">
                        <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Welcome Deal24hours!</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <!-- Theme Color -->
                    <div class="topbar-item">
                        <button type="button" class="topbar-button" id="light-dark-mode">
                            <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                        </button>
                    </div>
                    <!-- Notification -->
                    <div class="dropdown topbar-item">
                        <button type="button" class="topbar-button position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">3<span class="visually-hidden">unread messages</span></span>
                        </button>
                        <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end" aria-labelledby="page-header-notifications-dropdown">
                            <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0);" class="text-dark text-decoration-underline">
                                            <small>Clear All</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 280px;">
                                @foreach([
                                    ['name' => 'Josephine Thompson', 'avatar' => 'avatar-1.jpg', 'message' => 'commented on admin panel <span>" Wow 😍! this admin looks good and awesome design"</span>'],
                                    ['name' => 'Donoghue Susan', 'avatar' => 'D', 'message' => 'Hi, How are you? What about our next meeting'],
                                    ['name' => 'Jacob Gines', 'avatar' => 'avatar-3.jpg', 'message' => 'Answered to your comment on the cash flow forecast\'s graph 🔔.'],
                                    ['name' => null, 'avatar' => 'iconamoon:comment-dots-duotone', 'message' => 'You have received <b>20</b> new messages in the conversation'],
                                    ['name' => 'Shawn Bunch', 'avatar' => 'avatar-5.jpg', 'message' => 'Commented on Admin']
                                ] as $notification)
                                    <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom text-wrap">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                @if(strpos($notification['avatar'], '.jpg') !== false)
                                                    <img src="{{ asset('assets/images/users/' . $notification['avatar']) }}" class="img-fluid me-2 avatar-sm rounded-circle" alt="{{ $notification['avatar'] }}" />
                                                @else
                                                    <div class="avatar-sm me-2">
                                                        <span class="avatar-title bg-soft-{{ $notification['avatar'] == 'D' ? 'info text-info' : 'warning text-warning' }} fs-20 rounded-circle">
                                                            {{ $notification['avatar'] == 'D' ? 'D' : '<iconify-icon icon="' . $notification['avatar'] . '"></iconify-icon>' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0">
                                                    @if($notification['name'])
                                                        <span class="fw-medium">{{ $notification['name'] }}</span>
                                                    @endif
                                                    {!! $notification['message'] !!}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-center py-3">
                                <a href="javascript:void(0);" class="btn btn-primary btn-sm">View All Notification <i class="bx bx-right-arrow-alt ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Theme Setting -->
                    <div class="topbar-item d-none d-md-flex">
                        <button type="button" class="topbar-button" id="theme-settings-btn" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
                            <i class="bi bi-gear"></i>
                        </button>
                    </div>
                   
                    <!-- User -->
                    <div class="dropdown topbar-item">
                        <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <img class="rounded-circle" width="32" src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="avatar-3">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Welcome {{ Auth::user()->name ?? 'Gaston' }}!</h6>
                            
                            <a class="dropdown-item" href="{{ route('vendor.profile.show') }}">
                                <i class="bx bx-user-circle text-muted fs-18 align-middle me-1"></i><span class="align-middle">Profile</span>
                            </a>
                            
                            <a class="dropdown-item" href="#">
                                <i class="bx bx-help-circle text-muted fs-18 align-middle me-1"></i><span class="align-middle">Help</span>
                            </a>
                           
                            <div class="dropdown-divider my-1"></div>
                            <!-- Logout Link -->
                            <a class="dropdown-item text-danger"
                               href="#"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bx bx-log-out fs-18 align-middle me-1"></i>
                                <span class="align-middle">Logout</span>
                            </a>

                            <!-- Hidden Logout Form -->
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    

    <!-- Theme Settings -->
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
            <h5 class="text-white m-0">Theme Settings</h5>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div data-simplebar class="h-100">
                <div class="p-3 settings-bar">
                    <div>
                        <h5 class="mb-3 font-16 fw-semibold">Color Scheme</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-light" value="light">
                            <label class="form-check-label" for="layout-color-light">Light</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-dark" value="dark">
                            <label class="form-check-label" for="layout-color-dark">Dark</label>
                        </div>
                    </div>
                    <div>
                        <h5 class="my-3 font-16 fw-semibold">Topbar Color</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-light" value="light">
                            <label class="form-check-label" for="topbar-color-light">Light</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-dark" value="dark">
                            <label class="form-check-label" for="topbar-color-dark">Dark</label>
                        </div>
                    </div>
                    <div>
                        <h5 class="my-3 font-16 fw-semibold">Menu Color</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-light" value="light">
                            <label class="form-check-label" for="leftbar-color-light">Light</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-dark" value="dark">
                            <label class="form-check-label" for="leftbar-color-dark">Dark</label>
                        </div>
                    </div>
                    <div>
                        <h5 class="my-3 font-16 fw-semibold">Sidebar Size</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-default" value="default">
                            <label class="form-check-label" for="leftbar-size-default">Default</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small" value="condensed">
                            <label class="form-check-label" for="leftbar-size-small">Condensed</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-hidden" value="hidden">
                            <label class="form-check-label" for="leftbar-hidden">Hidden</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small-hover-active" value="sm-hover-active">
                            <label class="form-check-label" for="leftbar-size-small-hover-active">Small Hover Active</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small-hover" value="sm-hover">
                            <label class="form-check-label" for="leftbar-size-small-hover">Small Hover</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3 text-center">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-danger w-100" id="reset-layout">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="main-nav">
        <div class="logo-box">
            <a href="#" class="logo-dark">
                <img src="{{ asset('assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
                <img src="{{ asset('assets/images/logo-dark.png') }}" class="logo-lg" alt="logo dark">
            </a>
            <a href="#" class="logo-light">
                <img src="{{ asset('assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
                <img src="{{ asset('assets/images/logo-light.png') }}" class="logo-lg" alt="logo light">
            </a>
        </div>
        <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
            <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
        </button>
        <div class="scrollbar" data-simplebar>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title">General</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('vendor.dashboard') }}">
                        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="nav-text"> Dashboard </span>
                    </a>
                </li>
                @foreach([
                    ['title' => 'Products', 'icon' => 'bi-box-seam', 'id' => 'sidebarProducts', 'items' => [
                        ['name' => 'Add Product ', 'route' => 'vendor.products.create'],
                        ['name' => 'Product List', 'route' => 'vendor.products.index'],
                        ['name' => 'Approved Products', 'route' => 'vendor.products.approved'],
                        ['name' => 'Pending Products', 'route' => 'vendor.products.pending'],
                        ['name' => 'Rejected Products', 'route' => 'vendor.products.rejected'],
                    ]],
                    ['title' => 'Inventory Management', 'icon' => 'bi-stack', 'id' => 'sidebarInventory', 'items' => [
                        ['name' => 'Inventory List', 'route' => 'vendor.inventory.index'],
                    ]],
                    ['title' => 'Warehouses', 'icon' => 'bi-building', 'id' => 'sidebarWarehouses', 'items' => [
                        ['name' => 'Warehouse List', 'route' => 'vendor.warehouses.index'],
                    ]],
                    ['title' => 'Help & Support', 'icon' => 'bi-question-circle', 'id' => 'sidebarHelpSupport', 'items' => [
                        ['name' => 'Add Request', 'route' => 'vendor.help-support.create'],
                        ['name' => 'Request List', 'route' => 'vendor.help-support.index'],
                    ]],
                    ['title' => 'Settings', 'icon' => 'bi-gear', 'id' => 'sidebarSettings', 'items' => [
                        ['name' => 'Profile', 'route' => 'vendor.profile.show'],
                        ['name' => 'Change Password', 'route' => 'vendor.password.edit'],
                    ]]

                ] as $menu)
                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#{{ $menu['id'] }}" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="{{ $menu['id'] }}">
                            <span class="nav-icon">
                                <i class="bi {{ strpos($menu['icon'], 'solar:') === 0 ? 'iconify' : $menu['icon'] }}" 
                                   @if(strpos($menu['icon'], 'solar:') === 0) data-icon="{{ $menu['icon'] }}" @endif>
                                </i>
                            </span>
                            <span class="nav-text"> {{ $menu['title'] }} </span>
                        </a>
                        <div class="collapse" id="{{ $menu['id'] }}">
                            <ul class="nav sub-navbar-nav">
                                @foreach($menu['items'] as $item)
                                    <li class="sub-nav-item">
                                        <a class="sub-nav-link" href="{{ route($item['route']) }}">{{ $item['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endforeach
                
                <!-- Additional sidebar items can be added similarly -->
            </ul>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
        <div class="container-fluid">
            @yield('content')
        </div>
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center">
                        <script>document.write(new Date().getFullYear())</script> © Larkon. Crafted by <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon> <a href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Vendor Javascript -->
<script src="{{ asset('assets/js/vendor.js') }}"></script>
<!-- App Javascript -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- Gridjs Plugin js -->
<script src="{{ asset('assets/vendor/gridjs/gridjs.umd.js') }}"></script>

<!-- Gridjs Demo js -->
<script src="{{ asset('assets/js/components/table-gridjs.js') }}"></script>


<!-- Vector Map Js -->
<script src="{{ asset('assets/vendor/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('assets/vendor/jsvectormap/maps/world.js') }}"></script>
<!-- Preloader Javascript -->
<script>
    window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        setTimeout(() => {
            preloader.classList.add('hidden');
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        }, 300); // Delay for better UX
    });
</script>
<script>
$(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if (typeof flatpickr !== 'undefined') {
        $('.date-picker').flatpickr({ dateFormat: 'd-m-Y' });
    }
});
</script>
<script>
// Toastr global options
toastr.options = {
    "closeButton": true,           // Show close (×) button
    "progressBar": true,           // Show progress bar
    "positionClass": "toast-top-right", // Position of the toast
    "timeOut": "5000",             // Auto-dismiss after 5 seconds
    "extendedTimeOut": "1000"      // Extra time on hover
};
</script>
@stack('scripts')
</body>
</html>
