(function ($) {
    class UserClass {
        constructor($) {
            this.url = $;
        }

        getDataUser(user_id, token, successCallback, errorCallback) {
            $.ajax({
                type: "POST",
                url: `${this.url}/ord_get_data_user`,
                dataType: 'json',
                data: {
                    user_id: user_id
                },
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function (response) {
                    successCallback(response);
                },
                error: function (xhr, status, error) {
                    errorCallback(error);
                }
            });
        }

        authenticateUser(user_id, successCallback, errorCallback) {
            $.ajax({
                type: "POST",
                url: `${this.url}/ord_get_token`,
                dataType: 'json',
                data: {
                    user_id: user_id
                },
                success: function (response) {
                    if (response.res) {
                        successCallback(response.token);
                    }
                },
                error: function (xhr, status, error) {
                    errorCallback(error);
                }
            });
        }

        setDataUser(user_id, token, dataForm, successCallback, errorCallback) {
            $.ajax({
                type: "POST",
                url: `${this.url}/ord_set_data_user`,
                dataType: 'json',
                data: {
                    user_id: user_id,
                    dataForm: dataForm
                },
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function (response) {
                    successCallback(response);
                },
                error: function (xhr, status, error) {
                    errorCallback(error);
                }
            });
        }
    }

    window.UserClass = UserClass;
})(jQuery);
