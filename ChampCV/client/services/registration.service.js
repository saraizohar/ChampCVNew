define(function () {
    /**
    * responsible got handling server requests
    **/
    function LoginService($http, $q, fileUploadService) {

        function isError(response) {
            return (!response.data) || (response.data == "") || (response.data && response.data["error_message"]);
        }

        return {
            register: function (registrationData, file) {
                var deferred = $q.defer(),
                    formData = new FormData();

                formData.append('file', file);
                formData.append('data', JSON.stringify(registrationData));
                formData.append('action', 'register');

                $http.post('./server/clientAPI/registration.php', formData, {
                    headers: { 'Content-Type': undefined },
                    transformRequest: angular.identity
                }).then(function (response) {
                    if (isError(response)) {
                        deferred.reject(response);
                        return;
                    }
                    deferred.resolve(response);
                }).catch(function (response, status) {
                    if (status === 413) {
                        deferred.reject();
                        return;
                    }

                    deferred.reject();
                });

                return deferred.promise;
                
            }
                    
         }
       
    }

    return LoginService;
});