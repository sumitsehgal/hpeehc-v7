@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Users</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.users.create')}}" class="btn btn-primary pull-right">Add User</a>
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
                                <th scope="col">Email Address</th>
                                <th scope="col">Username</th>
                                <th scope="col">Role</th>
                                <th scope="col">Map Visibility</th>
                                <th scope="col">Assets</th>
                                <th cols="2" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($users->isNotEmpty())
                                @foreach($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->username}}</td>
                                    <td>{{$user->getRoleNames()->implode(',')}}</td>
                                    <td class="text-center">@if($user->hasMapView === "1")
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg>
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
</svg>
                                    @endif</td>
                                    <td>
                                    @if($user->getRoleNames()->isNotEmpty())
                                        @foreach($user->getRoleNames() as $role)
                                           <?php $assets = $user->realAssetsByRole($role)->pluck('object_id');
                                            if($role == "site") {
                                                echo $assets->implode(", ");
                                            }else if($role == "state"){
                                                if(!empty($assets)){
                                                    $stateData = [];
                                                    foreach($assets as $asset) {
                                                        $stateData[] = isset($states[$asset]) ? $states[$asset] : "";
                                                    }
                                                    echo implode(", ", $stateData);
                                                }  
                                            }else {
                                                if(!empty($assets)){
                                                    echo $assets->implode(", ");
                                                }
                                            }

                                            ?>
                                           

                                        @endforeach
                                    @endif
                                    </td>
                                    <td>
                                        <a href="{{route('admin.users.edit', $user->id)}}" class="btn btn-warning">Edit</a>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Do you really want to delete the User?')">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
