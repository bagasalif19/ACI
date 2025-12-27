<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ACI</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --navy-blue: #1e293b;
            --light-blue: #38bdf8;
            --bg-gray: #f1f5f9;
            --white: #ffffff;
            --danger: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body { display: flex; background-color: var(--bg-gray); min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--navy-blue); color: white; padding: 20px; flex-shrink: 0; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 30px; text-align: center; border-bottom: 1px solid #334155; padding-bottom: 15px; }
        .sidebar ul { list-style: none; }
        .sidebar li { padding: 12px 15px; margin-bottom: 5px; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .sidebar li:hover, .sidebar li.active { background-color: var(--light-blue); color: var(--navy-blue); font-weight: bold; }
        .sidebar i { margin-right: 10px; }

        /* Main Content */
        .main-content { flex-grow: 1; padding: 25px; overflow-y: auto; }
        
        /* Header */
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .search-box { background: white; padding: 10px; border-radius: 8px; width: 300px; border: 1px solid #ddd; }
        .user-profile { display: flex; align-items: center; gap: 10px; font-weight: 500; }

        /* Cards Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); text-align: center; }
        .card h3 { font-size: 2rem; color: var(--navy-blue); }
        .card p { color: #64748b; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-top: 5px; }

        /* Charts */
        .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
        .chart-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        .chart-container h4 { margin-bottom: 15px; color: var(--navy-blue); }

        /* Table */
        .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid var(--bg-gray); color: #64748b; }
        td { padding: 12px; border-bottom: 1px solid var(--bg-gray); font-size: 0.9rem; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; color: white; }
        .bg-urgent { background-color: var(--danger); }
        .bg-blue { background-color: var(--light-blue); color: #1e293b; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ACI ADMIN</h2>
        <ul>
            <li class="active"><i class="fas fa-home"></i> Dashboard</li>
            <li><i class="fas fa-file-alt"></i> Manajemen Laporan</li>
            <li><i class="fas fa-map-marker-alt"></i> Data Kecamatan</li>
            <li><i class="fas fa-cog"></i> Pengaturan Akun</li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <input type="text" class="search-box" placeholder="Cari ID Laporan...">
            <div class="user-profile">
                <span>Bagas Alif</span>
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </header>

        <div class="stats-grid">
            <div class="card"><h3>520</h3><p>Diterima</p></div>
            <div class="card"><h3>350</h3><p>Diverifikasi</p></div>
            <div class="card"><h3>70</h3><p>Selesai</p></div>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <h4>Laporan per Kecamatan</h4>
                <canvas id="barChart"></canvas>
            </div>
            <div class="chart-container">
                <h4>Status Laporan</h4>
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <div class="table-container">
            <h4>Laporan Terbaru</h4>
            <table>
                <thead>
                    <tr>
                        <th>ID Laporan</th>
                        <th>Nama Pelapor</th>
                        <th>Kecamatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#LP001</td>
                        <td>Andi Wijaya</td>
                        <td>Medan Sunggal</td>
                        <td><span class="badge bg-blue">Diverifikasi</span></td>
                        <td style="color: var(--light-blue); cursor: pointer;">Detail</td>
                    </tr>
                    <tr>
                        <td>#LP002</td>
                        <td>Siti Aminah</td>
                        <td>Deli Tua</td>
                        <td><span class="badge bg-urgent">Segera - Rusak >30%</span></td>
                        <td style="color: var(--light-blue); cursor: pointer;">Detail</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Medan', 'Helvetia', 'Medan Sunggal', 'Deli Tua', 'Pecut Sei Tuan'],
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: [120, 90, 70, 50, 40],
                    backgroundColor: '#38bdf8'
                }]
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Diterima', 'Diverifikasi', 'Selesai', 'Segera'],
                datasets: [{
                    data: [50, 30, 15, 5],
                    backgroundColor: ['#1e293b', '#38bdf8', '#10b981', '#ef4444']
                }]
            }
        });
    </script>
</body>
</html>