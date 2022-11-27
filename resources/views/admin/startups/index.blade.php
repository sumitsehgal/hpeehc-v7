@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Startups</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.startups.create')}}" class="btn btn-primary pull-right">Add Startup</a>
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
                                <th scope="col">Partner Name</th>
                                <th cols="2" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($startups->isNotEmpty())
                                @foreach($startups as $startup)
                                <tr>
                                    <td>{{$startup->id}}</td>
                                    <td>{{$startup->name}}</td>
                                    <td>{{$startup->partner->name }}</td>
                                    
                                
                                    <td>
                                        <a href="{{route('admin.startups.edit', $startup->id)}}" class="btn btn-warning">Edit</a>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.startups.destroy', $startup->id) }}" method="POST" onsubmit="return confirm('Do you really want to delete the startup?')">
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
                    {{$startups->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
