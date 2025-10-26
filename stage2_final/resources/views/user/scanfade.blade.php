<!DOCTYPE html>
<html>
<head><title>Scan Wajah</title></head>
<body>
  <h1>Scan Wajah Anda</h1>
  <p>Silakan hadapkan wajah ke kamera ESP32.</p>

  <button id="scanBtn">Mulai Scan</button>

  <script>
    document.getElementById('scanBtn').addEventListener('click', async () => {
        const uid = "{{ $uid }}";
        // Kirim permintaan ke ESP32
        await fetch("http://192.168.4.1/start_scan?uid=" + uid);
        alert("Silakan hadapkan wajah ke kamera.");
    });
  </script>
</body>
</html>
