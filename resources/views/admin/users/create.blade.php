@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Add User</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.users.index')}}" class="btn btn-primary pull-right">List Users</a>
                    </div>
                    
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{route('admin.users.store')}}" class="form-horizontal" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-2 control-label col-md-offset-2 ">Name</label>

                            <div class="col-md-6 ">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-2 control-label col-md-offset-2">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-2 control-label col-md-offset-2">Username</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-2 control-label col-md-offset-2">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-2 control-label col-md-offset-2">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>


                        <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                            <label for="role" class="col-md-2 control-label col-md-offset-2">Select Role</label>

                            <div class="col-md-6">
                                <select name="role" class="form-control" id="role">
                                    <option value="admin">Admin</option>
                                    <option value="state">State</option>
                                    <option value="site">Site</option>
                                </select>

                                @if ($errors->has('role'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('role') }}</strong>
                                    </span>
                                @endif
                                
                            </div>
                        </div>

                        <div class="form-group role-assets" id="state-asset" style="display: none;">
                            <label for="states" class="col-md-2 control-label col-md-offset-2">Select States</label>

                            <div class="col-md-6">
                                <select name="state[]" class="form-control" multiple>
                                    @if($states->isNotEmpty())
                                        @foreach($states as $stateid=>$state_title)
                                            <option value="{{$stateid}}">{{$state_title}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>

                        <div class="form-group role-assets" id="site-asset" style="display: none;">
                            <label for="sites" class="col-md-2 control-label col-md-offset-2">Select Sites</label>

                            <div class="col-md-6">
                                <select name="sites[]" class="form-control" multiple>
                                    @if($sites->isNotEmpty())
                                        @foreach($sites as $siteid=>$site_title)
                                            <option value="{{$siteid}}">{{$site_title}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>



                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>


                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
