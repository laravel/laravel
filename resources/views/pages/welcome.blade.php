@extends('main')
@section('title', ' | Homepage')
@section('content')
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron">
                    <h1>Welcome to My Blog!</h1>
                    <p class="lead">Thank you so much for visiting, This is my test website built with Laravel, Please read my popular post!</p>
                    <p><a class="btn btn-primary btn-lg" href="#" role="button">Popular Post</a></p>
                </div>                
            </div>            
        </div> <!-- end of .row -->

        <div class="row">
            <div class="col-md-8">
                <div class="post">
                    <h3>Post Title</h3>
                    <p>You can download it using the package control in Sublime Text. If you don't have Package Control downloaded, you can follow the simple instructions here</p>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>

                <hr>

                <div class="post">
                    <h3>Post Title</h3>
                    <p>You can download it using the package control in Sublime Text. If you don't have Package Control downloaded, you can follow the simple instructions here</p>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>

                <hr>

                <div class="post">
                    <h3>Post Title</h3>
                    <p>You can download it using the package control in Sublime Text. If you don't have Package Control downloaded, you can follow the simple instructions here</p>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>

                <hr>

                <div class="post">
                    <h3>Post Title</h3>
                    <p>You can download it using the package control in Sublime Text. If you don't have Package Control downloaded, you can follow the simple instructions here</p>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>
            </div>

            <div class="col-md-3 col-md-offset-1">
                <h2>Sidebar</h2>
            </div>
            
        </div>
@endsection