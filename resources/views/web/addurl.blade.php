@extends('layout.layout')
@section('content')
<div class="container-fluid">
    <div class="row">
    
        <div class="col-12">
            <div class="page-title-box">

                <h4 class="page-title">Thêm vào danh sách: {{$title}}</h4>
            </div>
            
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-md">   
                            <form action="{{route('web.addurl',$title)}}" name="form1" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="url">Nhập Url</label>
                                <input name="url" type="text" id="listname"
                                    class="form-control" value= >
                            </div>
                            <button type="submit" class="btn btn-success mt-2" name="tasks"><i
                                    class="mdi mdi-content-save"></i>
                                Lưu
                            </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection