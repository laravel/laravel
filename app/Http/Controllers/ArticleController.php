<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    function list(Request $request)
    {
        $articles = $request->session()->get('articles') ?? [];
        return view('article.list', [
            'articles' => $articles
        ]);
    }
    function create(Request $request)
    {
        if($request->isMethod('post')){
            $request->session()->push('articles',[
                'title'=>$request->title,
                'content'=>$request->content,
                'date' => date('Y-m-d H:i:s')
            ]);
            return redirect()->route('article.list');
        }
        return view('article.form');
    }

}
