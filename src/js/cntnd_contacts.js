/* cntnd_contacts */
$(document).ready(function () {
  $(".add_contact").click(function () {
    let contact = $(this).data("contact");
    let action = $(this).data("action");
    let source = $(this).data("source");
    let index = $(this).data("index");
    let data = $("#" + contact).val();
    let json = JSON.parse(window.atob(data));
    $('form[name="editor_form"] .editor_form_remove').show();

    $('form[name="editor_form"] :input').each(function () {
      $(this).removeClass("highlight");
      let input = $(this).attr("name");
      let type = $(this).attr("type");
      let value = json[input];
      if (value !== undefined) {
        if (type === "checkbox" || type === "radio") {
          $(this).prop("checked", value);
        } else {
          $(this).val(value);
        }
      }
    });

    if (json["_diff"] !== undefined) {
      $.each(json["_diff"], function (key) {
        $('form[name="editor_form"] input[name="' + key + '"]').addClass("highlight");
      });
    }

    $('form[name="editor_form"] input[name="editor_form_action"]').val(action);
    $('form[name="editor_form"] input[name="editor_form_source"]').val(source);
    $('form[name="editor_form"] input[name="editor_form_index"]').val(index);
    $("#editor").addClass('active')
  });

  $(".editor_form_remove").click(function () {
    $('form[name="editor_form"] input[name="editor_form_action"]').val("delete");
    $('form[name="editor_form"]').submit();
  });

  $(".remove_contact").click(function () {
    let source = $(this).data("source");
    let index = $(this).data("index");
    $('form[name="delete_form"] input[name="editor_form_source"]').val(source);
    $('form[name="delete_form"] input[name="editor_form_index"]').val(index);

    $('form[name="delete_form"]').submit();
  });

  $(".popup .close").click(function () {
    $('form[name="editor_form"] .editor_form_remove').hide();
    $('form[name="editor_form"]').trigger("reset");
    $('form[name="editor_form"] :input').each(function () {
      $(this).removeClass("highlight");
    });

    let overlay = $(this).parent(".popup").parent(".overlay");
    closePopup(overlay);
  });

  $('form[name="editor_form"]').submit(function () {
    let action = $('input[name="editor_form_action"]').val();
    if (action === undefined) {
      $('input[name="editor_form_action"]').val("new");
    }
    // set action for later post
    // validation?
    let overlay = $(this).parent(".popup").parent(".overlay");
    closePopup(overlay);

    console.log("editor submit");
    return true;
  });

  function closePopup(overlay) {
    if (overlay.hasClass("active")) {
      overlay.toggleClass('active');
    }
  }

  function deleteEntry(index) {
    // https://api.jquery.com/submit/
    //$().submit()
  }

  $.getJSON("https://cdn.jsdelivr.net/npm/jexcel@4.6.1/lang/de_DE.json", function (text) {
    let options = {
      data: data,
      columns: columns,
      tableOverflow: true,
      tableWidth: $(".tabs__content--pane").innerWidth(),
      tableHeight: '600px',
      defaultColWidth: 120,
      defaultColAlign: 'left',
      filters: true,
      allowManualInsertColumn: false,
      allowInsertColumn: false,
      allowInsertRow: false,
      allowManualInsertRow: false,
      allowDeleteColumn: false,
      allowManualDeleteColumn: false,
      search: true,
      csvFileName: 'Adressen',
      text: text,
      toolbar: [{
        type: 'i',
        content: 'add_circle',
        onclick: function () {
          $('#editor').toggleClass('active');
        }
      }, {
        type: 'i',
        content: 'save',
        onclick: function (instance) {
          console.log('store');
          var data = table.getData();
          console.log(data);
        }
      }, {
        type: 'i',
        content: 'sync',
        onclick: function () {
          console.log('sync');
        }
      }, {
        type: 'i',
        content: 'download',
        onclick: function () {
          table.download();
        }
      }, {
        type: 'i',
        content: 'undo',
        onclick: function () {
          table.undo();
        }
      }, {
        type: 'i',
        content: 'redo',
        onclick: function () {
          table.redo();
        }
      }],
      onchange: function (instance, cell, x, y, value) {
        console.log("CHANGE", x, y, value);
      },
      ondeleterow: function (instance, row, amount, value) {
        console.log("delete", row, amount, "index first row", value[0][9].innerHTML);
        alert('delete');
      }
    }

    let table = $('#spreadsheet').jspreadsheet(options);
  });

  $('#store_spreadsheet').click(function() {
    var data = table.getData();
    /*
    var headers = table.getHeaders();
    $('#cntnd_spreadsheet-csv').val(Base64.encode(JSON.stringify(data)));
    $('#cntnd_spreadsheet-headers').val(Base64.encode(JSON.stringify(headers)));
    return true;

     */
  });
});
