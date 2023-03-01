(function () {
  'use strict'

  var base_url = 'http://stock.test/backend';

  feather.replace({ 'aria-hidden': 'true' })

  $(document).ready(function() {
    $('#sales-list').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": base_url + "/sales?token=" + window.localStorage.getItem('token'),
        "drawCallback": function (haystack) { 
          var response = haystack.json;

          if(response.error_code == 401){
            window.localStorage.removeItem('token');
            window.location = '../sign-in';
          }
      },
    });

    $.ajax({
      type: "GET",
      url: base_url+'/customers?token='+window.localStorage.getItem('token'),
      cache: false,
      success: function(data)
      {
        var customers = data.data_raw;
        var select = $('#customer_id');
        $.each(customers, function(key, value) {
          select.append('<option value=' + value.id + '>' + value.name + '</option>');
        });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
          alert("Status: " + textStatus); alert("Error: " + errorThrown);
      }
    });

    //call products and put in product_id select
    $.ajax({
      type: "GET",
      url: base_url+'/products?token='+window.localStorage.getItem('token'),
      cache: false,
      success: function(data)
      {
        var products = data.data_raw;
        var select = $('#product_id');
        $.each(products, function(key, value) {
          select.append('<option value=' + value.id + '>' + value.name + '</option>');
        });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
          alert("Status: " + textStatus); alert("Error: " + errorThrown);
      }
    });

    $(document).on("click", "#sign-out", function(){
      $.ajax({
        type: "GET",
        url: base_url+'/logout?token='+window.localStorage.getItem('token'),
        cache: false,
        success: function(data)
        {
          window.localStorage.removeItem('token');
          window.location = '../sign-in';
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            alert("Status: " + textStatus); alert("Error: " + errorThrown); 
        }
      });
    });

    $("#add-sale").submit(function(e) {
      e.preventDefault(); 
  
      var form = $(this);
      var url = base_url+'/sale/add?token='+window.localStorage.getItem('token');
      console.log(form.serialize());
      $.ajax({
          type: "POST",
          url: url,
          cache: false,
          data: form.serialize(), 
          success: function(data)
          {
              console.log(data); 
              if(data.success == "true") {
                  $('#addModal').modal('hide');

                  var myAlert = document.getElementById('liveToast');
                  $('#liveToast').find('.toast-body').html(data.message);
                  var bsAlert = new bootstrap.Toast(myAlert);
                  bsAlert.show();

                  $('#sales-list').DataTable().ajax.reload();
              }
              else {
                var myAlert = document.getElementById('liveToast');
                $('#liveToast').find('.toast-body').html(data.error_message);
                var bsAlert = new bootstrap.Toast(myAlert);
                bsAlert.show();
              }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
              alert("Status: " + textStatus); alert("Error: " + errorThrown); 
          }
      });
    });

    $('#addModal').on('hidden.bs.modal', function () {
      $(this).find("input,textarea").val('').end();
    });
  } );
})()
