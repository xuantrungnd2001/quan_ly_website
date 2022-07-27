@extends('layout.layout')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{$data[0]->owner}}-{{$data[0]->title}}</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                   

                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="thead-light">
                                <tr> 
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Http Code</th>
                                    <th>Create time</th>
                                    <th>Last Check</th>
                                    <th>ReCheck</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $url)
                                    <tr>
                                        <td>{{$url->url}} </td>
                                        
                                        @if ($url->status === 'alive')
                                        <td>
                                            <h5><span class="badge badge-success-lighten">Alive</span></h5>
                                        </td>
                                        @else
                                        <td>
                                            <h5><span class="badge badge-danger-lighten">Die</span></h5>
                                        </td>
                                        @endif
                                        <td>{{$url->http_code}}</td>
                                        <td>{{$url->created_at}}</td>
                                        <td>{{$url->last_check}}</td>
                                        <td><a href="{{route('web.recheck',$url)}}" class="action-icon"> <i class="dripicons-clockwise"></i></a></td>
                                        <td><a href="{{route('web.edit',$url)}}" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a></td>
                                        <td><a href="{{route('web.delete',$url)}}" class="action-icon"> <i class="mdi mdi-delete"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
</div>
@endsection
