@extends('layouts.app');

@section('content')
    <h1>Edit Post</h1>
    {!! Form::open(['action' => ['PostsController@update', $post->id],'method'=> 'POST','enctype'=> 'multipart/form-data']) !!}
    <div class="form-group">
        {{Form::label('title','Title')}}
        {{Form::text('title',$post->title,['class' => 'form-control','placeholder'=> 'Title'])}}
    </div>

    <div class="form-group">
        {{Form::label('body','Body')}}
        {{Form::textarea('body',$post->body,['id'=>'article-ckeditor','class' => 'form-control','placeholder'=> 'Body'])}}
    </div>
    <div class="form-group">
            <!--because of the file we have to add above enctype -->
            {{Form::file('cover_image')}}
        </div>
    {{Form::hidden('_method','PUT')}} <!-- This is used to be abl to use put because we can only use either POST OR GET but in update we need PUT -->
    {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
   {!! Form::close() !!}
   
    
@endsection