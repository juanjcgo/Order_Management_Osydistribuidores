(function ($) {
  $(document).ready(function () {
    console.log(ord.rest_url);

    $.ajax({
      type: "POST",
      url: `${ord.rest_url}/ord_get_orders`,
      data: true,
      success: function (response) {
        console.log(response);
        table = $("#ord_data_orders").DataTable({
          data: response.data,
          columns: [
            { data: "count" },
            { data: "order_title" },
            { data: "order_price" },
            { data: "order_commission" },
            { data: "order_status" },
            { data: "order_date_created" },
            {
              defaultContent: ord_get_options(),
            },
          ],
        });
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      },
    });

    /*
     *Options of manage
     */
    function ord_get_options() {
      return `<td scope="col">
        <button type="button" class="btn ord-btn-edit-offer" data-bs-toggle="modal" data-bs-target="#ord_modal_details">
          <i class="bi bi-back ord-details-order"> Detalle </i>
        </button>
        </td>`;
    }


    $("#ord_data_orders tbody").on("click", "button.ord-btn-edit-offer", function () {
      var table = $("#ord_data_orders").DataTable();
      var data_order = table.row($(this).parents("tr")).data();

      console.log(data_order.order_id);

      if(data_order.order_id){

          $.ajax({
            type: "POST",
            url: `${ord.rest_url}/ord_get_order`,
            data: {
              order_id: data_order.order_id
            },
            success: function (response) {
              console.log(response.data);
              $('#ord_id_order').text(' #' + response.data.order_id);
              $('#ord_date').text(response.data.order_date_created);
              $('#ord_status').text(response.data.order_status);
              $('#ord_commission').text(response.data.order_commission);
              $('#order_price').text(response.data.order_price);
              $('#order_payment_amount').text(response.data.order_price);

              $.each(response.data.products, function(index, row) {
                // Crear una nueva fila de la tabla
                var newRow = $('<tr>');
          
                // Agregar las celdas de la fila con los datos correspondientes
                newRow.append($('<td>').text(row.prod_name));
                newRow.append($('<td>').text(row.prod_quantity));
                newRow.append($('<td>').text(row.prod_total));
          
                // Agregar la nueva fila a la tabla
                $('#ord_products tbody').append(newRow);
              });

            },
            error: function (jqXHR, textStatus, errorThrown) {
              console.log(textStatus, errorThrown);
            },
          });
  
      }

      /* $("#stu_main_nivel_user").val(data_user.id_level);
      $("#stu_status_user").val(data_user.id_user_status);
      $("#id_user_edit").val(data_user.id);
      $("#stu_open_edit_label").text(data_user.first_name); */
    });



  });


})(jQuery);
