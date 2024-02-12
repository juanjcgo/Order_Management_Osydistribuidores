(function ($) {
    class OrderClass {
        constructor($) {
            this.url = $;
        }

        gerAllOrders(info_order, successCallback, errorCallback) {
            $.ajax({
                type: "POST",
                url: `${this.url}/ord_get_orders`,
                dataType: 'json',
                data: {
                    info_order: info_order
                },
                success: function (response) {
                    successCallback(response);
                },
                error: function (xhr, status, error) {
                    errorCallback(error);
                }
            });
        }

        gerOneOrder(id_order, successCallback, errorCallback) {
            $.ajax({ 
                type: "POST",
                url: `${this.url}/ord_get_order`,
                dataType: 'json',
                data: {
                    order_id: id_order
                },
                success: function (response) {
                    successCallback(response);
                },
                error: function (xhr, status, error) {
                    errorCallback(error);
                }
            });
        }

        createNotes(dataForm, successCallback, errorCallback) {
            $.ajax({
                type: "POST",
                url: `${this.url}/ord_create_note`,
                dataType: 'json',
                data: dataForm,
                success: function (response) {
                    successCallback(response);
                },
                error: function (xhr, status, error) {
                    errorCallback(error);
                }
            });
        }

        getOrderByStatus(info_order, order_status, successCallback, errorCallback) {
            $.ajax({
                type: "POST",
                url: `${this.url}/ord_get_order_completed`,
                dataType: 'json',
                data: {
                    info_order: info_order,
                    order_status: order_status
                },
                success: function (response) {
                    console.log('response');
                    console.log(response);
                    successCallback(response);
                },
                error: function (xhr, status, error) {
                    errorCallback(error);
                }
            });
        }


        updateViewsNotes(user_id, order_id, successCallback, errorCallback) {
            $.ajax({ 
                type: "POST",
                url: `${this.url}/ord_update_view_notes`,
                dataType: 'json',
                data: {
                    order_id: order_id,
                    user_id: user_id
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

    window.OrderClass = OrderClass;
})(jQuery);
