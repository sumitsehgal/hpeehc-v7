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
                                    <td>
                                    @if($user->getRoleNames()->isNotEmpty())
                                        @foreach($user->getRoleNames() as $role)
                                           <?php $assets = $user->realAssetsByRole($role)->pluck('object_id');
                                            if($role == "site") {
                                                echo $assets->implode(", ");
                                            }else {
                                                if(!empty($assets)){
                                                    $stateData = [];
                                                    foreach($assets as $asset) {
                                                        $stateData[] = isset($states[$asset]) ? $states[$asset] : "";
                                                    }
                                                    echo implode(", ", $stateData);
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
