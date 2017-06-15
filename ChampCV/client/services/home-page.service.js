define(function () {
    /**
    * responsible got handling server requests
    **/
    function HomePageService($http, $q) {

        function isError(response) {
            return (!response.data) || (response.data == "") || (response.data && response.data["error_message"]);
        }

        return {
            getTasksList: function (cid) {
                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/homePage.php',
                        data: 'action=getTasksList&cid=' + cid,
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

    return HomePageService;
});