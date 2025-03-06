@extends('adminlte::page')

@section('title', 'Form Kategori')

@section('content_header')
    <h5><a href="{{route('categories.index')}}">Kategori</a> / Form Kategori</h5>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Produk</h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}" method="POST">
                @csrf
                @if(isset($category))
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label for="name">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
                </div>
                <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Simpan' }}</button>
            </form>
        </div>
    </div>
@stop
