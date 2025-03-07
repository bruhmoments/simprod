@extends('adminlte::page')

@section('title', 'Produk')

@section('content_header')
    <h5>List Produk</h5>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper {
            position: relative;
        }

        .table-blur {
            filter: blur(3px);
            pointer-events: none;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            z-index: 10;
        }
    </style>
@stop


@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Tambah Produk</a>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Produk</h3>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <label for="filter-category">Filter Kategori: </label>
                    <select id="filter-category" class="form-control" style="width: 100%;">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <a href="{{ route('products.export') }}" id="export-link" class="btn btn-success">Download Excel</a>
                </div>            
            </div>
            <br/>
            <table id="products-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                method: 'POST',
                ajax: {
                    url: '{{ route('products.getProducts') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.category_id = $('#filter-category').val();
                    }
                },
                columns: [
                    { data: null, name: null, orderable: false, searchable: false, render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }},
                    { data: 'image', name: 'image', orderable: false, searchable: false, render: function(data) {
                        return '<img src="' + data + '" width="100" height="100">';
                    }},
                    { data: 'name', name: 'name' },
                    { data: 'category', name: 'category' },
                    { data: 'purchase_price', name: 'purchase_price', orderable: true, searchable: true, render: function(data) {
                        // Separator "." tiap 3 digit
                        return 'Rp. ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }},
                    { data: 'selling_price', name: 'selling_price', orderable: true, searchable: true, render: function(data) {
                        // Separator "." tiap 3 digit
                        return 'Rp. ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }},
                    { data: 'stock', name: 'stock' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            table.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#products-table').addClass('table-blur');
                    if ($('.loading-overlay').length === 0) {
                        $('.dataTables_wrapper').append('<div class="loading-overlay">Loading...</div>');
                    } else {
                        $('#products-table').removeClass('table-blur');
                        $('.loading-overlay').remove();
                    }
                }
            });

            $('#filter-category').on('change', function() {
                $('#products-table').addClass('table-blur');
                $('.loading-overlay').remove();

                table.ajax.reload(() => {
                    $('#products-table').removeClass('table-blur');
                });
            });

            table.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#products-table').addClass('table-blur');
                    if ($('.loading-overlay').length === 0) {
                        $('.dataTables_wrapper').append('<div class="loading-overlay">Loading...</div>');
                    }
                } else {
                    $('#products-table').removeClass('table-blur');
                    $('.loading-overlay').remove();
                }
            });

            $('body').on('click', '.delete', function() {
                var productId = $(this).data('id');
                var confirmDelete = confirm("Yakin ingin menghapus produk ini?");
                
                if (confirmDelete) {
                    $.ajax({
                        url: '{{ url("products") }}/' + productId,
                        type: 'DELETE',
                        success: function(response) {
                            alert(response.success);
                            table.ajax.reload();
                        },
                        error: function(error) {
                            alert('Error deleting product');
                        }
                    });
                }
            });
        });

        function filterCategory() {
            var categoryId = $('#filter-category').value;
            var exportLink = $('#export-link');
            
            if (categoryId) {
                exportLink.href = "{{ route('products.export') }}" + "?category_id=" + categoryId;
            } else {
                exportLink.href = "{{ route('products.export') }}";
            }
        }
    </script>
@stop
