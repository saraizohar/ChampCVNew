define(['angular'], function (angular) {

    function RegistrationCtrl($rootScope, $scope, registrationService, fileUploadService, fieldsService) {
        var ctrl = this;
        this.$rootScope = $rootScope;
        this.registrationService = registrationService;
        this.fileUploadService = fileUploadService;
        this.fieldsService = fieldsService;

        this._initFields();

        this.$scope = $scope;

        this.isRecruiter = 'false';
        this.isSendDetails = false;
        this.isFileChosen = false;
        this.passwordMatch = true;
        this.isError = false;
    }

    RegistrationCtrl.prototype = {
        fileChosenHandler: function (event) {
            var ctrl = this.$parent.$ctrl;
            ctrl.$rootScope.$evalAsync(function () {
                var file = event.target.files[0];
                if (file) {
                    ctrl.fileChosen = file;
                    ctrl.isFileChosen = true;
                }
                else {
                    ctrl.fileChosen = undefined;
                    ctrl.isFileChosen = false;
                }
            });
            
        },
        submit: function () {
            var userObj, errorMsg;
            if (this._validate()) {
                
                // send data to server
                var data = {
                    isRecruiter: this.isRecruiter === 'true',
                    firstName: this.firstName,
                    lastName: this.lastName,
                    username: this.username,
                    email: this.email,
                    phoneNumber: this.phoneNumber,
                    city: this.setVal(this.city),
                    companyName: this.setVal(this.companyName),
                    password: this.password,
                    isFileChosen: this.isFileChosen,
                    isSendContactDetails: this.isSendDetails ? 1 : 0,
                    openQuestion: this.setVal(this.openQuestion)
                }

                data.fieldsToGrade = {};
                if(this.isFileChosen){
                    data.fieldsInResume = {};
                }
                // add field to grade and fields in resume
                this.fields.forEach(function (field) {
                    data.fieldsToGrade[field.id] = field.model.grade ? 1 : 0;
                    this.isFileChosen && (data.fieldsInResume[field.id] = field.model.resume ? 1 : 0);
                }.bind(this));

              
                this.registrationService.register(data, this.fileChosen).then(function (response) {
                    userObj = response.data.user;
                    this.$rootScope.$emit("registration:success", response.data);
                }.bind(this)).catch(function (error) {
                    errorMsg = error.data.error_message;
                    this.isError = true;
                    switch (errorMsg) {
                        case 'username already exists in the DB':
                            this.errorText = 'User name is already exists'
                            break;
                        default:
                            this.errorText = 'An error has occured. Please try again later.'
                            break;
                    }


                }.bind(this));

               
                
            }
        },
        _validate: function () {
            this.isError = false;
            var isOneChecked = false,
                isChecked,
                fileValidate;

            // check that the password is confirmed
            if (this.password !== this.confirmPassword) {
                this.passwordMatch = false;
                this.isError = true;
                this.errorText = "Passwords Does not Match!"
                return false;
            }

            // check if the user checked at least one field

            this.fields.forEach(function (field) {
                isChecked = !!field.model.grade;
                isOneChecked = isOneChecked || isChecked;
            });

            if (!isOneChecked) {
                this.isError = true;
                this.errorText = "You must choose at least one field"
                return false;
            }

            // Check that the user has entered all details
            if (!(this.firstName &&
                this.lastName &&
                this.username &&
                this.email &&
                this.phoneNumber &&
                this.password &&
                this.confirmPassword &&
                this.phoneNumber &&
                ((!this.isRecruiter && this.city) || (this.isRecruiter)))) {
                this.isError = true;
                this.isEmptyFields = true;
                this.errorText = "You must enter all detailes"
                return false;
            }

            if (this.isFileChosen) {
                // Check the at least one field was chosen
                isOneChecked = false;

                this.fields.forEach(function (field) {
                    isChecked = !!field.model.resume;
                    isOneChecked = isOneChecked || isChecked;
                });

                if (!isOneChecked) {
                    this.isError = true;
                    this.errorText = "You must choose at least one field your resume contains"
                    return false;
                }

                fileValidate = this.fileUploadService.validate([this.fileChosen]);
                if (!fileValidate.success) {
                    switch (fileValidate.error_message) {
                        case 'unAccepedFileType':
                            this.isError = true;
                            this.errorText = "The resume file's type is not supported. Please upload only a PDF file"
                            break;
                        case 'maxFileSizeExceed':
                            this.isError = true;
                            this.errorText = "The resume file's size exceed the maximum size which is 3MB"
                            break;
                    }
                    return;
                }
            }
            return true;
        },
        setVal: function (val) {
            return val ? val : null;
        },
        _initFields: function (result) {
            this.fields = this.fieldsService.getFieldsList();
            this.fields.forEach(function (field) {
                field.model = {};
                field.model['grade'] = false;
                field.model['resume'] = false;   
            });
        },
    }

    return {
        templateUrl: 'client/components/registration/registration.view.html',
        controller: ['$rootScope', '$scope', 'registrationService', 'fileUploadService', 'fieldsService', RegistrationCtrl]
    }
});