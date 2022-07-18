@extends('layout.layout')
@section('content')


<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Thay đổi thông tin người dùng</h4>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-xl-8 col-lg">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content align-self-center">
                        <div class="tab-pane active" id="settings">
                            <form action="{{route('user.update',$user)}}" method="POST">
                                @csrf
                                @method('PUT')
                                <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle mr-1"></i> Personal Info</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">   
                                        <div class="form-group">
                                            <label>Tên người dùng</label>
                                            <input name="name" type="text" class="form-control" value="{{$user->name}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">   
                                        <div class="form-group">
                                            <label>Mật khẩu cũ</label>
                                            <input name="old_password" type="password" class="form-control" placeholder="Mật khẩu">
                                        </div>
                                    </div>

                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mật khẩu mới</label>
                                            <input name="password" type="password" class="form-control" placeholder="Mật khẩu">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Xác nhận mật khẩu</label>
                                            <input name="password_confirmation" type="password" class="form-control"
                                                placeholder="Xác nhận mật khẩu">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button name="update" type="submit" class="btn btn-success mt-2"><i class="mdi mdi-content-save"></i>
                                        Lưu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection