@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 py-5">
                <h1>Products</h1>
            </div>
        </div>
        <form action="{{route('products.index')}}" method="GET" class="row">
            <div class="col-12 py-5 d-flex align-items-center justify-content-end">
                <label for="per_page">Per page</label>
                <select name="per_page" id="per_page">
                    <option value="1" @if($per_page == 1) selected @endif>1</option>
                    <option value="5" @if($per_page == 5) selected @endif>5</option>
                    <option value="10" @if($per_page == 10) selected @endif>10</option>
                    <option value="15" @if($per_page == 15) selected @endif>15</option>
                </select>
            </div>
            @unless($attributes->isEmpty())
                <div class="col-12 col-md-4 col-lg-3">
                    @foreach($attributes as $key => $attr)
                        <h4>{{$key}}</h4>
                        @foreach($attr as $option)
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="{{$option->id}}"
                                       name="options[]"
                                       id="option_{{$option->id}}}"
                                       @if(in_array($option->id, $selectedAttrs)) checked @endif
                                >
                                <label class="form-check-label" for="option_{{$option->id}}}">
                                    {{$option->value}} ({{$option->products_count}})
                                </label>
                            </div>
                        @endforeach
                    @endforeach
                    <button type="submit" class="btn btn-outline-info">Use filter</button>
                </div>
            @endunless
            <div class="col-12 col-md-8 col-lg-9">
                <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 g-3">
                    @each('products.parts.card', $products, 'product')
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12 mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
