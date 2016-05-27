@extends('main')
@section('title', ' | Contact')
@section('content')
        <div class="row">
            <div class="col-md-8 col-md-offset-1 well">
                <h1>Contact Me</h1>
                <hr>
                <form>
                    <div class="form-group">
                        <label name="email">Email:</label>
                        <input id="email" name="email" class="form-control"></input>
                    </div>

                    <div class="form-group">
                        <label name="subject">Subject:</label>
                        <input id="subject" name="subject" class="form-control"></input>
                    </div>

                    <div class="form-group">
                        <label name="message">Message:</label>
                        <textarea id="message" name="message" class="form-control" placeholder="type message here ..." rows="9"></textarea>
                    </div>

                    <input type="submit" value="Send Message" class="btn btn-success"></input>

                </form>
            </div>
            @include('partials._sidebar')
        </div>
@endsection