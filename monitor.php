<!DOCTYPE html>
<html>
<head>
    <title>Queue Monitor</title>
    <link rel="stylesheet" href="monitor.css">
    <style>
        /* Optional basic style to make skipped list visible */
        .skipped-numbers {
            font-size: 0.9em;
            color: #ff5722; /* Orange/Red for emphasis */
            margin-top: 10px;
            font-weight: bold;
        }
        .skipped-numbers span {
            display: block;
            margin-top: 5px;
            color: #777; /* Subtler color for the actual list */
            font-weight: normal;
        }
    </style>
</head>
<body>
    <h1>Now Serving</h1>

    <div class="container">
        <div class="panel">
            <h2>Registrar</h2>
            <div id="regNum" class="number">—</div>
            <div id="regName" class="name">Waiting...</div>
            <div class="skipped-numbers">
                Recently Skipped:
                <span id="regSkipped">None</span>
            </div>
        </div>

        <div class="panel">
            <h2>Accounting</h2>
            <div id="accNum" class="number">—</div>
            <div id="accName" class="name">Waiting...</div>
            <div class="skipped-numbers">
                Recently Skipped:
                <span id="accSkipped">None</span>
            </div>
        </div>
    </div>

    <script>
        function loadQueue() {
            fetch('monitor_data.php')
                .then(res => res.json())
                .then(data => {
                    // --- Registrar Logic ---
                    if (data.Registrar && data.Registrar.serving) {
                        document.getElementById('regNum').textContent = String(data.Registrar.serving.queue_number).padStart(3, '0');
                        document.getElementById('regName').textContent = data.Registrar.serving.full_name;
                    } else {
                        document.getElementById('regNum').textContent = '—';
                        document.getElementById('regName').textContent = 'Waiting...';
                    }
                    
                    // Display Registrar Skipped Numbers
                    const regSkippedList = data.Registrar.skipped_list;
                    document.getElementById('regSkipped').textContent = regSkippedList.length > 0 
                        ? regSkippedList.join(', ')
                        : 'None';


                    // --- Accounting Logic ---
                    if (data.Accounting && data.Accounting.serving) {
                        document.getElementById('accNum').textContent = String(data.Accounting.serving.queue_number).padStart(3, '0');
                        document.getElementById('accName').textContent = data.Accounting.serving.full_name;
                    } else {
                        document.getElementById('accNum').textContent = '—';
                        document.getElementById('accName').textContent = 'Waiting...';
                    }

                    // Display Accounting Skipped Numbers
                    const accSkippedList = data.Accounting.skipped_list;
                    document.getElementById('accSkipped').textContent = accSkippedList.length > 0 
                        ? accSkippedList.join(', ')
                        : 'None';
                });
        }

        loadQueue();
        setInterval(loadQueue, 5000);
    </script>
</body>
</html>