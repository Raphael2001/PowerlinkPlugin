function delete_token() {
  (function ($) {
    $.ajax({
      type: "POST",
      url: ajaxurl,
      cache: false,
      data: {
        action: "delete_token",
      },
      success: function (response) {
        console.log(response);
      },
      fail: function () {},
      complete: function () {
        location.reload();
      },
    });
  })(jQuery);
}

document.addEventListener("DOMContentLoaded", function (event) {
  //we ready baby
});

$(document).ready(function () {
  $(".chosen-select").dropdown({
    multipleMode: "search",
    searchable: true,
    input: '<input type="text" maxLength="20" placeholder="חפש">',
  });
});
