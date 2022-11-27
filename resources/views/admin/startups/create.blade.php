@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Add Startup</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.startups.index')}}" class="btn btn-primary pull-right">List Startup</a>
                    </div>
                    
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{route('admin.startups.store')}}" class="form-horizontal" method="POST">
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

                        <div class="form-group">
                            <label for="partners" class="col-md-2 control-label col-md-offset-2">Select Partner</label>

                            <div class="col-md-6">
                                <select name="partner_id" class="form-control">
                                    @if($partners->isNotEmpty())
                                        @foreach($partners as $partnerid=>$partnername)
                                            <option value="{{$partnerid}}" @if($partner_id == $partnerid) selected @endif >{{$partnername}}</option>
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
