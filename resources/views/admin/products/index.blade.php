@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 pt-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>SKU</th>
                        <th>Qty</th>
                        <th>Categories</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td><img src="{{$product->thumbnailUrl}}" width="100" height="75" alt="{{$product->title}}"></td>
                            <td>{{ $product->title }}</td>
                            <td>{{ $product->SKU }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>
                                @forelse($product->categories as $category)
                                    {{ $category->name }}

                                    @unless($loop->last)
                                        {{ ' | ' }}
                                    @endunless
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>{{ $product->created_at }}</td>
                            <td>{{ $product->updated_at }}</td>
                            <td>
                                <form method="POST" action="{{route('admin.products.destroy', $product)}}">
                                    @csrf
                                    @method('DELETE')

                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-info"><i class="fa-regular fa-pen-to-square"></i></a>
                                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
