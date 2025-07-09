<!--header start-->
<header id="masthead" class="header ttm-header-style-01">
    
    
    <!-- header_main -->
    <div class="header_main">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-3 col-3 order-1">
                    <!-- site-branding -->
                    <div class="site-branding">
                        <a class="home-link" href="{{ url('/') }}" title="Fixfellow" rel="home">
                            <img id="logo-img" class="img-center" src="{{ asset('assets/buyer_assets/images/logo-img.png') }}" alt="logo-img">
                        </a>
                    </div><!-- site-branding end -->
                </div>
                <div class="col-lg-6 col-12 order-lg-2 order-3 text-lg-left text-right">
                    <div class="header_search"><!-- header_search -->
                        <div class="header_search_content">
                            <div id="search_block_top" class="search_block_top">
                                <form id="searchbox" method="get" action="#">
                                    <input class="search_query form-control" type="text" id="search_query_top" name="s" placeholder="Search For Product...." value="">           
                                   
                                    <button type="submit" name="submit_search" class="btn btn-default button-search"><i class="fa fa-search"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>    
                    <!-- header_search end -->
                </div>
                <div class="col-lg-3 col-9 order-lg-3 order-2 text-lg-left text-right">
                    <!-- header_extra -->
                    <div class="header_extra d-flex flex-row align-items-center justify-content-end">
                        <div class="account dropdown">
                            <div class="d-flex flex-row align-items-center justify-content-start">
                                <div class="account_icon">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="account_content">
                                    <div class="account_text"><a href="#">Sign In</a></div>
                                </div>
                            </div>
                            <div class="account_extra dropdown_link" data-toggle="dropdown">Account</div>
                            <aside class="widget_account dropdown_content">
                                <div class="widget_account_content">
                                    <ul>
                                        <li><i class="fa fa-sign-in mr-2"></i><a href="{{ route('login') }}">Login</a></li>
                                        <li><i class="fa fa-sign-in mr-2"></i><a href="{{ route('register') }}">Register</a></li>
                                    </ul>
                                </div>
                            </aside>
                        </div>
                        <div class="cart dropdown">
                            <div class="dropdown_link d-flex flex-row align-items-center justify-content-end" data-toggle="dropdown">
                                <div class="cart_icon">
                                    <i class="fa fa-shopping-cart"></i>
                                    <div class="cart_count">02</div>
                                </div>
                                <div class="cart_content">
                                    <div class="cart_text"><a href="#">My Cart</a></div>
                                    <div class="cart_price">$257.00</div>
                                </div>
                            </div>
                            <aside class="widget_shopping_cart dropdown_content">
                                <ul class="cart-list">
                                    <li>
                                        <a href="#" class="photo"><img src="{{ asset('images/product/pro-front-02.png') }}" class="cart-thumb" alt="" /></a>
                                        <h6><a href="#">Impact Wrench</a></h6>
                                        <p>2x - <span class="price">$220.00</span></p>
                                    </li>
                                    <li>
                                        <a href="#" class="photo"><img src="{{ asset('images/product/pro-front-03.png') }}" class="cart-thumb" alt="" /></a>
                                        <h6><a href="#">Demolition Breaker</a></h6>
                                        <p>1x - <span class="price">$38.00</span></p>
                                    </li>
                                    <li class="total">
                                        <span class="pull-right"><strong>Total</strong>: $257.00</span>
                                        <a href="#" class="btn btn-default btn-cart">Cart</a>
                                    </li>
                                </ul>
                            </aside>
                        </div>
                    </div><!-- header_extra end -->
                </div>
            </div>
        </div>
    </div><!-- haeder-main end -->
    
    <!-- site-header-menu -->
    <div id="site-header-menu" class="site-header-menu ttm-bgcolor-white clearfix">
        <div class="site-header-menu-inner stickable-header">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main_nav_content d-flex flex-row">
                            <div class="cat_menu_container">
                                <a href="#" class="cat_menu d-flex flex-row align-items-center">
                                    <div class="cat_icon"><i class="fa fa-bars"></i></div>
                                    <div class="cat_text"><span>Shop by</span><h4>Categories</h4></div>
                                </a>
                                <ul class="cat_menu_list menu-vertical">
                                    <li><a href="#" class="close-side"><i class="fa fa-times"></i></a></li>
                                    <li class="parent">
                                        <a href="#">Toolbox</a>
                                        <div class="sub-menu megamenu column3">
                                            <ul class="list-unstyled childs_1">
                                                <li class="title"><a href="#">Materials</a>
                                                    <div class="sub-menu">
                                                        <ul class="list-unstyled childs_2">
                                                            <li><a href="#">Miter Box</a></li>
                                                            <li><a href="#">Scraper</a></li>
                                                            <li><a href="#">Screwdriver</a></li>
                                                            <li><a href="#">Glass Cutter</a></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                            <ul class="list-unstyled childs_1">
                                                <li class="title"><a href="#">Accessories</a>
                                                    <div class="sub-menu">
                                                        <ul class="list-unstyled childs_2">
                                                            <li><a href="#">Hacksaw</a></li>
                                                            <li><a href="#">Pitchfork</a></li>
                                                            <li><a href="#">Circular Saw</a></li>
                                                            <li><a href="#">Hex Wrench</a></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                            <ul class="list-unstyled childs_1">
                                                <li class="title"><a href="#">Cutting Tools‎ </a>
                                                    <div class="sub-menu">
                                                        <ul class="list-unstyled childs_2">
                                                            <li><a href="#">Axes‎</a></li>
                                                            <li><a href="#">Scissors</a></li>
                                                            <li><a href="#">Saws‎</a></li>
                                                            <li><a href="#">Knives</a></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="parent">
                                        <a href="#">Hand Tool</a>
                                        <div class="sub-menu megamenu column3">
                                            <ul class="list-unstyled childs_1">
                                                <li class="title"><a href="#">Gas Equipment</a>
                                                    <div class="sub-menu">
                                                        <ul class="list-unstyled childs_2">
                                                            <li><a href="#">Dust Collector</a></li>
                                                            <li><a href="#">Heat Guns</a></li>
                                                            <li><a href="#">Impact Drivers</a></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                            <ul class="list-unstyled childs_1">
                                                <li class="title"><a href="#">Cordless Tools</a>
                                                    <div class="sub-menu">
                                                        <ul class="list-unstyled childs_2">
                                                            <li><a href="#">Bare Tools</a></li>
                                                            <li><a href="#">Grinders</a></li>
                                                            <li><a href="#">Impact Drivers</a></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                            <ul class="list-unstyled childs_1">
                                                <li class="title"><a href="#">Air Tools</a>
                                                    <div class="sub-menu">
                                                        <ul class="list-unstyled childs_2">
                                                            <li><a href="#">Air Hoses</a></li>
                                                            <li><a href="#">Chipping Hammers</a></li>
                                                            <li><a href="#">Compressors</a></li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li><a href="#">Cutter Wood</a></li>
                                    <li><a href="#">Power Tools</a></li>
                                    <li><a href="#">Saw Map</a></li>
                                    <li><a href="#">Electric Tools</a></li>
                                    <li><a href="#">Collapsible</a></li>
                                    <li><a href="#">Corded Planer</a></li>
                                    <li class="parent-plus"><a href="#">More Categories</a>
                                        <div class="plus-menu">
                                            <ul class="list-unstyled">
                                                <li><a href="#">Hacksaw</a></li>
                                                <li><a href="#">Post Hole</a></li>
                                                <li><a href="#">Tool Belt</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!--site-navigation -->
                            <div id="site-navigation" class="site-navigation">
                                <div class="btn-show-menu-mobile menubar menubar--squeeze">
                                    <span class="menubar-box">
                                        <span class="menubar-inner"></span>
                                    </span>
                                </div>
                                <!-- menu -->
                                <nav class="menu menu-mobile" id="menu">
                                    <ul class="nav">
                                        <li class="mega-menu-item active">
                                            <a href="#" class="mega-menu-link">Home</a>
                                            <ul class="mega-submenu">
                                                <li><a href="{{ url('/') }}">Sample Homepage 1</a></li>
                                                <li><a href="{{ url('/home-2') }}">Sample Homepage 2</a></li>
                                                <li class="active"><a href="{{ url('/home-3') }}">Sample Homepage 3</a></li>
                                                <li class="mega-menu-item">
                                                    <a href="#" class="mega-menu-link">Header Styles</a>
                                                    <ul class="mega-submenu">
                                                        <li><a href="{{ url('/') }}">Header Style 01</a></li>
                                                        <li><a target="_blank" href="{{ url('/header-style-02') }}">Header Style 02</a></li>
                                                        <li><a target="_blank" href="{{ url('/header-style-03') }}">Header Style 03</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="mega-menu-item">
                                            <a href="#" class="mega-menu-link">Pages</a>
                                            <ul class="mega-submenu">
                                                <li><a href="#">About Us</a></li>
                                                <li><a href="#">Login</a></li>
                                                <li><a href="#">Register</a></li>
                                                <li><a href="#">Contact Us</a></li>
                                                <li><a href="#">Error Page</a></li>
                                            </ul>
                                        </li>
                                        <li class="mega-menu-item megamenu-fw">
                                            <a href="#" class="mega-menu-link">Shop</a>
                                            <ul class="mega-submenu megamenu-content" role="menu">
                                                <li>
                                                    <div class="row">
                                                        <div class="col-menu col-md-3">
                                                            <h6 class="title">Shop Page Layout</h6>
                                                            <div class="content">
                                                                <ul class="menu-col">
                                                                    <li><a href="#">Shop Default</a></li>
                                                                    <li><a href="#">Shop Left Sidebar</a></li>
                                                                    <li><a href="#">Shop Right Sidebar</a></li>
                                                                </ul>
                                                            </div>
                                                        </div><!-- end col-3 -->
                                                        <div class="col-menu col-md-3">
                                                            <h6 class="title">Shop Pages</h6>
                                                            <div class="content">
                                                                <ul class="menu-col">
                                                                    <li><a href="#">Cart</a></li>
                                                                    <li><a href="#">Checkout</a></li>
                                                                    <li><a href="#">Account/Login</a></li>
                                                                </ul>
                                                            </div>
                                                        </div><!-- end col-3 -->
                                                        <div class="col-menu col-md-3">
                                                            <h6 class="title">Shop Product Layout</h6>
                                                            <div class="content">
                                                                <ul class="menu-col">
                                                                    <li><a href="#">Product Layout 1</a></li>
                                                                    <li><a href="#">Product Layout 2</a></li>
                                                                    <li><a href="#">Product Layout 3</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>    
                                                        <div class="col-menu col-md-3">
                                                            <div class="content">
                                                                <ul class="menu-col">
                                                                    <li><a href="#">
                                                                        <img class="img-fluid" src="{{ asset('images/menu-item-banner.jpg') }}" alt="bimg">
                                                                    </a></li>
                                                                </ul>
                                                            </div>
                                                        </div><!-- end col-3 -->
                                                    </div><!-- end row -->
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="mega-menu-item">
                                            <a href="#" class="mega-menu-link">Blog</a>
                                            <ul class="mega-submenu">
                                                <li class=""><a href="#">Blog Classic</a></li>
                                                <li class=""><a href="#">Blog Grid</a></li>
                                                <li class=""><a href="#">Single Blog View</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Contact Us</a></li>
                                    </ul>
                                </nav>  
                            </div><!-- site-navigation end-->
                            <div class="user_zone_block d-flex flex-row align-items-center justify-content-end ml-auto">
                                <div class="icon"><i class="bi bi-graph-up-arrow"></i></div>
                                <h6 class="text"><a href="javascript:void(0)">Free Listing</a></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- site-header-menu end -->
</header><!--header end-->