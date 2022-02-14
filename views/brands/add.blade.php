@extends('admin/layout/common')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Add {{ $modelTitle}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/admin/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/admin/$viewName') }}">{{$modelTitle}}</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!--column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>              
                </div>
                <!-- /.box-header -->
                <!-- form start -->    
                {!! Form::open(array("url" => "admin/$viewName/create", "method" => "POST", "role" => "form", 'files' => true, 'id' => 'submitFrm', )) !!}	
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Brand Name</label>                  
                            {!! Form::input('text', 'name', null, ['class' => 'form-control textRequired','placeholder'=>'Brand Name']) !!}
                            <div class="error-message">{{$errors->first('name')}}</div>
                        </div> 
                        <div class="form-group">
                            <label for="">Brand Description</label>                  
                            {!! Form::textarea('description', null, ['id' => 'editor1', 'class' => 'form-control','placeholder'=>'Description','rows'=>7]) !!}
                            <div class="error-message">{{$errors->first('description')}}</div>
                        </div>
                        <div class="row upload-img-row">
                            <div class="col-md-4 text-center">
                                <label for="exampleInputFile">Brand Icon</label>
                                <div class="form-group">
                                    <div class="browse-this">
                                        <div id="catIconPreview"></div>
                                        {!!Form::file('icon', ['class' => 'category_icon'])!!}
                                    </div>
                                    <div class="error-message">{{$errors->first('icon')}}</div>
                                    <span class="message"> Icon Height should be of 70px.</span>
                                </div> 
                            </div>
                        </div> <!--/row-->
                        <div class="checkbox bike-category">
                            <label >
                                {!! Form::checkbox('status', '1', null) !!}Status Active
                            </label>
                        </div>
                        <div class="form-group" id="select_cat_div">
                            <label for="">Select Catgeories</label>
                            {!! Form::select('categories_id[]', $categories, '', [ 'class' => 'form-control categories','multiple'=>'multiple']) !!}
                            <div class="error-message">{{$errors->first('categories_id')}}</div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button> 
                        @if($attributesSelected)
                        <a style="margin-left: 5px;" class="btn btn-default btn-close" href='{!! url("admin/attributes/$attributesSelected->id"); !!}'>Cancel</a>
                        @else
                        <a style="margin-left: 5px;" class="btn btn-default btn-close" href="{!! url('admin/attributes'); !!}">Cancel</a>
                        @endif
                    </div>
                {!! Form::close() !!}
            </div>
            <!-- /.box -->
        </div>
        <!--/.col -->        
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->	

@stop

@section('scripts')
<script>
    setTimeout(function () {
        //Initialize select2
        $(".categories").select2();
    }, 20);
</script>
@stop
