@extends('layout.template')

@section('content')
<script src="{{ url('js/updateBuyerController.js') }}"></script>
<link href="{{ asset('css/customStyle.css') }}" rel="stylesheet">
    <h1>Update Buyer</h1>
	@if($errors->any())
		<div class="alert alert-danger">
			@foreach($errors->all() as $error)			
				<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif
	
	@if(Session::has('success'))
		<div class="alert alert-success">
		{{Session::get('success')}}
		</div>
	@endif	
	
	@if(Session::has('errorMessage'))
		<div class="alert alert-danger">
		{{Session::get('errorMessage')}}
		</div>
	@endif	
	
    {!! Form::open(['url' => 'buyers/updateBuyerData', 'method' => 'POST','id'=>'buyerFormSubmission','ng-controller'=>'updateBuyerController']) !!}
    <div class="form-group">
		{!! Form::label('Name', 'Name:') !!}
		{!! Form::text('name',$userDetails[0]['attributes']['name'],['class'=>'form-control']) !!}
	</div>
	<div class="form-group">
        {!! Form::label('Email', 'Email:') !!}
		{!! Form::email('email',$userDetails[0]['attributes']['email'],['class'=>'form-control']) !!}
	</div>
	<div class="form-group">
		{!! Form::label('Password', 'Password:') !!}		
		{!! Form::input('password', 'password',$userDetails[0]['attributes']['decrypt_password'],['class'=>'form-control']) !!}
	</div>
	
		{!! Form::hidden('buyerId',$buyerId,['id'=>'buyerId']) !!}
		<?php
		if($countStatus == 1):
			?>
		<table class="table table-striped table-bordered table-hover">
	<tr>
		<thead>
			<th>#</th>
			<th>Departments</th>
			<th>Sellers</th>
		</thead>
	</tr>
	<tr ng-if="updateBuyerDetails.length == 0"><td colspan="3">No Department Exist.</td></tr>
	<tr ng-repeat="singleBuyer in updateBuyerDetails" ng-if="updateBuyerDetails.length > 0">
		<td><% singleBuyer.index %></td>
		<td><% singleBuyer.departmentName %></td>
		<td><div class="sellerTotal" ng-repeat="singleSeller in singleBuyer.sellerDetails" ng-if="singleBuyer.sellerDetails.length > 0"><span><% singleSeller.sellerName %></span><a href="javascript:void(0)" ng-click="deleteSeller(singleBuyer.departmentId,singleSeller.sellerId,buyerId)" class="btn btn-danger deleteButton">X</a></div></td>
	</tr>
	
	</table>
		@endif
		
	<div class="form-group">	
		{!! Form::label('DEPARTMENT', 'Add New Department::') !!}
		<div>
		@foreach($departmentsList as $singleDepartment)
		<?php
		$checkedDept=$singleDepartment["id"];
		?>
			<label class="checkbox-inline">{!! Form::checkbox('dept_id[]',$singleDepartment['id'], NULL, ['class' => 'dept_id','ng-click'=>"updateDepartmentClick($checkedDept)" ]) !!}	{{  $singleDepartment['department_name'] }}</label>
			
		@endforeach
		</div>
	</div>
		
		
		
	
	<div class="form-group">	
		{!! Form::label('SELLER', 'Add New Seller:') !!}
		<div id="sellerIds"> 
			<div class="sellerDetails" ng-if="deptSellersData.length == 0">No seller is available.</div>
			<div class="sellerDetails" ng-if="deptSellersData.length > 0" ng-repeat="singleDeptSeller in deptSellersData">
				<label class="spaceStyle"><% singleDeptSeller.departmentName %> :</label>
				<span class="spaceStyle" ng-if="singleDeptSeller.sellerDetails.length > 0" ng-repeat="singleSellerDetail in singleDeptSeller.sellerDetails">
				<label class="checkbox-inline">{!! Form::checkbox('seller_id[]','1',NULL,['class'=>'seller_id','ng-click'=>"updateSellerClick(singleDeptSeller.departmentId,singleSellerDetail.sellerId)", 'ng-checked'=>"singleSellerDetail.status","ng-value"=>"singleDeptSeller.departmentId+' '+singleSellerDetail.sellerId", "ng-disabled"=>"singleSellerDetail.disabledStatus"])  !!} <% singleSellerDetail.sellerName %></label>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group">		
		{!! Form::label('PRODUCT', 'Products:') !!}
		<div id="productIds">
			<div class="productDetails" ng-if="productsList.length == 0" >No product is available.</div>
			<div class="productDetails" ng-if="productsList.length > 0" ng-repeat="deptSellerDetails in productsList">
				<div ng-repeat="productList in deptSellerDetails.sellerDetails"><label  class="spaceStyle"><% deptSellerDetails.departmentName %> </label> <label  class="spaceStyle"><% productList.sellerName %> :</label>
					<span   class="spaceStyle" ng-if="productList.productNames.length > 0" ng-repeat="productName in productList.productNames">
						<% productName %>
					</span>
				</div>
				
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
  
  
// function to delete seller department on behalf of buyer id.
function deleteSeller(buyerId, departmentId, sellerId, sellerCellId,departmentRowId)
{	
	var buyerId=buyerId;
	var departmentId=departmentId;
	var sellerId=sellerId;
	var baseUrl=$("#app_url").text();
	var url=baseUrl+'buyers/deleteSeller';
	$.ajax({
			 headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
			url: url,
			
			data: {buyerId:buyerId, departmentId:departmentId, sellerId:sellerId},
			type: 'POST',
			datatype: 'JSON',			
			success: function (resp)
			{				
				var response=JSON.parse(resp);
				
				if(response.success == 1)
				{
					jQuery("#sellerCell_"+sellerCellId).remove();
					jQuery(".dept_id").attr('checked',false);
					$("#sellerIds .sellerDetails").empty();
					$("#productIds .productDetails").empty();					
					$("#sellerIds .sellerDetails").append("No seller is available.");
					$("#productIds .productDetails").append("No product is available.");
					
					if(jQuery("#departmentRowId_"+departmentRowId+" .sellerTotal").length == 0)
					{
						jQuery("#departmentRowId_"+departmentRowId).remove();
					}					
				}
				else
				{
					alert('something goes wrong.');
				}
				return false;
			}
		});
	
	return false;
}

</script>
@stop
