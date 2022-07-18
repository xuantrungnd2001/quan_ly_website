@extends('layout.layout')
@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Danh sách người dùng</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <a href="{{route('user.create')}}" class="btn btn-success mb-2"><i
                                    class="mdi mdi-plus-circle mr-2"></i> Tạo tài khoản</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100"
                            id="products">
                            <thead>
                                <tr>
                                    <th>Tên người dùng</th>
                                    <th style="width: 75px;">Sửa</th>
                                    <th style="width: 75px;">Xóa</th>
                                    <th>Tên người dùng</th>
                                    <th style="width: 75px;">Sửa</th>
                                    <th style="width: 75px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $num = 0;
                                @endphp
                                @foreach ($data as $user)
                                    @php if($num % 2 === 0)
                                    echo'<tr>';
                                    @endphp
                                    
                                    <td class="table-user">
                                        <img src="{{asset('images/users/avatar.png')}}" alt="table-user"
                                            class="mr-2 rounded-circle">
                                        <a href="" 
                                            class="text-body
                                            font-weight-semibold">{{$user->name}}</a>
                                    </td>
                                    <td><a href="{{route('user.edit',$user)}}" class="Sửa-icon"> <i class="mdi mdi-square-edit-outline"></i></a></td>
                                    <td><a href="{{route('user.delete',$user)}}" class="action-icon"> <i class="mdi mdi-delete"></i></a></td>
                                    @php if($num % 2 === 1)
                                        echo'</tr>';
                                        $num++;
                                    @endphp
                                @endforeach
                            </tbody>
                            
                        </table>
                        {{ $data->links() }}
                    </div>
                    
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div>

@endsection
