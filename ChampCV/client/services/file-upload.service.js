define(['angular'], function (angular) {
    'use strict';
    
    //ImageUploadFactory.$inject = ['$http', '$q'];

    function ImageUploadFactory($http, $q) {
        var compatibility = {
                'filereader': typeof FileReader !== 'undefined',
                'dnd': 'draggable' in document.createElement('span'),
                'formdata': !!window.FormData,
                'progress': "upload" in new XMLHttpRequest()
            },
            acceptedTypes = {
                'application/pdf': true
            },
            maxFileSize = 3 * 1024 * 1024, // 3MB
            errors = {
                unSupportedBrowser: 'unSupportedBrowser',
                noFiles: 'noFiles',
                unAccepedFileType: 'unAccepedFileType',
                maxFileSizeExceed: 'maxFileSizeExceed',
                failedToUpload: 'failedToUpload'
            };

        function validate(files, config) {
            var maxSize, types;
       
            config = config || {};
            types = config.acceptedTypes || acceptedTypes;
            maxSize = config.maxFileSize || maxFileSize;

            /*if (!compatibility.formdata || !compatibility.filereader) {
                return { 'success': false, 'error': errors.unSupportedBrowser };
            }

            if (!files || files.length === 0) {
                return { 'success': false, 'error': 'noFiles' };
            }*/

            for (var i = 0, l = files.length; i < l; i++) {
                if (!types[files[i].type]) {
                    return { 'success': false, 'error_message': errors.unAccepedFileType };
                }

                if (maxSize < files[i].size) {
                    return { 'success': false, 'error_message': errors.maxFileSizeExceed };
                }
            }

            return { 'success': true, 'error_message': null };
        }

        function previewFile(files, config) {
            var filesCheck = validate(files, config),
                deferred = $q.defer();

            if (!filesCheck.success) {
                deferred.reject(filesCheck);
                return deferred.promise;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                deferred.resolve(event.target.result);
            };
            reader.readAsDataURL(files[0]);

            return deferred.promise;
        }

        function uploadFiles(files, url, params, config) {
            //var filesCheck = validate(files, config),
            var deferred = $q.defer(),
                formData;

            /*if (!filesCheck.success) {
                deferred.reject(filesCheck);
                return deferred.promise;
            }*/

            formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append('file', files[i]);
            }

            angular.forEach(params, function (value, key) {
                formData.append(key, value);
            });
            


            $http.post(url, formData, {
                headers: { 'Content-Type': undefined },
                transformRequest: angular.identity
            }).then(function (data) {
                deferred.resolve(data);
            }).error(function (data, status) {
                if (status === 413) {
                    deferred.reject({ 'success': false, 'error': errors.maxFileSizeExceed });
                    return;
                }

                deferred.reject({ 'success': false, 'error': errors.failedToUpload });
            });

            return deferred.promise;
        }

        function dataURItoBlob(dataURI) {
            var binary = atob(dataURI.split(',')[1]),
                array = [],
                mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

            for (var i = 0; i < binary.length; i++) {
                array.push(binary.charCodeAt(i));
            }

            return new Blob([new Uint8Array(array)], { type: mimeString });
        }


        return {
            acceptedTypes: acceptedTypes,
            uploadFiles: uploadFiles,
            previewFile: previewFile,
            dataURItoBlob: dataURItoBlob,
            validate: validate
        };

    }
    
    return ImageUploadFactory;
});