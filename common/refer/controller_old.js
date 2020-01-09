
var referApp = angular.module("referApp", []);
referApp.controller("referController", function($scope, $http) {
			$scope.referrals = referrer;

			$scope.voucher_notification =function(referrer_name, referrer_email, referrer_id,src, referral_id){
				//alert(referrer_name);
				var dataObj = {
				name : referrer_name,
				email : referrer_email,
				referrer_id : referrer_id,
				src: src,
				referred_id: referral_id
				};

				//var headers= { headers: 'Content-Type: application/x-www-form-urlencoded' };
				var config = { headers:  {
				        'Content-Type': 'application/x-www-form-urlencoded',
				    }
				};
				var res = $http({
					url: "https://www.jigsawacademy.com/jaws/lmsapi/jlc.voucher.notification",
					method: "POST",
					data: dataObj,
					headers:  {  'Content-Type': 'application/x-www-form-urlencoded' }
				});
				res.success(function(data, status, headers, config) {
					$scope.message = data;
					alert("Notification Sent");
					//console.log(data);
				});
				res.error(function(data, status, headers, config) {
					alert( "failure message: " + JSON.stringify({data: data}));
				});
			};
			
});

referApp.directive('ngConfirmClick', [
        function(){
            return {
                link: function (scope, element, attr) {
                    var msg = attr.ngConfirmClick || "Are you sure?";
                    var clickAction = attr.confirmedClick;
                    element.bind('click',function (event) {
                        if ( window.confirm(msg) ) {
                            scope.$eval(clickAction)
                        }
                    });
                }
            };
    }])
