define(function () {
    /**
    * responsible for handling server requests
    **/
    function LoginService($http, $q) {

        function isError(response) {
            return (!response.data) || (response.data == "") || (response.data && response.data["error_message"]);
        }

        return {
            /*
                ask the server to login. 
            */
            login: function (username, password) {
                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/login.php',
                        data: 'action=login&username='+username+'&password='+password,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).then(function successCallback(response) {
                        if (isError(response)) {
                            reject(response);
                            return;
                        }
                        resolve(response);

                    }, function errorCallback(response) {
                        reject(response);
                    });
                });
            }
                    
         }
       
    }

    return LoginService;
});