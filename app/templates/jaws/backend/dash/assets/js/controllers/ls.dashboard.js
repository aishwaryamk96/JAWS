'use strict';

/*  Start JA-113 : LS Dashboard */

angular.module('jaws')
    .controller('CtrlLsDashboard',['$scope','$http', function($scope,$http) {
       
      $scope.leadStatus='0';
      $scope.leadTable='basic';
      $scope.leadList="1";   
      $scope.allResponse = ''; 
      $scope.leadStatusArr = [];
      $scope.leadcount = "";

       $scope.loadLsDashboard = function(leadStatus, leadTable, leadList=''){

         var param = {
                        params:{
                        "leadStatus": leadStatus,
                        "leadTable": leadTable,
                        "leadList":leadList
                      }
                    }  
           $http.get(_JAWS_PATH_API + 'lsDashboard', param).then(function (response) {
                
                $scope.allResponse  = response.data.data.list;
                $scope.leadcount = response.data.data.count;
                console.log($scope.allResponse);
            });
       }
      


     $scope.leadSelectTable = function (){
        
        if($scope.leadTable == 'basic'){
            $scope.leadStatus='0';
            $scope.leadStatusArr = [{'id':'0','name':'New Data'},{'id':'1','name':'Process'},{'id':'9','name':'Old Data'}];

         }else{
            $scope.leadStatus='1';
            $scope.leadStatusArr = [{'id':'1','name':'New'},{'id':'2','name':'API requeste'},{'id':'3','name':'Success'},{'id':'4','name':'Failure'},{'id':'9','name':'Old Data'}];
         }
         
         $scope.loadLsDashboard($scope.leadStatus,$scope.leadTable,$scope.leadList); 
     }

     
     $scope.selectLeadStatus = function (){

        $scope.loadLsDashboard($scope.leadStatus,$scope.leadTable,$scope.leadList);
     }

     $scope.selectLeadList = function(){
      
        $scope.loadLsDashboard($scope.leadStatus,$scope.leadTable,$scope.leadList);
    }

     if($scope.leadTable == 'basic'){
        $scope.leadStatusArr = [{'id':'0','name':'New Data'},{'id':'1','name':'Process'},{'id':'9','name':'Old Data'}];
     }else{
        $scope.leadStatusArr = [{'id':'1','name':'New'},{'id':'2','name':'API requeste'},{'id':'3','name':'Success'},{'id':'4','name':'Failure'},{'id':'9','name':'Old Data'}];
     }
      
     $scope.loadLsDashboard($scope.leadStatus,$scope.leadTable,$scope.leadList);


    }]);
   /*  End JA-113 : LS Dashboard */