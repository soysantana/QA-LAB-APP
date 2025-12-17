function suggestion() {
  let timer = null;

  $("#sug_input")
    .off("input")
    .on("input", function () {

      const value = $(this).val();

      clearTimeout(timer);

      timer = setTimeout(() => {
        if (value.length >= 1) {
          $.ajax({
            type: "POST",
            url: "../php/ajax.php",
            data: {
              action: "suggest",
              product_name: value
            },
            dataType: "json",
            success: function (res) {
              updateDatalist(res.html);
            },
            error: function (xhr) {
              console.error(xhr.status, xhr.responseText);
            }
          });
        } else {
          clearDatalist();
        }
      }, 300);
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
  e.preventDefault();

  var formData = {
    action: "find_product",
    p_name: $("input[name=title]").val(),
  };

  $.ajax({
    type: "POST",
    url: "../php/ajax.php",
    data: formData,
    dataType: "json",
  })
    .done(function (res) {
      $("#product_info").html(res.html).show();
    })
    .fail(function (xhr) {
      console.error(xhr.status, xhr.responseText);
      $("#product_info").html(
        '<div class="alert alert-danger">Error al buscar el producto</div>'
      ).show();
    });
});


$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip();

  $(".submenu-toggle").click(function () {
    $(this).parent().children("ul.submenu").toggle(200);
  });

  suggestion();
});  