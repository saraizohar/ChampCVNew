define(['angular'], function (angular) {

    function LoginCtrl(loginService, $rootScope) {
        this.loginService = loginService;
        this.$rootScope = $rootScope;
        this.isShowLogin = false;
        this.errorText = '';
        this.isError = false;
        this.isLoading = false;
    }

    LoginCtrl.prototype = {
        submit: function () {
            var errorMsg;

            this.errorText = '';
            this.isError = false;
            this.isLoading = true;

            if (this.username && this.password) {
                this.loginService.login(this.username, this.password).then(function (response) {
                    this.$rootScope.$emit("login:success", response.data);
                }.bind(this)).catch(function (result) {
                    // handle error
                    this.isError = true;
                    errorMsg = result.data.error_message;
                    switch (errorMsg) {
                        case "incorrect password":
                            this.errorText = 'The username and password does not match';
                            break;
                        case "username doesn't exist":
                            this.errorText = 'The username does not exist';
                            break;
                        case "user blocked":
                            this.errorText = 'Your account has been blocked. Please contact our customer support.';
                            break;
                        default:
                            this.errorText = 'An error has occured. Please try again later.';

                    }
                }.bind(this)).finally(function () {
                    this.isLoading = false;
                }.bind(this));
            }
        },
        goToLogin: function () {
            this.isShowLogin = true;
            Materialize.fadeInImage('#index-banner');
        },
        signUpHandler: function () {
            this.$rootScope.$emit("registration:clicked");
        }
    }

    return {
        templateUrl: 'client/components/login/login.view.html',
        controller: ['loginService','$rootScope',  LoginCtrl]
    }
});