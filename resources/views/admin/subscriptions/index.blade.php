@extends('admin.layouts.app')
@section('title', 'Vendor Subscriptions | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1 mb-0">Vendor Subscriptions</h4>
                <a href="{{ route('admin.vendor-subscriptions.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Add Subscription
                </a>
            </div>
            <div class="card-body">
                <form id="filter-form" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label" for="vendor">Vendor Name</label>
                        <input type="text" id="vendor" class="form-control" placeholder="Vendor Name">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="plan">Plan Name</label>
                        <select id="plan" class="form-select">
                            <option value="">All Plans</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan }}">{{ $plan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" class="form-select">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" id="search" class="btn btn-primary">
                            <i class="bi bi-search"></i> SEARCH
                        </button>
                        <button type="button" id="reset" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-clockwise"></i> RESET
                        </button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped" id="subscriptions-table" style="width:100%">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Vendor</th>
                                <th>Plan</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="subscriptions-table-body-content">
                            <tr>
                                <td colspan="7" class="text-center">Loading Subscriptions...</td>
                            </tr>
                        </tbody>
                        <tfoot id="subscriptions-table-foot-content">
                            <tr>
                                <td colspan="7" class="text-center"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    fetchSubscriptionsData(1);

    var currentAjaxRequest = null;

    function fetchSubscriptionsData(page = 1, perPage = null) {
        if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
            currentAjaxRequest.abort();
        }
        $('#subscriptions-table-body-content').html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        $('#subscriptions-table-foot-content').empty();

        const filters = {
            vendor: $('#vendor').val(),
            plan: $('#plan').val(),
            status: $('#status').val()
        };
        perPage = perPage || $('#perPage').val() || 10;

        currentAjaxRequest = $.ajax({
            url: "{{ route('admin.vendor-subscriptions.render-table') }}",
            method: 'GET',
            data: {
                page: page,
                per_page: perPage,
                ...filters
            },
            success: function(response) {
                const $responseHtml = $(response);
                $('#subscriptions-table-body-content').html($responseHtml.filter('tbody').html());
                $('#subscriptions-table-foot-content').html($responseHtml.filter('tfoot').html());
            },
            error: function(xhr) {
                if (xhr.statusText === 'abort') {
                    return;
                }
                $('#subscriptions-table-body-content').html('<tr><td colspan="7" class="text-center text-danger">Error loading subscriptions.</td></tr>');
            },
            complete: function() {
                currentAjaxRequest = null;
            }
        });
    }

    $('#search').on('click', function() {
        fetchSubscriptionsData(1);
    });

    $('#reset').on('click', function() {
        $('#filter-form').find('input, select').val('');
        fetchSubscriptionsData(1);
    });

    $(document).on('click', '#subscriptions-table-foot-content a.page-link', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        if (page) {
            fetchSubscriptionsData(page);
        }
    });

    $(document).on('change', '#perPage', function() {
        fetchSubscriptionsData(1, $(this).val());
    });
});
</script>
@endsection
