@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 py-5 text-center">
                <h1>Cart</h1>
            </div>
        </div>
        @if ($cart->content()->isEmpty())
            <div class="row">
                <div class="col text-center">
                    <h3>The cart is empty</h3>
                    <a href="{{ route('home') }}" class="btn btn-primary">Go shopping</a>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12 col-sm-9">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cart->content() as $item)
                            <tr>
                                <td><img src="{{ $item->model->thumbnailUrl }}" style="width: 75px;"
                                         alt="{{ $item->name }}"></td>
                                <td>
                                    <a href="{{ route('products.show', $item->model) }}">{{ $item->name }}</a>
                                    @if ($item->options)
                                        @foreach($item->options as $attribute => $option)
                                            <p>{{$attribute}}: <strong>{{$option}}</strong></p>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('cart.update', $item->model) }}" method="POST">
                                        @method('PUT')
                                        @csrf
                                        <input type="hidden" name="rowId" value="{{ $item->rowId }}"/>
                                        <input type="number" class="form-control product-qty" min="1"
                                               max="{{ $item->model->quantity }}" name="qty" value="{{ $item->qty }}"/>
                                    </form>
                                </td>
                                <td>{{ $item->price }} $</td>
                                <td>{{ $item->subtotal }} $</td>
                                <td>
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <input type="hidden" name="rowId" value="{{ $item->rowId }}"/>
                                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-sm-3">
                    <h1 class="mb-3">Summary</h1>
                    <table class="table table-striped table-hover">
                        <tbody>
                        <tr>
                            <td>Subtotal</td>
                            <td>{{ $cart->subtotal() }} $</td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td>{{ $cart->tax() }} $</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>{{ $cart->total() }} $</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <a href="{{ route('checkout') }}" class="btn w-100 btn-outline-success">Proceed to checkout</a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('footer-js')
    @vite(['resources/js/cart.js'])
@endpush
