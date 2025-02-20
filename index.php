<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    .chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 100%;
    }

    #horizontalBarChart {
        width: 100%;
        max-width: 100%;
    }

    .card {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card mb-3 {
        margin-left: 20px;
    }

    .card-header {
        background-color: #363639;
        padding: 16px;
        font-size: 1.25rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .card-body {
        padding: 5px;
    }

    .d-flex {
        display: flex;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .align-items-center {
        align-items: center;
    }

    .form-control {
        width: auto;
        min-width: 200px;
    }

    body>pre {
        display: none;
    }

    .hidden-by-date {
        display: none !important;
    }

    .custom-align {
        margin-left: auto;
    }

    select {
        color: #fff;
    }

    select option {
        background-color: #fff;
        color: #000;
    }

    body {
        background-image: url('../popending/assets/img/mainbg.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
</style>


<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <a class="navbar-brand ps-3" href="index.php">Haier</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <form method="get" action="dashboard.php" class="d-flex">
                        <select name="plant" class="form-select" onchange="this.form.submit();"
                            style="width: auto; margin-right: 60px; background-color: transparent; border: none #ccc; color: #fff;">
                            <option value="" style="background-color: #fff; color: #000;">Plant</option>
                            <?php
                            include 'connection.php';

                            if (isset($conn)) {
                                try {
                                    $sqlPlant = "SELECT plant FROM po_pending GROUP BY plant;";
                                    $queryPlant = $conn->prepare($sqlPlant);
                                    $queryPlant->execute();

                                    $resultPlant = $queryPlant->fetchAll(PDO::FETCH_ASSOC);

                                    if (!empty($resultPlant)) {
                                        foreach ($resultPlant as $row) {
                                            echo '<option value="' . htmlspecialchars($row["plant"]) . '">' . htmlspecialchars($row["plant"]) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No Plants Available</option>';
                                    }
                                } catch (PDOException $e) {
                                    echo "<option>Error: " . htmlspecialchars($e->getMessage()) . "</option>";
                                }
                            } else {
                                echo '<option value="">Database connection is not available</option>';
                            }
                            ?>
                        </select>
                    </form>
                </li>
            </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link " href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link" href="datatable.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Data Table
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active"></li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card bg-primary text-white shadow ">
                                <div class="card-body ">Total Order Qty</div>
                                <?php
                                include 'connection.php';
                                if (isset($conn)) {
                                    $sqlOrder = "SELECT SUM(order_qty) AS total_order_qty
                                                FROM po_pending";
                                    $queryOrderqty = $conn->prepare($sqlOrder);
                                    $queryOrderqty->execute();
                                    $resultOrderqty = $queryOrderqty->fetch(PDO::FETCH_ASSOC);
                                    $OrderqtyCount = $resultOrderqty['total_order_qty'];
                                } else {
                                    echo "<p>Database connection is not available.</p>";
                                    $OrderqtyCoun = 'N/A';
                                }
                                ?>
                                <p class="card-text ps-3"><?php echo $OrderqtyCount; ?> Quantity</p>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card bg-warning text-white shadow">
                                <div class="card-body">Factory Order</div>
                                <?php
                                if (isset($conn)) {
                                    $sqlFactory = "SELECT plant, COUNT(*) AS Factory 
                                                    FROM po_pending; ";
                                    $queryFactory = $conn->prepare($sqlFactory);
                                    $queryFactory->execute();
                                    $resultFactory = $queryFactory->fetch(PDO::FETCH_ASSOC);
                                    $FactoryCount = $resultFactory['Factory'];
                                } else {
                                    $FactoryCount = 'N/A';
                                }
                                ?>
                                <p class="card-text ps-3"><?php echo $FactoryCount; ?> Orders</p>
                            </div>
                            </a>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card bg-success text-white shadow">
                                <?php
                                if (isset($conn)) {
                                    try {
                                        $sqlpoitem = "SELECT po_item, COUNT(*) AS poitem
                                                        FROM po_pending
                                                        GROUP BY po_item
                                                        ORDER BY poitem DESC
                                                        LIMIT 1;";
                                        $querypoitem = $conn->prepare($sqlpoitem);
                                        $querypoitem->execute();
                                        $resultpoitem = $querypoitem->fetch(PDO::FETCH_ASSOC);

                                        if ($resultpoitem) {
                                            $poitemCount = $resultpoitem['po_item'];
                                            $poitemcount = $resultpoitem['poitem'];
                                        } else {
                                            $poitemCount = 'N/A';
                                            $poitemcount = 0;
                                        }
                                    } catch (Exception $e) {
                                        $poitemCount = 'Error';
                                        $poitemcount = 'Error';
                                        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                                    }
                                } else {
                                    $poitemCount = 'N/A';
                                    $poitemcount = 0;
                                }
                                ?>
                                <div class="card-body">Max Po Item <?php echo htmlspecialchars($poitemCount); ?></div>
                                <p class="card-text ps-3"><?php echo htmlspecialchars($poitemcount); ?> Order</p>
                            </div>
                            </a>
                        </div>

                    </div>

                    <div class="col-xl-12 col-lg-12 mb-3" style="margin-right: auto; margin-left: auto;">
                        <div class="card shadow mb-3">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Aging Days</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <?php
                                    include 'connection.php';

                                    $sql = "SELECT 
                                            CASE 
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 0 AND 15 THEN '0-15'
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 16 AND 30 THEN '16-30'
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 31 AND 60 THEN '31-60'
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 61 AND 90 THEN '61-90'
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 91 AND 120 THEN '91-120'
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 121 AND 150 THEN '121-150'
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 151 AND 180 THEN '151-180'
                                                WHEN DATEDIFF(CURDATE(), created_on) BETWEEN 181 AND 360 THEN '181-360'
                                                ELSE '>360'
                                            END AS aging_days,
                                            COUNT(*) AS 'order'
                                        FROM po_pending
                                        GROUP BY aging_days
                                        ORDER BY MIN(DATEDIFF(CURDATE(), created_on));";

                                    $agingOrder = [];
                                    $maxAging = 0;

                                    try {
                                        $result = $conn->query($sql);

                                        foreach ($result as $row) {
                                            if (isset($row['aging_days'], $row['order'])) {
                                                $agingOrder[$row['aging_days']] = $row['order'];
                                                $maxAging = max($maxAging, $row['order']);
                                            }
                                        }
                                    } catch (Exception $e) {
                                        echo "<p>Error fetching data: " . $e->getMessage() . "</p>";
                                    }

                                    $agingRanges = [
                                        '0-15',
                                        '16-30',
                                        '31-60',
                                        '61-90',
                                        '91-120',
                                        '121-150',
                                        '151-180',
                                        '181-360',
                                        '>360',
                                    ];

                                    echo "<table class='table table-bordered text-center'>";
                                    echo "<thead>
                                <tr>
                                    <th>Aging</th>";
                                    foreach ($agingRanges as $range) {
                                        echo "<th>{$range} Days</th>";
                                    }
                                    echo "</tr>
                                </thead>
                                <tbody>";
                                    echo "<tr><td>Count</td>";
                                    foreach ($agingRanges as $range) {
                                        $order = $agingOrder[$range] ?? 0;
                                        $highlight = $order == $maxAging ? "style='background-color: #ffab5c; font-weight: bold;'" : "";
                                        // เพิ่มลิงก์ไป พร้อมพารามิเตอร์ aging
                                        echo "<td $highlight><a href='agingplant.php?aging={$range}' style='text-decoration: none; color: inherit;'>{$order}</a></td>";
                                    }
                                    echo "</tr>";
                                    echo "</tbody></table>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Chart Row -->
                    <div class="row">
                        <div class="col-xl-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-chart-pie me-1"></i> Receive Order
                                </div>
                                <div class="card-body" style="width: 100%; margin: 5px;">
                                    <canvas id="poChart" style="width: 100%; height: 340px;"></canvas>
                                    <?php
                                    include 'connection.php';
                                    try {
                                        $sql = "SELECT 
                            SUM(CASE WHEN receive_qty < order_qty THEN 1 ELSE 0 END) AS pending,
                            SUM(CASE WHEN receive_qty >= order_qty THEN 1 ELSE 0 END) AS complete
                        FROM po_pending";

                                        $query = $conn->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetch(PDO::FETCH_ASSOC);

                                        $categories = ['Pending', 'Complete'];
                                        $revenues = [
                                            $results['pending'] ?? 0,
                                            $results['complete'] ?? 0,
                                        ];

                                        $categories_json = json_encode($categories, JSON_UNESCAPED_UNICODE);
                                        $revenues_json = json_encode($revenues, JSON_UNESCAPED_UNICODE);
                                    } catch (PDOException $e) {
                                        echo "<p>Error: " . $e->getMessage() . "</p>";
                                    }
                                    ?>
                                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                                    <script>
                                        const categories = <?php echo $categories_json; ?>;
                                        const revenues = <?php echo $revenues_json; ?>;

                                        const chartData = {
                                            labels: categories,
                                            datasets: [{
                                                label: 'Deliveries',
                                                data: revenues,
                                                backgroundColor: [
                                                    'rgba(255, 102, 102, 0.5)',
                                                    'rgba(54, 162, 235, 0.5)'
                                                ],
                                                borderColor: [
                                                    'rgba(255, 102, 102, 1)',
                                                    'rgba(54, 162, 235, 1)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        };

                                        const ctxDel = document.getElementById('poChart').getContext('2d');
                                        const poChart = new Chart(ctxDel, {
                                            type: 'pie',
                                            data: chartData,
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: {
                                                        display: true,
                                                        position: 'bottom'
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(tooltipItem) {
                                                                const label = tooltipItem.label || '';
                                                                const value = tooltipItem.raw || 0;
                                                                return `${label}: ${value}`;
                                                            }
                                                        }
                                                    },
                                                    datalabels: {
                                                        display: true,
                                                        color: '#000',
                                                        font: {
                                                            size: 15,
                                                        },
                                                        formatter: (value, context) => {
                                                            const total = context.dataset.data.reduce((acc, curr) => acc + curr, 0);
                                                        }
                                                    }
                                                },
                                                onClick: function(event, elements) {
                                                    if (elements.length > 0) {
                                                        const index = elements[0].index;
                                                        const status = chartData.labels[index];
                                                        window.location.href = `statusplant.php?status=${encodeURIComponent(status)}`;
                                                    }
                                                }
                                            },
                                            plugins: [ChartDataLabels]
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i> Top Materials Orderede
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="width: 100%; margin: auto; padding: 0px;">
                                        <canvas id="horizontalBarChart" style="width: 100%; height: 350px;"></canvas>
                                        <?php
                                        include 'connection.php';
                                        try {
                                            $sql = "SELECT mat_desc, SUM(order_qty) AS total_ordered 
                                            FROM po_pending
                                            GROUP BY mat_desc
                                            ORDER BY total_ordered DESC
                                            LIMIT 10;";
                                            $result = $conn->prepare($sql);
                                            $result->execute();
                                            $results = $result->fetchAll(PDO::FETCH_ASSOC);

                                            $labels = [];
                                            $data = [];

                                            if (count($results) > 0) {
                                                foreach ($results as $row) {
                                                    $labels[] = htmlspecialchars($row['mat_desc']);
                                                    $data[] = $row['total_ordered'];
                                                }
                                            }
                                        } catch (Exception $e) {
                                            echo "<p>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
                                        }
                                        ?>
                                        <script>
                                            const labels = <?php echo json_encode($labels); ?>;
                                            const data = <?php echo json_encode($data); ?>;
                                            const ctxHor = document.getElementById('horizontalBarChart').getContext('2d');

                                            new Chart(ctxHor, {
                                                type: 'bar',
                                                data: {
                                                    labels: labels,
                                                    datasets: [{
                                                        label: 'Quantity',
                                                        data: data,
                                                        backgroundColor: [
                                                            'rgba(255, 99, 132, 0.8)',
                                                            'rgba(54, 162, 235, 0.8)',
                                                            'rgba(255, 206, 86, 0.8)',
                                                            'rgba(75, 192, 192, 0.8)',
                                                            'rgba(153, 102, 255, 0.8)',
                                                            'rgba(255, 159, 64, 0.8)',
                                                            'rgba(199, 199, 199, 0.8)'
                                                        ],
                                                        borderColor: [
                                                            'rgba(255, 99, 132, 1)',
                                                            'rgba(54, 162, 235, 1)',
                                                            'rgba(255, 206, 86, 1)',
                                                            'rgba(75, 192, 192, 1)',
                                                            'rgba(153, 102, 255, 1)',
                                                            'rgba(255, 159, 64, 1)',
                                                            'rgba(199, 199, 199, 1)'
                                                        ],
                                                        borderWidth: 1
                                                    }]
                                                },
                                                options: {
                                                    indexAxis: 'y',
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    plugins: {
                                                        legend: {
                                                            display: true,
                                                            position: 'top',
                                                        }
                                                    },
                                                    scales: {
                                                        x: {
                                                            beginAtZero: true,
                                                            ticks: {
                                                                font: {
                                                                    size: 10
                                                                }
                                                            }
                                                        },
                                                        y: {
                                                            ticks: {
                                                                font: {
                                                                    size: 9
                                                                }
                                                            }
                                                        }
                                                    },
                                                    onClick: (e, elements) => {
                                                        if (elements.length > 0) {
                                                            const index = elements[0].index;
                                                            const selectedMats = labels[index];
                                                            window.location.href = `material_plant.php?mat_desc=${encodeURIComponent(selectedMats)}`;
                                                        }
                                                    }

                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4" style="margin-right: 10px;">
                            <div class=" py-3">
                                <i class="fas fa-chart-bar me-1"></i>Po Pending Group
                            </div>
                            <div class="card-body">
                                <canvas id="chartContainer" width="auto" height="120"></canvas>
                                <?php
                                include 'connection.php';

                                try {
                                    $sqlMax = "SELECT po_group, COUNT(*) AS Po_Pending FROM po_pending GROUP BY po_group ORDER BY Po_Pending DESC;";
                                    $queryMax = $conn->prepare($sqlMax);
                                    $queryMax->execute();
                                    $resultsMax = $queryMax->fetchAll(PDO::FETCH_ASSOC);

                                    $pogroupMax = [];
                                    $revenuesMax = [];

                                    foreach ($resultsMax as $row) {
                                        if (!empty($row['po_group'])) {
                                            $pogroupMax[] = $row['po_group'];
                                            $revenuesMax[] = $row['Po_Pending'];
                                        }
                                    }

                                    $sqlMin = "SELECT po_group, COUNT(*) AS Po_Pending FROM po_pending 
                                            GROUP BY po_group ORDER BY Po_Pending ASC;";
                                    $queryMin = $conn->prepare($sqlMin);
                                    $queryMin->execute();
                                    $resultsMin = $queryMin->fetchAll(PDO::FETCH_ASSOC);

                                    $pogroupMin = [];
                                    $revenuesMin = [];

                                    foreach ($resultsMin as $row) {
                                        if (!empty($row['po_group'])) {
                                            $pogroupMin[] = $row['po_group'];
                                            $revenuesMin[] = $row['Po_Pending'];
                                        }
                                    }
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }

                                $pogroupMax_json = json_encode($pogroupMax);
                                $revenuesMax_json = json_encode($revenuesMax);
                                $pogroupMin_json = json_encode($pogroupMin);
                                $revenuesMin_json = json_encode($revenuesMin);
                                ?>
                                <script>
                                    const pogroupMax = JSON.parse('<?php echo $pogroupMax_json; ?>');
                                    const revenuesMax = JSON.parse('<?php echo $revenuesMax_json; ?>');
                                    const pogroupMin = JSON.parse('<?php echo $pogroupMin_json; ?>');
                                    const revenuesMin = JSON.parse('<?php echo $revenuesMin_json; ?>');

                                    const barColorsMax = pogroupMax.map((_, index) => `hsl(${index * 360 / pogroupMax.length}, 70%, 60%)`);
                                    const barColorsMin = pogroupMin.map((_, index) => `hsl(${index * 360 / pogroupMin.length}, 70%, 60%)`);

                                    const ctx = document.getElementById('chartContainer').getContext('2d');

                                    let chart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: pogroupMax,
                                            datasets: [{
                                                    label: "Max",
                                                    backgroundColor: barColorsMax,
                                                    borderColor: barColorsMax,
                                                    borderWidth: 1,
                                                    data: revenuesMax,
                                                },
                                                {
                                                    label: "Min",
                                                    backgroundColor: barColorsMin,
                                                    borderColor: barColorsMin,
                                                    borderWidth: 1,
                                                    data: revenuesMin,
                                                    hidden: true,
                                                }
                                            ]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                legend: {
                                                    display: true,
                                                    position: 'top',
                                                    labels: {
                                                        color: "white" 
                                                    },
                                                    onClick: function(e, legendItem, legend) {
                                                        const label = legendItem.text;

                                                        if (label === "Max") {
                                                            chart.data.labels = pogroupMax;
                                                            chart.data.datasets[0].hidden = false;
                                                            chart.data.datasets[1].hidden = true;
                                                        } else if (label === "Min") {
                                                            chart.data.labels = pogroupMin;
                                                            chart.data.datasets[0].hidden = true;
                                                            chart.data.datasets[1].hidden = false;
                                                        }

                                                        chart.update();
                                                    }
                                                }
                                            },
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        color: "white" 
                                                    }
                                                },
                                                x: {
                                                    ticks: {
                                                        color: "white" 
                                                    }
                                                }
                                            },
                                            onClick: (e, elements) => {
                                                if (elements.length > 0) {
                                                    const index = elements[0].index;
                                                    const selectedGroup = chart.data.labels[index];
                                                    window.location.href = `po_group_details.php?po_group=${encodeURIComponent(selectedGroup)}`;
                                                }
                                            }
                                        }
                                    });
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </main>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="assets/demo/chart-pie-demo.js"></script>
</body>

</html>