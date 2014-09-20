var stadiumApp = angular.module('stadiumApp', []);

stadiumApp.controller('MainCtrl', ['$scope', '$http',
    function($scope, $http) {

    FB.init({
        appId: '740878865980424',
        status: true,
        xfbml: true,
        version: 'v2.0'
    });

    FB.getLoginStatus(function(response){
        console.log(response);
        if (response.status == 'connected') {
            // DONT SHOW FB BUTTON
        } else {
            // SHOW FB BUTTON
        }
    });

    $scope.login = function () {
        FB.login(function(response) {
            console.log(response);
            location.href = "homepage.html";
        });
    }

    $scope.openNavMenu = function () {
        $scope.nav = !$scope.nav;
    }

    $scope.getProfilePicture = function () {
        return "https://graph.facebook.com/" + fbID + "/picture";
    }

}]);