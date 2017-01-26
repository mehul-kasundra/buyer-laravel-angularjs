myApp.controller('updateBuyerController',function($scope,$http){
	var selectedDept='';
	$scope.deptSellersData=[];
	$scope.productsList=[];
	
	$scope.updateDepartmentClick=function(departmentValue){
		
		var tempProductArray=[];
		var uri_url=location.pathname.split("/");
		var buyerId=uri_url[uri_url.length-1];		
		var deptFlag=0;
		
		// remove department
		if($scope.deptSellersData.length > 0)
		{
			angular.forEach($scope.deptSellersData,function(deptValue){
				if(deptValue.departmentId == departmentValue)
				{					
					deptFlag=1;
					$scope.deptSellersData.splice($scope.deptSellersData.indexOf(deptValue),1);
				}
			});
		}
		
		// remove products
		if($scope.productsList.length > 0)
		{
			angular.forEach($scope.productsList,function(checkDepartmentId){
				
				if(checkDepartmentId.departmentId == departmentValue)
				{
					
				}
				else
				{
					tempProductArray.push(checkDepartmentId);
				}
			});
			$scope.productsList=tempProductArray;
		}
		
		
		
		if(deptFlag == 0)
		{
			
			var url="http://"+location.host+"/webplanex/public/buyers/getsellerForBuyer";
			var data={selectedDept:departmentValue,buyerId:buyerId};
			$http.post(url,data).success(function(output){

				if(output.success == '1')
				{
					$scope.deptSellersData.push(output.sellerData);
						
					if(output.countSellerProductArr >0)
					{
						$scope.productsList.push(output.sellerProductArr);
					} 
				}
				var d=$scope.productsList;
				var p=$scope.deptSellersData;
			});
		}
		
	}
	
	
	
	
	
	// when clicking on seller checkbox
	
	$scope.updateSellerClick=function(departmentId,sellerId){
		var productFlag=0;
		
		
		if($scope.productsList.length > 0)
		{
			// remove products
			angular.forEach($scope.productsList,function(productCategory){
				angular.forEach(productCategory.sellerDetails,function(sellerDetail){
					if(productCategory.departmentId == departmentId && sellerDetail.sellerId == sellerId)
					{						
						productFlag=1;
						
						$scope.productsList.splice($scope.productsList.indexOf(productCategory),1);
					}
				});
			});
			var t=$scope.productsList;
		}
		
		
		
		if(productFlag == 0)
		{
			// insert products.
			var url="http://"+location.host+"/webplanex/public/buyers/getproduct";				
			var data={departmentId:departmentId,sellerId:sellerId};
			$http.post(url,data).success(function(output){
				if(output.success == '1')
				{
					$scope.productsList.push(output.productNames);
				}
				var p=$scope.productsList;
			});
		}
	}	
	
	// ajax call on page load 
	var url="http://"+location.host+"/webplanex/public/buyers/getBuyerInfoPageLoad";
	var subUrl=location.pathname.split("/");
	var buyerId=subUrl[subUrl.length-1];
	$scope.buyerId=buyerId;
	var data={buyerId:buyerId};
	$http.post(url,data).success(function(output){	
		$scope.updateBuyerDetails=output.buyerDetails;
		var t=$scope.updateBuyerDetails;
	});
	
	
	// when atleast one department exists for the corresponding buyer.
	$scope.deleteSeller=function(departmentId,sellerId,buyerId)
	{
		if(!confirm('Are you sure you want to delete this seller?'))
		{
			return false;
		}
		// alert(departmentId+' '+sellerId+' '+buyerId);
		var url="http://"+location.host+"/webplanex/public/buyers/deleteSeller";
		var data={departmentId:departmentId,sellerId:sellerId,buyerId:buyerId};
		$http.post(url,data).success(function(output){
			// alert(output.success);
			if(output.success == '1')
			{				
				angular.forEach($scope.updateBuyerDetails,function(departmentValues){
					angular.forEach(departmentValues.sellerDetails,function(sellerValues,key){
						if(departmentValues.departmentId == departmentId && sellerValues.sellerId == sellerId)
						{
							// when more than one sellers exist.
							if(departmentValues.sellerDetails.length > 1)	// only remove seller cell in department row.
							{								
								departmentValues.sellerDetails.splice(key,1);								
								// remove one products row.
								angular.forEach($scope.productsList,function(deleteDepartment){
									angular.forEach(deleteDepartment.sellerDetails,function(deleteSeller,sellerDetailKey){
										if(deleteDepartment.departmentId == departmentId && deleteSeller.sellerId == sellerId)
										{
											// alert(sellerDetailKey);
											deleteDepartment.sellerDetails.splice(sellerDetailKey,1);
											// $scope.productsList.splice($scope.productsList.indexOf(deleteDepartment));
										}
									});
								}); 
							}
							// when only one seller exists, then we need to remove the whole row with its department.
							else
							{
								
								$scope.updateBuyerDetails.splice($scope.updateBuyerDetails.indexOf(departmentValues),1);
								
								// remove row from products row
								angular.forEach($scope.productsList,function(deleteDepartment){
									angular.forEach(deleteDepartment.sellerDetails,function(deleteSeller,sellerDetailKey){
										if(deleteDepartment.departmentId == departmentId && deleteSeller.sellerId == sellerId)
										{
											$scope.productsList.splice($scope.productsList.indexOf(deleteDepartment),1);
										}
									});
								});
							}
							
							
							// if buyer already selected department, then we have to unselect the seller corresponding to that department.
							if($scope.deptSellersData.length > 0)
							{								
								angular.forEach($scope.deptSellersData,function(departmentDetail){
									angular.forEach(departmentDetail.sellerDetails,function(sellerDetail){
										if(departmentDetail.departmentId == departmentId && sellerDetail.sellerId == sellerId && sellerDetail.status == 1)
										{											
											sellerDetail.status=0;
											sellerDetail.disabledStatus=0;
										}
									});
								});
							}
							
							
							$scope.updateBuyerDetails;
							// remove row from products row
							var r=$scope.productsList;
						}
					});
				});
			}
		});
	}
});
