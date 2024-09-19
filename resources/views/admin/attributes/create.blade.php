@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 d-flex align-items-center justify-content-center pt-5">
                <form class="card w-50" method="POST" action="{{route('admin.attributes.store')}}">
                    @csrf

                    <h5 class="card-header">Create Attribute</h5>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Category name"
                                   value="{{ old('name') }}">

                            @error('name')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Options</label>

                            <div class="options-list d-flex flex-column" data-key="0"></div>
                            <button class="btn btn-success add-option">Add option</button>

                            @error('options')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-end">
                        <button type="submit" class="btn btn-outline-success">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
    @vite(['resources/js/admin/attributes.js'])
@endpush
