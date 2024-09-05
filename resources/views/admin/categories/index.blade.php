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
                        <th>Parent</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->parent?->name ?? '-' }}</td>
                            <td>{{ $category->created_at }}</td>
                            <td>{{ $category->updated_at }}</td>
                            <td>
                                <form method="POST" action="{{route('admin.categories.destroy', $category)}}">
                                    @csrf
                                    @method('DELETE')

                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-info"><i class="fa-regular fa-pen-to-square"></i></a>
                                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
