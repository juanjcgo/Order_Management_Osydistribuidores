(function ($) {
  $(document).ready(function () {

    getAllOrdersRender();

    function getAllOrdersRender() {
      var info_order = $('#ord_data_orders').data('info-order');
      $('#ord_data_orders').append('<span class="loader"></span>');
      $('#ord_data_orders').css('opacity', '0.5');

      var orderAPI = new OrderClass(ord.rest_url);
      orderAPI.gerAllOrders(
        info_order,
        function (orders) {
          console.log(orders);

          setTimeout(function () {
            $('.loader').remove();
            $('#ord_data_orders').css('opacity', 'initial');
          }, 1500);

          if (orders.res) {
            var units = $("#ord_data_orders").DataTable();
            units.destroy();

            calculateCommissionByState(orders.data);

            $("#ord_data_orders").DataTable({
              language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar pedidos:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                  "sFirst": "Primero",
                  "sLast": "Último",
                  "sNext": "Siguiente",
                  "sPrevious": "Anterior"
                },
                "oAria": {
                  "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                  "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
              },
              data: orders.data,
              columns: [
                { data: "count" },
                { data: "customer_user" },
                { data: "order_total" },
                { data: "order_commission" },
                { data: "order_status_translate" },
                { data: "customer_phone" },
                { data: "order_date_created" },
                {
                  data: 'number_note_views',
                  defaultContent: '',
                  render: function (data, type, row) {
                    if (type === 'display') {
                      return ord_get_options(data);
                    }
                    return data;
                  }
                },
              ],
            });
          } else {
            console.log(orders.msg);
          }

        },
        function (error) {
          $('.loader').remove();
          $('#ord_data_orders').css('opacity', 'initial');
          console.log(error);
        }
      );
    }

    function calculateCommissionByState(orders) {
      var profit_paid = 0;
      var profit_completed = 0;
      var profit_processing = 0;
      var profit_delivered = 0;

      $.each(orders, function (index, row) {
        if (row.order_status == "paid") {
          profit_paid += row.order_commission;
        }
        if (row.order_status == "completed") {
          profit_completed += row.order_commission;
        }
        if (row.order_status == "processing") {
          profit_processing += row.order_commission;
        }
        if (row.order_status == "delivered") {
          profit_delivered += row.order_commission;
        }
      });

      $('.ord-profit-paid').text(profit_paid.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
      $('.ord-profit-delivered').text(profit_delivered.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
      $('.ord-profit-completed').text(profit_completed.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
      $('.ord-profit-processing').text(profit_processing.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
    }

    /*
     *Options of manage
     */
    function ord_get_options(data) {
      $badge = '';
      if (data > 0) {
        $badge = `
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
            ${data}
            <span class="visually-hidden">unread messages</span>
          </span>`;
      }
      return `<td scope="col">
        <button type="button" class="btn ord-btn-edit-offer position-relative" data-bs-toggle="modal" data-bs-target="#ord_modal_details">
          <i class="bi bi-back ord-details-order"> Detalle </i>
          ${$badge}
        </button>
        </td>`;
    }


    $("#ord_data_orders tbody").on("click", "button.ord-btn-edit-offer", function () {
      var table = $("#ord_data_orders").DataTable();
      var data_order = table.row($(this).parents("tr")).data();

      $('#ord_products tbody').empty();
      $('#id_order_note').val(data_order.order_id);
      $('.ord-box-chats').html(' ');
      $('#up-content-list-widgets').css('display', 'none');

      if (data_order.order_id) {
        $('#tab1').append('<span class="loader"></span>');
        $('#tab1').css('opacity', '0.5');

        var orderAPI = new OrderClass(ord.rest_url);
        orderAPI.gerOneOrder(
          data_order.order_id,
          function (response) {
            console.log(response);
            $('.loader').remove();
            $('#tab1').css('opacity', 'initial');

            if (response.res) {
              /* Details Orders */

              $('#ord_id_order').text(response.data.order_id);
              $('#ord_date').text(response.data.order_date_created);
              $('#ord_status').text(response.data.order_status_translate);
              $('#ord_commission').text(response.data.order_commission.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
              $('#shipping_total').text(response.data.shipping_total.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
              $('#order_total_tax').text(response.data.order_total_tax.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
              $('#order_subtotal').text(response.data.order_subtotal.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
              $('#order_total').text(response.data.order_total.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));

              /* Ordes Products */

              $.each(response.data.products, function (index, row) {
                var newRow = $('<tr>');
                newRow.append($('<td>').text(row.prod_name));
                newRow.append($('<td>').text(row.prod_quantity));
                newRow.append($('<td>').text(row.prod_total));
                $('#ord_products tbody').append(newRow);
              });

              /* Details Customer */
              $('#ord_name_customer').text(response.data.billing.first_name);
              $('#ord_lastname_customer').text(response.data.billing.last_name);
              $('#ord_email_customer').text(response.data.billing.email);
              $('#ord_phone_customer').text(response.data.billing.phone);
              $('#ord_address1_customer').text(response.data.billing.address_1);
              $('#ord_address2_customer').text(response.data.billing.address_2);
              $('#ord_city_customer').text(response.data.billing.city);
              $('#ord_company_customer').text(response.data.billing.company);
              $('#ord_zipcode_customer').text(response.data.billing.postcode);
              $('#ord_country_customer').text(response.data.billing.country);

              renderNotes(response.data.notes);
            } else {
              console.log(response.msg);
            }

          },
          function (error) {
            $('.loader').remove();
            $('#tab1').css('opacity', 'initial');
            console.log(error);
          }
        );

      }
    });

    $('.ord-close-btn').click(function () {
      $('#ord_form_note').css('display', 'none');
      $('.ord-dismiss-modal').css('display', 'block');
      $('#up-content-list-widgets').css('display', 'block');
    });

    $('.ord-note-form').click(function () {
      $('.ord-dismiss-modal').css('display', 'none');
      $('#ord_form_note').css('display', 'flex');
    });

    /**************************** Create Note *****************************/
    $("#ord_form_note").submit(function (event) {
      event.preventDefault();
      if ($('textarea[name="ord_note"]').val().length > 0) {
        $('.ord_lists_box').append(`<span class="loader"></span>`);
        $('.ord_lists_box').css('opacity', '0.5');

        var orderAPI = new OrderClass(ord.rest_url);
        orderAPI.createNotes(
          $(this).serialize(),
          function (notes) {  console.log(notes);
            $('.loader').remove();
            $('.ord_lists_box').css('opacity', 'initial');
            $('.ord-box-chats').html(' ');
            $('textarea[name="ord_note"]').val('');
            renderNotes(notes);
          },
          function (error) {
            console.log(error);
          }
        );
      }
    });

    /***************************** Filter Orders By Status *****************************/
    $(".ord-order-status").click(function (event) {
      event.preventDefault();

      $('#ord_data_orders').append('<span class="loader"></span>');

      var orderAPI = new OrderClass(ord.rest_url);
      orderAPI.getOrderByStatus(
        $('#ord_data_orders').data('info-order'),
        $(this).data('status-order'),
        function (order) {
          $('.loader').remove();
          var table = $("#ord_data_orders").DataTable();
          if (order.res) {
            table.clear();
            table.rows.add(order.data);
            table.draw();
          } else {
            table.clear();
            table.rows.add([]);
            table.draw();
          }
        },
        function (error) {
          $('.loader').remove();
          console.log(error);
        }
      );
    });

    /***************************** Render Notes *****************************/
    function renderNotes(notes) {
      $.each(notes, function (index, row) {
        classView = '';
        if (row.view == "0") {
          classView = 'none_view'
        }

        if (row.customer_note && row.added_by == "system") {
          $('.ord-box-chats').append(`
            <div class="advisory-message">
              <div class="encabezado">
                  <h2>Mi nota</h2>
              </div>
              <div class="cuerpo">
                  <p>${row.content}</p>
              </div>
              <div class="pie">
                  <span>${row.date_created.date}</span>
              </div>
            </div>
            `);
        }

        if (row.customer_note && row.added_by != "system") {
          $('.ord-box-chats').append(`
          <div class="system-message ${classView}">
            <div class="encabezado">
                <h2>Administrador</h2>
            </div>
            <div class="cuerpo">
                <p>${row.content}</p>
            </div>
            <div class="pie">
                <span>${row.date_created.date}</span>
            </div>
          </div>
          `);
        }
        if (!row.customer_note && row.added_by == "system") {
          $('.ord-box-chats').append(`
          <div class="system-message ${classView}">
            <div class="encabezado">
                <h2>Sistema</h2>
            </div>
            <div class="cuerpo">
                <p>${row.content}</p>
            </div>
            <div class="pie">
                <span>${row.date_created.date}</span>
            </div>
          </div>
          `);
        }
      });
    }

    /***************************** Filter by order status *****************************/
    $('#ord_filter_mov').change(function () {

      $('#ord_data_orders').append('<span class="loader"></span>');

      var orderAPI = new OrderClass(ord.rest_url);
      orderAPI.getOrderByStatus(
        $('#ord_data_orders').data('info-order'),
        $('#ord_filter_mov').val(),
        function (order) {
          $('.loader').remove();
          var table = $("#ord_data_orders").DataTable();
          if (order.res) {
            table.clear();
            table.rows.add(order.data);
            table.draw();
          } else {
            table.clear();
            table.rows.add([]);
            table.draw();
          }
        },
        function (error) {
          $('.loader').remove();
          console.log(error);
        }
      );
    });


    /****************************** Update View Note *******************************/
    $('#ord_view').click(function () {
      var orderAPI = new OrderClass(ord.rest_url);
      orderAPI.updateViewsNotes(
        $('#ord_data_orders').data('info-order'),
        $('#ord_id_order').text(),
        function (order) {
          console.log(order);
          getAllOrdersRender();
        },
        function (error) {
          console.log(error);
        }
      );
    });

  });


})(jQuery);
