<!DOCTYPE html>
<html>
<head>
  <title>Queue Monitor</title>
<link rel="stylesheet" href="monitor.css">
</head>
<body>
  <h1>Now Serving</h1>

  <div class="container">
    <div class="panel">
      <h2>Registrar</h2>
      <div id="regNum" class="number">—</div>
      <div id="regName" class="name">Waiting...</div>
    </div>

    <div class="panel">
      <h2>Accounting</h2>
      <div id="accNum" class="number">—</div>
      <div id="accName" class="name">Waiting...</div>
    </div>
  </div>

  <script>
    function loadQueue() {
      fetch('monitor_data.php')
        .then(res => res.json())
        .then(data => {
          // Registrar ni siya
          if (data.Registrar) {
            document.getElementById('regNum').textContent = String(data.Registrar.queue_number).padStart(3, '0');
            document.getElementById('regName').textContent = data.Registrar.full_name;
          } else {
            document.getElementById('regNum').textContent = '—';
            document.getElementById('regName').textContent = 'Waiting...';
          }

          // Accounting ni siya
          if (data.Accounting) {
            document.getElementById('accNum').textContent = String(data.Accounting.queue_number).padStart(3, '0');
            document.getElementById('accName').textContent = data.Accounting.full_name;
          } else {
            document.getElementById('accNum').textContent = '—';
            document.getElementById('accName').textContent = 'Waiting...';
          }
        });
    }

    loadQueue();
    setInterval(loadQueue, 5000);
  </script>
</body>
</html>
