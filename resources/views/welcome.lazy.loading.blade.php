<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handsontable in Laravel</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css">
</head>
<body>
    {{-- <div class="mt-4">
        <h1>Organizations</h1>
        <div id="hot" class="w-100"></div>
    </div> --}}

    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <h1 class="mb-4">Handsontable with Bootstrap</h1>
                <div id="hot" ></div>
            </div>
        </div>
    </div>
     <!-- Bootstrap JS (optional, for Bootstrap components like modals, dropdowns, etc.) -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Handsontable JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <script>
        let offset = 0;
        const limit = 100;
        let hot;
    
        function loadData(offset) {
            fetch(`/organizations?offset=${offset}&limit=${limit}`)
                .then(response => response.json())
                .then(data => {
                    if (!hot) {
                        // Initialize Handsontable on first load
                        const container = document.getElementById('hot');
                        hot = new Handsontable(container, {
                            data: data.data,
                            rowHeaders: true,
                            colHeaders: ['ID', 'Name', 'Email','Phone','address','City','Region','Country','PostalCode'],
                            columns: [
                                { data: 'id', readOnly: true },
                                // { data: 'name' , type: 'numeric'},
                                { data: 'name'},
                                { data: 'email' },
                                { data: 'phone' },
                                { data: 'address' },
                                { data: 'city' },
                                { data: 'region' },
                                { data: 'country' },
                                { data: 'postal_code' },

                            ],
                            licenseKey: 'non-commercial-and-evaluation'
                        });
                    } else {
                        // Append new data to Handsontable
                        hot.loadData([...hot.getData(), ...data.data]);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    
        // Load initial data
        loadData(offset);
    
        // Infinite scroll
        window.addEventListener('scroll', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
                offset += limit;
                loadData(offset);
            }
        });
    </script>
    {{-- <script>
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
                            colHeaders: ['ID', 'Name', 'Email','Phone','address','City','Region','Country','PostalCode'],
                            columns: [
                                { data: 'id', readOnly: true },
                                // { data: 'name' , type: 'numeric'},
                                { data: 'name'},
                                { data: 'email' },
                                { data: 'phone' },
                                { data: 'address' },
                                { data: 'city' },
                                { data: 'region' },
                                { data: 'country' },
                                { data: 'postal_code' },

                            ],
                            licenseKey: 'non-commercial-and-evaluation'
                        });
                    } else {
                        // Update Handsontable data
                        hot.loadData(data.data);
                    }

                    // Update pagination info
                    totalPages = data.lastPage;
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
    </script> --}}
</body>
</html>