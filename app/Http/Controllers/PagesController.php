<?php

namespace App\Http\Controllers;

use App\Post;

class PagesController extends Controller {

	public function getIndex() {
		/*$posts = Post::orderBy('created_at', 'desc')->limit(4)->get();*/
		$posts = Post::orderBy('created_at', 'desc')->paginate(5);
        return view('pages.welcome')->withPosts($posts);
	}

	public function getAbout() {
		$first = 'Ali';
		$last = 'Mirrajei';
		$fullname = $first . " " . $last;
		$email = 'ali.mirrajei@gmail.com';
		$data = [];
		$data['fullname'] = $fullname;
		$data['email'] = $email;
		return view('pages.about')->withData($data);
	}

	public function getContact () {
		return view('pages.contact');
	}


}