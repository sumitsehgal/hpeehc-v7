@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Partners</h4>
                    <div class="btn-group pull-right">
                        <a href="{{route('admin.partners.refresh')}}" class="btn btn-primary pull-right">Refresh</a>
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
                                <th scope="col">Show on App</th>
                                <th scope="col">Total Startups</th>
                                <th cols="2" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($partners->isNotEmpty())
                                @foreach($partners as $partner)
                                <tr>
                                    <td>{{$partner->id}}</td>
                                    <td>{{$partner->name}}</td>
                                    <td>{{($partner->show_on_app == 1) ? "Yes" : "No" }}</td>
                                    <td><a href="{{route('admin.startups.index', ['partner_id'=>$partner->id])}}" >{{$partner->startups_count}}</a></td>
                                
                                    <td>
                                        <a href="{{route('admin.partners.edit', $partner->id)}}" class="btn btn-warning">Edit</a>
                                    </td>

                                    <td>
                                        <a href="{{route('admin.startups.create',['partner_id'=>$partner->id])}}" class="btn btn-primary">Add Startup</a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    {{$partners->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
