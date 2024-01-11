function suggestion() {
    $("#sug_input").on("input", function () {
      var formData = {
        product_name: $(this).val(),
      };
  
      if (formData["product_name"].length >= 1) {
        $.ajax({
          type: "POST",
          url: "../php/ajax.php",
          data: formData,
          dataType: "json",
          encode: true,
        }).done(function (data) {
          updateDatalist(data);
        });
      } else {
        clearDatalist();
      }
    });
  }
  
  function updateDatalist(data) {
    var datalist = document.getElementById("search-results");
  
    if (datalist) {
      clearDatalist();
  
      var tempContainer = document.createElement("div");
      tempContainer.innerHTML = data;
  
      var listItems = tempContainer.querySelectorAll("li");
  
      listItems.forEach(function (item) {
        var option = document.createElement("option");
        option.value = item.textContent || item.innerText;
        datalist.appendChild(option);
      });
    } else {
      console.error("No se pudo encontrar el elemento con id 'search-results'");
    }
  }
  
  function clearDatalist() {
    var datalist = document.getElementById("search-results");
    if (datalist) {
      datalist.innerHTML = "";
    } else {
      console.error("No se pudo encontrar el elemento con id 'search-results'");
    }
  }
  
  $("#sug-form").submit(function (e) {
    var formData = {
      p_name: $("input[name=title]").val(),
    };
  
    $.ajax({
      type: "POST",
      url: "../php/ajax.php",
      data: formData,
      dataType: "json",
      encode: true,
    })
      .done(function (data) {
        $("#product_info").html(data).show();
      })
      .fail(function () {
        $("#product_info").html(data).show();
      });
  
    e.preventDefault();
  });
  
  $(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
  
    $(".submenu-toggle").click(function () {
      $(this).parent().children("ul.submenu").toggle(200);
    });
  
    suggestion();
  });  