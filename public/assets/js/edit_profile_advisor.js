(function ($) {

    $(document).ready(function () {
        AuthenticateTokenUser(getDataUser);

        function AuthenticateTokenUser(successCallback) {
            var user_id = $('#ord_edit_profile').data('info-advisor');
            var Api = new UserClass(ord.rest_url);
            Api.authenticateUser(
                user_id,
                function (token) {
                    if (token) {
                        successCallback(ord.rest_url, token, user_id);
                    }
                },
                function (error) {
                    console.log(error);
                }
            );
        }

        function getDataUser(rest_url, token, user_id) {
            var Api = new UserClass(rest_url);
            Api.getDataUser(
                user_id,
                token,
                function (response) {
                    if (response.res) {
                        $.each(response.data, function (key, value) {
                            $(`#${key}`).val(value);
                        });
                    } else {
                        console.log(response.msg);
                    }
                },
                function (error) {
                    console.log(error);
                }
            );
        }

        function setDataUser(rest_url, token, user_id) {
            var Api = new UserClass(rest_url);
            Api.setDataUser(
                user_id,
                token,
                $('#ord_edit_profile').serialize(),
                function (response) {
                    console.log(response);
                    $('.loader').remove();
                },
                function (error) {
                    console.log(error);
                }
            );
        }

        $("#ord_edit_profile").submit(function (event) {
            event.preventDefault();
            $('#ord_edit_profile').append('<span class="loader"></span>');
            AuthenticateTokenUser(setDataUser);
        });



    });

})(jQuery);
