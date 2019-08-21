@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 ">
            <div class="panel panel-default">
                <div class="panel-heading">Панель Управления</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
         <div class="col-md-4 ">
            <div class="panel panel-default">
                <div class="panel-heading">Панель Управления</div>

                <div class="panel-body">
                   

                    You are logged in!
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Работа Дизелей</div>

                <div class="panel-body">
                
                <form class="form-inline" action="/">
                
                <label class="radio-inline"><input type="radio" name="optradio" checked>ADR16.5</label>
                
                <label class="radio-inline"><input type="radio" name="optradio">SD6000E</label>
                 <br />
                 <br />
   
        <div >
            <div class="form-group">
                <div class='input-group date' id='datetimepicker2'>
                    <input type='text' class="form-control" placeholder="старт" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
             <script type="text/javascript">
            $(function () {
                $('#datetimepicker2').datetimepicker({
                    locale: 'ru',
                     format: 'HH:mm'
                });
            });
        </script>
        
   </div>
   <br />
    <div >
            <div class="form-group">
                <div class='input-group date' id='datetimepicker3'>
                    <input type='text' class="form-control" placeholder="стоп"/>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
             <script type="text/javascript">
            $(function () {
                $('#datetimepicker3').datetimepicker({
                    locale: 'ru',
                     format: 'HH:mm'
                });
            });
        </script>
        
   </div>
  
   <br />
    <br />
  <button type="submit" class="btn btn-default">В журнал!</button>
</form>
                   

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
