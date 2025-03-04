<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handsontable in Laravel</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Handsontable CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col">
                <h1 class="mb-4">Handsontable with Bootstrap</h1>
                <div class="controls">
                    <button id="export-file">Download CSV</button>
                    <button id="ExportAllData">Download All Data</button>
                  </div>
                <div id="hot" class="w-100"></div>
                <div id="pagination">
                    <button id="prevPage">Previous</button>
                    <span id="pageInfo"></span> total 
                    <span id="total_record"></span> entries
                    <button id="nextPage">Next</button>
                </div>
            </div>
        </div>
    </div>
     <!-- Bootstrap JS (optional, for Bootstrap components like modals, dropdowns, etc.) -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Handsontable JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 1;
        let hot;

        function loadData(page) {
            fetch(`/organizations?page=${page}&perPage=30`)
                .then(response => response.json())
                .then(data => {
                    if (!hot) {
                        // Initialize Handsontable on first load
                        const container = document.getElementById('hot');
                        hot = new Handsontable(container, {
                            data: data.data,
                            rowHeaders: true,
                            colHeaders: ['ID', 'Name','Account Name', 'Email','Phone','address','City','Region','Country','Postal Code','Created At','Last Updated At'],
                            columns: [
                                { data: 'id', readOnly: true },
                                // { data: 'name' , type: 'numeric'},
                                { data: 'name'},
                                { data: 'account_name'},
                                { data: 'email' },
                                { data: 'phone' },
                                { data: 'address' },
                                { data: 'city' },
                                { data: 'region' },
                                { data: 'country' },
                                { data: 'postal_code' },
                                { data: 'created_at' },
                                { data: 'updated_at' },

                            ],
                            licenseKey: 'non-commercial-and-evaluation',
                            height: 'auto',
                            contextMenu: true,
                            filters: true, 
                            dropdownMenu: true, // Enable dropdown menu for filter options
                            manualColumnMove: true,
                            manualRowMove: true,
                            // enable the `HiddenColumns` plugin
                            // hiddenColumns: {
                            //     columns: [2, 4, 6],
                            //     indicators: true,
                            // },
                            autoWrapRow: true,
                            autoWrapCol: true,
                            fixedColumnsStart: 1,
                            fixedRowsTop: 1,
                        });
                    } else {
                        // Update Handsontable data
                        hot.loadData(data.data);
                    }

                    const exportPlugin = hot.getPlugin('exportFile');
const button = document.querySelector('#export-file');

button.addEventListener('click', () => {
  exportPlugin.downloadFile('csv', {
    bom: false,
    columnDelimiter: ',',
    columnHeaders: false,
    exportHiddenColumns: true,
    exportHiddenRows: true,
    fileExtension: 'csv',
    filename: 'Handsontable-CSV-file_[YYYY]-[MM]-[DD]',
    mimeType: 'text/csv',
    rowDelimiter: '\r\n',
    rowHeaders: true,
  });
});

                    // Update pagination info
                    totalPages = data.lastPage;
                    document.getElementById('total_record').innerText = data.total;
                    document.getElementById('pageInfo').innerText = `Page ${currentPage} of ${totalPages}`;
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Load initial data
        loadData(currentPage);

        // Pagination controls
        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                loadData(currentPage);
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                loadData(currentPage);
            }
        });

        // Export to XLSX functionality
        document.getElementById('ExportAllData').addEventListener('click', () => {
    // Fetch all data from the backend
    fetch('/organizations/all')
        .then(response => {
            // Check if the response is OK
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            // Initialize an array to store all data
            const allData = [];

            // Create a reader to process the stream
            const reader = response.body.getReader();

            // Function to read the stream
            const readStream = () => {
                return reader.read().then(({ done, value }) => {
                    if (done) {
                        // Stream is complete
                        console.log('All data for export:', allData); // Debugging

                        // Get the column headers
                        const headers = hot.getColHeader();

                        // Prepare the data for XLSX
                        const wsData = [
                            headers, // Add headers as the first row
                            ...allData.map(row => [
                                row.id,
                                row.name,
                                row.email,
                                row.phone,
                                row.address,
                                row.city,
                                row.region,
                                row.country,
                                row.postal_code
                            ]) // Add the table data
                        ];

                        // Create a worksheet
                        const ws = XLSX.utils.aoa_to_sheet(wsData);

                        // Create a workbook
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

                        // Export the file
                        XLSX.writeFile(wb, 'export.xlsx');

                        return;
                    }

                    // Convert the chunk to a string
                    const chunk = new TextDecoder().decode(value);
                    console.log('Chunk received:', chunk);
                    // Process the chunk (append to allData)
                    try {
                        // Remove the opening '[' and closing ']' from the JSON array
                        const jsonString = chunk.replace(/^\[|\]$/g, '');

                        // Split the JSON string into individual objects
                        const jsonObjects = jsonString.split('},{');

                        // Parse each JSON object and add it to allData
                        jsonObjects.forEach((obj, index) => {
                            // Add back the curly braces if necessary
                            if (!obj.startsWith('{')) {
                                obj = '{' + obj;
                            }
                            if (!obj.endsWith('}')) {
                                obj = obj + '}';
                            }

                            // Parse the JSON object
                            const parsedObj = JSON.parse(obj);
                            allData.push(parsedObj);
                        });
                    } catch (error) {
                        console.error('Error parsing JSON chunk:', error);
                    }

                    // Continue reading the stream
                    return readStream();
                });
            };

            // Start reading the stream
            return readStream();
        })
        .catch(error => console.error('Error fetching all data:', error));
});
    </script>
</body>
</html>