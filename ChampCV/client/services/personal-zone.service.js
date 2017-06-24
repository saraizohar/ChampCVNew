define(function () {
    /**
    * responsible for handling server requests
    **/
    function personalZone($http, $q) {

        function isError(response) {
            return (!response.data) || (response.data == "") || (response.data && response.data["error_message"]);
        }

        return {
            /*
                get statistics from server
            */
            getAnalyze: function (cid) {
                return $q(function (resolve, reject) {
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/analyze.php',
                        data: 'action=getAnalyze&cid=' + cid,
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
            },
            /*
                send the server a report
            */
            report: function (cid, id, isAnswer) {
                return $q(function (resolve, reject) {
                    var action = isAnswer ? 'reportAnswer' : 'reportComment';
                    $http({
                        method: 'POST',
                        url: './server/clientAPI/analyze.php',
                        data: 'action=' + action + '&cid=' + cid + '&id=' + id,
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

    return personalZone;
});