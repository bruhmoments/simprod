@extends('adminlte::page')

@section('title', 'Form Produk')

@section('content_header')
    <h5><a href="{{route('products.index')}}">Produk</a> / Form Produk</h5>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Produk</h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label for="name">Nama Produk</label>
                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori Produk</label>
                    <select name="category_id" class="form-control select2" required>
                        {{-- <option value="">Pilih Kategori</option> --}}
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="purchase_price">Harga Beli Barang</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light">Rp.</span>
                        </div>
                        <input type="number" name="purchase_price" class="form-control" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? '') }}" required>
                    </div>
                    @error('purchase_price') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="selling_price">Harga Jual Barang</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light">Rp.</span>
                        </div>
                        <input type="number" name="selling_price" class="form-control" id="selling_price" value="{{ old('selling_price', $product->selling_price ?? '') }}" required readonly>
                    </div>
                    @error('selling_price') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="stock">Stok Barang</label>
                    <input type="number" name="stock" class="form-control" id="stock" value="{{ old('stock', $product->stock ?? '') }}" required>
                    @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="file" name="image" class="form-control" id="image" accept="image/jpeg, image/png" required>
                            </div>
                            <div class="col-2">
                                <small class="text-muted">Maksimal ukuran gambar 100KB</small>
                                <br>
                                <img id="image-preview" src="{{ isset($product) ? asset('product_images/' . $product->image_path) : '#' }}" alt="Image Preview" style="max-width: 200px; {{ isset($product) ? '' : 'display:none;'}}">
                            </div>
                        </div>
                    @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Simpan' }}</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $('#image').change(function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                $('#image-preview').attr('src', reader.result).show();
            };
            reader.readAsDataURL(this.files[0]);
        });

        $('#purchase_price').on('input', function() {
            var buyPrice = parseFloat($(this).val());
            if (!isNaN(buyPrice)) {
                var sellPrice = buyPrice * 1.30;
                $('#selling_price').val(sellPrice.toFixed(0));
            } else {
                $('#selling_price').val('');
            }
        });
    </script>
@stop
