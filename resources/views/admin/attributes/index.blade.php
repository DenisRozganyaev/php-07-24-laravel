@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 pt-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Options</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->id }}</td>
                            <td>{{ $attribute->name }}</td>
                            <td>{{ $attribute->options->implode('value', ' | ') }}</td>
                            <td>{{ $attribute->created_at }}</td>
                            <td>{{ $attribute->updated_at }}</td>
                            <td>
                                <form method="POST" action="{{route('admin.categories.destroy', $attribute)}}">
                                    @csrf
                                    @method('DELETE')

                                    <a href="{{ route('admin.categories.edit', $attribute) }}" class="btn btn-info"><i class="fa-regular fa-pen-to-square"></i></a>
                                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $attributes->links() }}
        </div>
    </div>
</div>
@endsection
