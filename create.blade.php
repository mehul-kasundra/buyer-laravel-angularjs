@extends('layout.template')

@section('content')

<script src="{{ url('js/createBuyerController.js') }}"></script>
<link href="{{ asset('css/customStyle.css') }}" rel="stylesheet">
    <h1>Create Buyer</h1>
	@if($errors->any())
		<div class="alert alert-danger">
			@foreach($errors->all() as $error)			
				<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif
	
	@if(Session::has('msg'))
		<div class="alert alert-danger">
		{{Session::get('msg')}}
		</div>
	@endif
	
    {!! Form::open(['url' => 'buyers', 'method' => 'POST','id'=>'buyerFormSubmission','ng-controller'=>'buyerCreateFormController']) !!}
    <div class="form-group">
		{!! Form::label('Name', 'Name:') !!}
		{!! Form::text('name',null,['class'=>'form-control','ng-click'=>'addFunc()']) !!}
	</div>
	<div class="form-group">
        {!! Form::label('Email', 'Email:') !!}
		{!! Form::email('email',null,['class'=>'form-control']) !!}
	</div>
	<div class="form-group">
		{!! Form::label('Password', 'Password:') !!}
		{!! Form::password('password',['class'=>'form-control']) !!}
	</div>
	
	<div class="form-group">
		{!! Form::label('DEPARTMENT', 'DEPARTMENT:') !!}
	<div>		
	
		@foreach($departmentsList as $index=>$singleDepartment)
		<?php
	$unchecked=$singleDepartment["id"];
	?>
			<label class="checkbox-inline">{!! Form::checkbox('dept_id', $singleDepartment['id'], NULL, ['class' => 'dept_id','ng-click'=>"deptClick($unchecked)"]) !!}	{{  $singleDepartment['department_name'] }}</label>
			
		@endforeach
		</div>
	</div>
	


	<div class="form-group">
		{!! Form::label('SELLER', 'Add New Seller:') !!}
								<div ng-if="deptSellerList.length == 0">			
								No seller is available.
								</div>			
								<div class="deptSellerArea" ng-if="deptSellerList.length > 0" ng-repeat="dept in deptSellerList">				
									<div>				
										<label  class="spaceStyle"><% dept.departmentName %> :</label>				
										<span class="spaceStyle" ng-repeat="seller in dept.sellerDetails">				
										<label class='checkbox-inline'><input type='checkbox' name='seller_id[]' ng-value='dept.departmentId+" "+seller.sellerId' ng-click='sellerClick(dept.departmentId , seller.sellerId )'/> <% seller.sellerName %></label>
										</span>				
									</div>			
								</div>				
	</div>
	
	<div class="form-group">		
		{!! Form::label('PRODUCT', 'Products:') !!}
		<div id="productIds">		
		<div ng-if="ProductList.length == 0">
		No product is available.		
		</div>
			<div class="productDetails" ng-if="ProductList.length > 0" ng-repeat="sellerProduct in ProductList">				
			
			<label><% sellerProduct.departmentName %></label><span ng-repeat="seller in sellerProduct.sellerDetails"> <label  class="spaceStyle"><% seller.sellerName %> :</label>
			<label class='checkbox-inline' ng-repeat="products in seller.productNames">
			<% products %>
			</label>
			</span>
			
			</div>
		</div>
	</div>
    
    <div id="app_url" style="display:none"><?php echo asset('/');?></div>
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="form-group">
        {!! Form::submit('Save', ['class' => 'btn btn-primary form-control','id'=>'buyerSubmit']) !!}
    </div>
    {!! Form::close() !!}
	<script>
$( 'document' ).ready(function() {
  
  // remove selected index of department by clicking on submit
  $("#buyerFormSubmission").on('submit',function(){
	  $(".dept_id").attr('name','dept_id[]');
	  $(".seller_id").attr('name','seller_id[]');
  });
		
});

</script>
@stop
