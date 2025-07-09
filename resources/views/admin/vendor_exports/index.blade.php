@extends('admin.layouts.app')
@section('title', 'Vendor Export | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Vendor Export</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Select Range</label>
                    <select id="export-range" class="form-select">
                        <option value="0-25000">1-25,000</option>
                        <option value="25000-50000">25,000-50,000</option>
                        <option value="50000-75000">50,000-75,000</option>
                        <option value="75000-100000">75,000-100,000</option>
                    </select>
                </div>
                <div class="progress" id="export-progress-container" style="height:20px; display:none;">
                    <div id="export-progress" class="progress-bar" role="progressbar" style="width:0%">0%</div>
                </div>
                <div id="export-status" class="mt-2"></div>
                <button type="button" id="start-export" class="btn btn-primary mt-3">Export Now</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/exceljs.min.js') }}"></script>
<script>
$(function(){
    $('#start-export').on('click', function(){
        const range = $('#export-range').val().split('-');
        const start = range[0];
        const end = range[1];

        $('#export-progress-container').show();
        $('#export-status').text('Fetching data...');

        $.ajax({
            url: "{{ route('admin.vendors.export-data') }}",
            method: 'GET',
            data: {range_start: start, range_end: end},
            success: async function(data){
                $('#export-status').text('Generating file...');

                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Vendors');
                worksheet.addRow(['ID','Name','Email','Phone','Store Name','GST No','Pincode','Address','Status','Is Verified','Approved Products','Pending Products','Created At']);

                const total = data.length;
                data.forEach((row, idx) => {
                    worksheet.addRow([
                        row.id,
                        row.name,
                        row.email,
                        row.phone,
                        row.vendor_profile ? row.vendor_profile.store_name : '',
                        row.vendor_profile ? row.vendor_profile.gst_no : '',
                        row.vendor_profile ? row.vendor_profile.pincode : '',
                        row.vendor_profile ? row.vendor_profile.address : '',
                        row.status == 1 ? 'Active' : 'Inactive',
                        row.is_profile_verified == 1 ? 'Yes' : 'No',
                        row.approved_products_count,
                        row.pending_products_count,
                        row.created_at
                    ]);
                    const percent = Math.round(((idx+1)/total)*100);
                    $('#export-progress').css('width', percent+'%').text(percent+'%');
                });

                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], {type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'vendors_'+Date.now()+'.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                $('#export-status').text('Export complete');
                setTimeout(() => window.location.reload(), 1000);
            },
            error: function(xhr){
                $('#export-status').text('Error fetching data');
            }
        });
    });
});
</script>
@endpush
