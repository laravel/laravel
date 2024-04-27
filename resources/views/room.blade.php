
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Room</title>
</head>
<body>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="{{url('admin/room')}}" class="float-right btn btn-success btn-sm">View All</a>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" >
                    <tr>
                        <th>Title</th>
                        <td{{ $data->id }}>{{ $data->title }}</td> 
                    </tr>
                    {{-- <tr>
                        <th>Roomtype</th>
                        <td> <option @if($data->room_type_id==$rt->id) selected @endif value="{{$rt->id}}">{{$rt->title}}</option>
                        </td>
                    </tr>
                     --}}
                    <tr>
                        <th>description	</th>
                        <td>{{($data->description) }}</td>
                </table>
            </div>
        </div>
    </div>
</body>
</html>