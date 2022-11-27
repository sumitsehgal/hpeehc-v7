@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Edit Studio</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.studio.index')}}" class="btn btn-primary pull-right">List Studio</a>
                    </div>
                    
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{route('admin.studio.update', $studio->id)}}" class="form-horizontal" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-2 control-label col-md-offset-2 ">Name</label>

                            <div class="col-md-6 ">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $studio->name) }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('stats') ? ' has-error' : '' }}">
                            <label for="stats" class="col-md-2 control-label col-md-offset-2 ">Statistics Data</label>

                            <div class="col-md-6 ">
                                <input id="stats" type="number" class="form-control" name="stats" required autofocus value="{{ old('stats', $studio->stats) }}">

                                @if ($errors->has('stats'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('stats') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="states" class="col-md-2 control-label col-md-offset-2">Select States</label>

                            <div class="col-md-6">
                                <select name="state[]" class="form-control" multiple>
                                    @if($states->isNotEmpty())
                                        @foreach($states as $stateid=>$state_title)
                                            <option value="{{$stateid}}" @if(in_array($stateid, $studio->realAssetsByType('state')->pluck('object_id')->toArray()) ) selected @endif  >{{$state_title}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>

                        <div class="form-group" >
                            <label for="partners" class="col-md-2 control-label col-md-offset-2">Select Partners</label>

                            <div class="col-md-6">
                                <select name="partner[]" class="form-control" multiple>
                                    @if($partners->isNotEmpty())
                                        @foreach($partners as $partnerType=>$partnerTitle)
                                            <option value="{{$partnerType}}" @if(in_array($partnerType, $studio->realAssetsByType('partner')->pluck('object_id')->toArray()) ) selected @endif  >{{$partnerTitle}}</option>
                                        @endforeach
                                    @endif
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
