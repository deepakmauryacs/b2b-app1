@extends('admin.layouts.app')
@section('title', 'Dashboard | Deal24hours')
@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Recent Orders</h4>
                        <a href="#" class="btn btn-sm btn-soft-primary">
                            <i class="bx bx-plus me-1"></i>Create Order
                        </a>
                    </div>
                </div>
                <div class="table-responsive table-centered">
                    <table class="table mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-3">Order ID 1.</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Customer Name</th>
                                <th>Email ID</th>
                                <th>Phone No.</th>
                                <th>Address</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
            ['id' => '#RB5625', 'date' => '29 April 2024', 'product' => 'product-1(1).png', 'customer' => 'Anna M. Hines', 'email' => 'anna.hines@mail.com', 'phone' => '(+1)-555-1564-261', 'address' => 'Burr Ridge/Illinois', 'payment' => 'Credit Card', 'status' => 'Completed', 'status_type' => 'success'],
            ['id' => '#RB9652', 'date' => '25 April 2024', 'product' => 'product-4.png', 'customer' => 'Judith H. Fritsche', 'email' => 'judith.fritsche.com', 'phone' => '(+57)-305-5579-759', 'address' => 'SULLIVAN/Kentucky', 'payment' => 'Credit Card', 'status' => 'Completed', 'status_type' => 'success'],
            ['id' => '#RB5984', 'date' => '25 April 2024', 'product' => 'product-5.png', 'customer' => 'Peter T. Smith', 'email' => 'peter.smith@mail.com', 'phone' => '(+33)-655-5187-93', 'address' => 'Yreka/California', 'payment' => 'Pay Pal', 'status' => 'Completed', 'status_type' => 'success'],
            ['id' => '#RB3625', 'date' => '21 April 2024', 'product' => 'product-6.png', 'customer' => 'Emmanuel J. Delcid', 'email' => 'emmanuel.delicid@mail.com', 'phone' => '(+30)-693-5553-637', 'address' => 'Atlanta/Georgia', 'payment' => 'Pay Pal', 'status' => 'Processing', 'status_type' => 'primary'],
            ['id' => '#RB8652', 'date' => '18 April 2024', 'product' => 'product-1(2).png', 'customer' => 'William J. Cook', 'email' => 'william.cook@mail.com', 'phone' => '(+91)-855-5446-150', 'address' => 'Rosenberg/Texas', 'payment' => 'Credit Card', 'status' => 'Processing', 'status_type' => 'primary'],
        ] as $order)
                                <tr>
                                    <td class="ps-3"><a href="#">{{ $order['id'] }}</a></td>
                                    <td>{{ $order['date'] }}</td>
                                    <td><img src="{{ asset('assets/images/products/' . $order['product']) }}"
                                            alt="{{ $order['product'] }}" class="img-fluid avatar-sm"></td>
                                    <td><a href="#!">{{ $order['customer'] }}</a></td>
                                    <td>{{ $order['email'] }}</td>
                                    <td>{{ $order['phone'] }}</td>
                                    <td>{{ $order['address'] }}</td>
                                    <td>{{ $order['payment'] }}</td>
                                    <td><i
                                            class="bx bxs-circle text-{{ $order['status_type'] }} me-1"></i>{{ $order['status'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer border-top">
                    <div class="row g-3">
                        <div class="col-sm">
                            <div class="text-muted">
                                Showing <span class="fw-semibold">5</span> of <span class="fw-semibold">90,521</span> orders
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <ul class="pagination m-0">
                                <li class="page-item">
                                    <a href="#" class="page-link"><i class="bx bx-left-arrow-alt"></i></a>
                                </li>
                                <li class="page-item active">
                                    <a href="#" class="page-link">1</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link">2</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link">3</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link"><i class="bx bx-right-arrow-alt"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Dashboard Js -->
    <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
@endsection
