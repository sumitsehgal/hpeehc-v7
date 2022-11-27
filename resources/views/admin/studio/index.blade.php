@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Studios</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.studio.create')}}" class="btn btn-primary pull-right">Add Studio</a>
                    </div>
                    
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Statistics Data</th>
                                <th cols="2" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($studios->isNotEmpty())
                                @foreach($studios as $studio)
                                <tr>
                                    <td>{{$studio->id}}</td>
                                    <td>{{$studio->name}}</td>
                                    <td>{{$studio->stats }}</td>
                                    
                                
                                    <td>
                                        <a href="{{route('admin.studio.edit', $studio->id)}}" class="btn btn-warning">Edit</a>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.studio.destroy', $studio->id) }}" method="POST" onsubmit="return confirm('Do you really want to delete the studio?')">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    {{$studios->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
