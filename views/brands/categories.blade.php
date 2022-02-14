@extends('admin/layout/common')
@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Categories/Subcategories
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/admin/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>

    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">

                @if (Session::has('message')) 
                <p id="alertmsg" class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                @endif

                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">


                    <table class="table table-hover table-bordered">
                        <tr>

                            <th>ID</th>
                            <th>Icon</th>
                            <th>BG Image</th>
                            <th>Category Name</th>
                            <th>Status</th>
                            <th>Community part</th>                  
                            <th>Add Date</th>                  

                        </tr>


                        @foreach($result as $key=>$val)
                        <tr>

                            <td>{{ $val->id }}</td>
                            <td>
                                @if($val->icon != '')
                                    {{ Html::image(asset('/upload_images/categories/icon/'.$val->id.'/'.$val->icon), 'alt', array( 'width' => 40, 'height' => 40 )) }}
                                @endif
                            </td>
                            <td>
                                @if($val->image != '')
                                    {{ Html::image(asset('/upload_images/categories/backgroundimage/30px/'.$val->id.'/'.$val->image), 'alt', array( 'width' => 40, 'height' => 40 )) }} 
                                @endif
                            </td>
                            <td>{{ @ucfirst($val->name) }}</td>              
                            <td>
                                <span class="label label-<?php if ($val->status == 0) { ?>danger<?php } else { ?>success<?php } ?>">
                                    @if($val->status==0) Inactive @else Active @endif
                                </span>
                            </td>
                            <td>{!! ($val->belong_to_community == 1)? '<i class="icon fa fa-check"></i>' : '<i class="icon fa fa-ban"></i>'  !!} </td>
                            <td>{{@date('d-M-Y',strtotime($val->created_at))}}</td>
                        </tr>   
                        @endforeach

                        @if(sizeof($result)==0)
                            <tr><td colspan="9" style="text-align:center">no record found</td></tr>
                        @endif
                    </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <ul class="pagination pagination-sm no-margin pull-right">
                        <?php /* {!! $result->render() !!} */ ?>
                    </ul>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->	  
@stop
