@extends('layouts.app')


@section('content')
<div class="container">
    <!---div class="row justify-content-center"--->
        <!--div class="col-md-8"-->

            {{------ dd( $dataTable->getColumns() , new ReflectionClass($dataTable) ) --------------}}
    {{----  Table head:<br>
    @foreach($dataTable->getColumns() as $key=>$val)
       --data {!! $val->data !!}
       --name {!! $val->name !!}
       --title {!! $val->title !!}<br>
    @endforeach   ------}}

    Example: <b>dataTables</b><br>

    Eager Loading `Multiple` Relationships. <button><a href="https://laravel.com/docs/5.6/eloquent-relationships#eager-loading" target="_blank">laravel ref:</a></button><br>
    Model: User , Table: users<br>
    Model: Exam , Table: exams | protected $with = ['user'] ;<br>


            {{$dataTable->table(['id' => 'exam','class'=>'table table-bordered' ,'cellspacing'=>'0','width'=>'100%'  ])}}

        <!---/div--->
    <!----/div--->
</div>
@endsection

@push('scripts')
    <!--suppress JSAnnotator -->
    <script>

        var editor ;

        $(function() {
            {{-----------https://yajrabox.com/docs/laravel-datatables/master/editor-usage#setup-csrf---------------------}}
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });

            editor = new $.fn.dataTable.Editor({

                ajax: '{!! route('Exameditor') !!}',     {{--------- Route::POST --------}}
                legacyAjax: false,
                table: "#exam",
                idSrc: "id",
                display: "bootstrap",
                fields: [
            {{------{label: "user_id", name: "user_id" },
                    {label: "name", name: "user.name"  },
                    {label: "email", name: "user.email"  },
                    {label: "header", name: "header"},
                    {label: "text", name: "text" , className: 'block' ,type: "textarea" } -------------}}
                    {!! $table_editor !!}
                ]
            });

            {{------- https://editor.datatables.net/manual/php/events ---------}}
            editor.on( 'open', function ( e , type , action  ) {
                console.log('Event: on open '   );

                console.log('Editor type: ' + type + ' form shown' + ' | action: ' + action);
                xxx = '';
                if(action =='create') {
                    // Type is 'main', 'bubble' or 'inline'
                    //console.log('Editor fields: ' + editor.fields() + ' | Fields Is Array: ' + Array.isArray(editor.fields()));

                    editor.fields().forEach(function(el) {
                        console.log(el);
                        if(el == 'user.id'){
                            {{------  var fid = editor.field('user.id');
                            if (fid ) {
                                fid
                                    .def('no id entered')
                                    .val('{!! Auth::user()->id !!}');        // add this user id
                            }  -------}}
                            editor.field('user.id').set('{!! Auth::user()->id !!}');
                        }else if(el == 'user.name'){
                            editor.field('user.name').set('{!! Auth::user()->name !!}');
                        }else if(el == 'user.email'){
                            editor.field('user.email').set('{!! Auth::user()->email !!}');
                        }else if(el == 'user_id'){
                            editor.field('user_id').set('{!! Auth::user()->id !!}');
                        }

                    });

                }
                if(action == 'edit'){
                    // fields are filled in
                }
                if (action == 'remove') {
                    // fields are empty

                }

                // If any error was reported, cancel the submission so it can be corrected

                if ( this.inError() ) {
                    console.log(xxx);
                    delete xxx ;
                    return false;

                }else if ( xxx ) {
                    console.log(xxx );
                    //alert(xxx);
                    delete xxx ;
                }else{

                }

            } );

            {{--tbody td button------delete button on last row ------see columnDefs-------https://editor.datatables.net/reference/type/button-options-----------------------}}
            {{-------https://stackoverflow.com/questions/14460421/get-the-contents-of-a-table-row-with-a-button-click----------}}
            $('#exam').on( 'click', 'tbody td button', function () {

                var $row = $(this).closest("tr"),        // Finds the closest row <tr>
                    $tds = $row.find("td");             // Finds the  <td> element

                /*******
                         //console.log(' 2e: ' + $row.find("td:nth-child(2)").text());  // Finds the 2nd <td> element

                         for (i = 0; i < $tds.length; i++) {
                        console.log( $row.find("td:nth-child("+ i + ")").text());
                    }

                         console.log('-----------------------------------------------------');
                 ****/

                var getdata = editor
                    .title( 'Delete row' )
                    .buttons( 'Confirm delete' )
                    .message( 'Are you sure you want to remove this row?' )
                    .remove( $tds );

                /***
                             x=0;
                             getdata.fields().forEach(function(el , x) {
                            console.log(el  + ': ' + getdata.modifier()[x].firstChild.data);
                            x++;
                        })
                 console.log(getdata);
                 ****/

            } );


            {{--- https://editor.datatables.net/reference/event/initSubmit Event , immediately prior to the data being submitted to the server-----}}
            editor.on( 'initSubmit', function ( e, action    ) {
                console.log('Event: initSubmit:  modify  form\'s values  immediately prior to the data being submitted to the server.'   );

            });

            editor.on( 'preSubmit', function (e, json, data   ) {

                console.log('Event: preSubmit , cancellable');
                console.log(json);
                console.log(data);
                xxx = '';

                {{--------  if(data == 'create') {
                    var fid = editor.field('user_id');
                    var fname = editor.field('user.name');
                    var ftext = editor.field('text');
                    var fheader = editor.field('header');

                    console.log('input text: ' + json.data[0].text + ' | length: ' + json.data[0].text.length);
                    console.log('input header: ' + json.data[0].header + ' | length: ' +  json.data[0].header.length);

                    if (fid.val() != '{!! auth::user()->id !!}' || fname.val() != '{!! auth::user()->name !!}') {
                        //xxx = '{!! auth::user()->id !!}' +  ' , does not own this record !! \n rightful owner user id: ' + fid.val();
                        xxx = '<span style="color:red;">{!! auth::user()->name !!}' + ' , does not own this record !! </span><br><br><b> ' + fname.val() + ' is the owner of this record !!</b>';
                        fname.error('!! Can`t Update , you don`t own this record !!'); // if you don`t use sweetalert2

                        setTimeout(function () {
                            editor.close();
                        }, 2000);
                    }

                    if( json.data[0].text.length < '1' ) {
                        //ftext.error(' Must be between 1 and 20000 characters long !!');
                        x = 'Text field too few characters !!';

                    }
                    if(  json.data[0].text.length > '20000') {
                        //ftext.error(' Must be between 1 and 20000 characters long !!');
                        x = 'Text field too much characters !!';

                    }
                    if( json.data[0].header.length < '1' ) {
                        //fheader.error('Must be between 1 and 60 characters long !!');
                        y = 'Header field too few characters !!';

                    }
                    if(  json.data[0].header.length > '60') {
                        //fheader.error('Must be between 1 and 60 characters long !!');
                        y = 'Header field too much characters !!';

                    }


                    if(typeof x != 'undefined' && typeof y != 'undefined'){
                        editor.error( x + ' <br> ' + y );
                    }else if(typeof y != 'undefined'){
                        editor.error( y );
                    }else if(typeof x != 'undefined'){
                        editor.error( x );
                    }else{
                        console.log('Submit');
                        //this.submit();
                        swal({
                            position: 'top-end',
                            type: 'success',
                            title: 'Saving your work !',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }

                    /*else{
                        swal({
                            position: 'top-end',
                            type: 'success',
                            title: 'Saving your work !',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }*/

                } ------}}

                if(data == 'remove') {

                    correctUser = true;
                    Object.keys(json.data).forEach(function (el) {
                        console.log('correct owner user.id: ' + json.data[el].user['id']);
                        if (json.data[el].user['id'] != '{{ Auth()->User()->id }}') {
                            correctUser = false;
                        }
                    });

                    console.log(' true/false correctUser:' + correctUser);
                    //console.log(typeof (correctUser));

                    if(correctUser === true){
                        swal({
                            position: 'top-end',
                            type: 'success',
                            title: 'Deleting your data !',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }else if(correctUser === false){
                        editor.error( 'An error has occurred' );
                        swal({
                            position: 'top-end',
                            type: 'error',
                            title: 'You are not the rightful owner of this data !',
                            showConfirmButton: false,
                            timer: 5000
                        })
                        editor.close();
                    }else{

                    }

                }

                if (data == 'edit') {

                    if(typeof correctUser == 'undefined') {
                        correctUser = true;
                        console.log(correctUser + ' <-- SET correctUser');
                    }

                    if(correctUser === true) { // next loops if editor multi edit

                        Object.keys(json.data).forEach(function (el) {
                            //console.log('user.id: ' + json.data[el].user['id']);

                    if(typeof json.data[el].user != 'undefined') {

                        if (typeof json.data[el].user['id'] != 'undefined') {
                            if (json.data[el].user['id'] != '{{ Auth()->User()->id }}') {
                                correctUser = false;
                                console.log(correctUser + ' <-- correctUser fails on: user.id');
                                return false;  //break loop
                            }
                        }
                        if (typeof json.data[el].user['name'] != 'undefined' ) {
                            if ( json.data[el].user['name'] != '{{ Auth()->User()->name }}') {
                                correctUser = false;
                                console.log(correctUser + ' <-- correctUser fails on: user.name');
                                return false;  //break loop
                            }
                        }
                        if (typeof json.data[el].user['email'] != 'undefined' ) {
                            if ( json.data[el].user['email'] != '{{ Auth()->User()->email }}') {
                                correctUser = false;
                                console.log(correctUser + ' <-- correctUser fails on: user.email');
                                return false;  //break loop
                            }
                        }

                    }else{
                        if (typeof json.data[el]['user_id'] != 'undefined') {
                            if (json.data[el]['user_id'] != '{{ Auth()->User()->id }}') {

                                correctUser = false;
                                console.log(correctUser + ' <-- correctUser fails on: user_id');
                                return false;  //break loop
                            }

                        }else{
                            correctUser = false;
                            console.log(correctUser + ' <-- correctUser fails on: every check');
                            return false;  //break loop
                        }

                        //correctUser = false;
                        //return false;
                    }

                        });

                    }else{
                        console.log(correctUser + ' <-- correctUser must be false');
                    } // end loops editor multi edit

                        if (typeof correctUser !== 'undefined') {
                            console.log(correctUser + ' <-- correctUser');
                        }

                        if (correctUser === false) {
                            editor.error('An error has occurred');
                            xxx = 'wrong user !';
                            swal({
                                position: 'top-end',
                                type: 'error',
                                title: 'You are not the rightful owner of this data !',
                                showConfirmButton: false,
                                timer: 5000
                            });
                            editor.close();
                        } else {
                            swal({
                                position: 'top-end',
                                type: 'success',
                                title: 'Your data will be updated !',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            xxx = 'Your data will be updated !';
                        }

                } // end of edit

                if ( this.inError() ) {
                    console.log(xxx);
                    //alert(xxx);
                    delete xxx ;delete x;delete y;delete correctUser;
                    return false;

                }else if ( xxx ) {
                    console.log(xxx );
                    //alert(xxx);
                    delete xxx ;delete x;delete y;
                }else{

                }
            });



            {{------------- after ExamDataTablesEditor editRules() Validation-----------------------------------------------------}}
            editor.on( 'preEdit', function (e, json, data   )   {
                console.log('Event: preEdit , Works as mutator  , not cancellable' );
                //console.log('first table id: ' + data.id + ' | auth user ,  user.id: ' + data.user.id);

            });


            /*editor
                .disable( 'user_id' )
                .disable( 'user.name' );*/
           {!! $editor_disable !!}



               $table = $('#exam').DataTable({
                "processing": true,
                "info": true,
                "serverSide": true,
                'iDisplayLength': 10,
                "lengthMenu": [[1,5,10,15,20, -1], [1,5,10,15,20, "All"]],
                "pagingType": "full_numbers",
                dom: "pBfrltiBp",
               "columnDefs": [
                   { className: "mje_class", "targets": [ 1 , 0 ] },
                   { data: null ,"defaultContent": "<button  class='btn btn-danger btn-sm'>Single <br> Remove</button>" , "targets": [{{ count( $dataTable->getColumns() ) - 1  }}]}
                ],

                ajax: '{!! route('getExamAjax') !!}',
                columns: [
                    {!! $table_collumns !!}
                ],
                order: [ 0, 'asc' ] ,
                select: true ,
                buttons: {
                    name: 'primary',
                    buttons: [
                        'copy', 'csv', 'pdf', 'colvis',
                        { extend: "create", editor: editor },
                        { extend: "edit"   ,  editor: editor },
                        { extend: "remove",  text: 'Multi Remove' , editor: editor },
                        {
                            extend: "print",
                            messageTop: "Made by MJE"
                        }
                    ]
                }
            });


        });

        {{-----   $.fn.dataTable.ext.errMode = 'throw';   ----}}

    </script>
@endpush