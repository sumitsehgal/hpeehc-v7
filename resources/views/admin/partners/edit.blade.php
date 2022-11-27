@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Edit Partner</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.partners.index')}}" class="btn btn-primary pull-right">List Partners</a>
                    </div>
                    
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{route('admin.partners.update', $partner->id)}}" class="form-horizontal" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-2 control-label col-md-offset-2 ">Name</label>

                            <div class="col-md-6 ">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $partner->name) }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group" >
                            <label for="show-on-app" class="col-md-2 control-label col-md-offset-2">Show this on App</label>

                            <div class="col-md-6">
                                <select name="show_on_app" id="show-on-app" class="form-control">
                                    <option value="0" @if(old('show_on_app', $partner->show_on_app) == 0 ) selected @endif >No</option>
                                    <option value="1"  @if(old('show_on_app', $partner->show_on_app) == 1 ) selected @endif  >Yes</option>
                                </select>
                                
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Update
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
