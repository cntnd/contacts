<div class="tabs__content--pane spreadsheet fade" id="contacts__content--contacts">
    <script>
        $(document).ready(function () {
            var width = $(".tabs__content--pane").innerWidth();
            var data = {$data};
            var columns = {$columns};
            var table = jspreadsheet(document.getElementById('spreadsheet'), {
                data: data,
                columns: columns,
                tableOverflow: true,
                tableWidth: width,
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
                toolbar: [{
                    type: 'i',
                    content: 'add_circle',
                    onclick: function () {
                        $('#editor').toggleClass('active');
                    }
                }, {
                    type: 'i',
                    content: 'save',
                    onclick: function () {
                        console.log('store');
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
                ondeleterow: function () {
                    alert('delete');
                }
            });
        });
    </script>
    <div id="spreadsheet"></div>
</div>