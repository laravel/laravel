@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Update {{$data->title}}
                                <a href="{{url('admin/roomtype')}}" class="float-right btn btn-success btn-sm">View All</a>
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(Session::has('success'))
                            <p class="text-success">{{session('success')}}</p>
                            @endif
                            <div class="table-responsive">
                                <form enctype="multipart/form-data" method="post" action="{{url('admin/roomtype/'.$data->id)}}">
                                    @csrf
                                    @method('put')
                                    <table class="table table-bordered" >
                                        <tr>
                                            <th>Title</th>
                                            <td><input value="{{$data->title}}" name="title" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Price</th>
                                            <td><input value="{{$data->price}}" name="price" type="number" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Detail</th>
                                            <td><textarea name="detail" class="form-control">{{$data->detail}}</textarea></td>
                                        </tr>
                                        <tr>
                                            
                                                <th> Image<span class="text-danger">*</span></th>
                                                <td>
                                                    <input name="img_src" type="file" />
                                                    <input type="hidden" name="prev_photo" value="{{$data->img_src}}" />
                                                    <img width="100" src="{{asset('storage/app/'.$data->img_src)}}" />
                                                </td>
                                            </tr>
        </table>
          
                                            
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <input type="submit" class="btn btn-primary" />
                                            </td> 
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(".delete-image").on('click',function(){
            var _img_id=$(this).attr('data-image-id');
            var _vm=$(this);
            $.ajax({
                url:"{{url('admin/roomtypeimage/delete')}}/"+_img_id,
                dataType:'json',
                beforeSend:function(){
                    _vm.addClass('disabled');
                },
                success:function(res){
                    if(res.bool==true){
                        $(".imgcol"+_img_id).remove();
                    }
                    _vm.removeClass('disabled');
                }
            });
        });
    });
</script>
@endsection

@endsection