angular.module('jaws')
	.service('apiSVC', ['$http', function($http) {

		this.get = function (func) {
			return $http.get(_JAWS_PATH_API + func).then(function (response) {
      				return response.data; 
      			});
		};

}]);