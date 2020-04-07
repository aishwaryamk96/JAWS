'use strict';
/*  Start JA-113 : LS Dashboard */
angular.module('jaws')
    .controller('CtrlLsDashboard',['$scope','$http', function($scope,$http) {
      $scope.leadStatus='4';
      $scope.leadTable='compiled';
      $scope.leadList="1";   
      $scope.allResponse = ''; 
      $scope.leadStatusArr = [];
      $scope.leadCount = "";
      $scope.leadListArr = [{"id":"0","name":"Count"},{"id":"1","name":"List"},{"id":"2","name":"Both"}];     
      $scope.leadBasicStatusArr = [{'id':'0','name':'New Data'},{'id':'1','name':'Process'},{'id':'9','name':'Old Data'}];
      $scope.leadCompiledStatusArr = [{'id':'1','name':'New'},{'id':'2','name':'API requeste'},{'id':'3','name':'Success'},{'id':'4','name':'Failure'},{'id':'9','name':'Old Data'}];
      $scope.pageHeader = [{"id":0,"name":"Lead ID"},{"id":0,"name":"Lead Name"},{"id":0,"name":"Lead Email"},{"id":0,"name":"Lead Phone"},{"id":0,"name":"Lead Date"}];
      $scope.leadStatusArr = $scope.leadCompiledStatusArr;
      $scope.loadLsDashboard = function(){
         var param = {  
                        params:{
                        "leadStatus": $scope.leadStatus,
                        "leadTable": $scope.leadTable,
                        "leadList":$scope.leadList
                      }
                    };  
           $http.get(_JAWS_PATH_API + 'lsdashboard', param).then(function (response) {
                $scope.allResponse  = response.data.data.list;
                $scope.leadCount = response.data.data.count;
            });
       }
     $scope.leadSelectTable = function (){
         $scope.leadStatus='4';
         $scope.leadStatusArr = $scope.leadCompiledStatusArr;
       if($scope.leadTable == 'basic'){
         $scope.leadStatus='0';
         $scope.leadStatusArr = $scope.leadBasicStatusArr;
        }           
         $scope.loadLsDashboard(); 
     }
     $scope.selectLeadStatus = function (){
        $scope.loadLsDashboard();
     }
     $scope.loadLsDashboard();
    }]);
   /*  End JA-113 : LS Dashboard */