<?php
include 'connection.php';

$plant = $_GET['plant'] ?? '';
$filterDate = $_GET['filterDate'] ?? '';
$mat_desc = $_GET['mat_desc'] ?? '';

if (empty($plant)) {
    echo "<p>Plant is required.</p>";
    exit;
}
$conditions = ["plant = :plant"];
$params = [':plant' => $plant];

try {

    $sql = "SELECT DISTINCT mat_desc, SUM(order_qty) AS total_ordered 
            FROM po_pending 
            WHERE plant = :plant 
            GROUP BY mat_desc 
            ORDER BY total_ordered DESC 
            LIMIT 10;";

    $query = $conn->prepare($sql);
    $query->bindParam(':plant', $plant, PDO::PARAM_STR);
    $query->execute();
    $matDesc = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($mat_desc)) {
        $conditions[] = "mat_desc = :mat_desc";
        $params[':mat_desc'] = $mat_desc;
    }

    if (!empty($filterDate)) {
        $conditions[] = "DATE(created_on) = :filterDate";
        $params[':filterDate'] = $filterDate;
    }

    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = "SELECT id, po_no, po_item, po_type, mat_code, mat_desc, po_group, vendor_code, vendor_name, order_qty, created_on
            FROM po_pending 
            $whereClause;";

    $query = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $query->bindValue($key, $value);
    }
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Data Table Dashboard" />
    <title>Data table</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <style>
        body {
            background-image: url('../popending/assets/img/mainbg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 14px;
        }

        table.display {
            width: 100%;
            border-collapse: collapse;
            margin: 0px auto;
            background-color: transparent;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table.display thead th {
            background-color: rgba(254, 254, 255, 0.93);
            border-bottom: 2px solid #e3e6f0;
            padding: 7px;
            font-weight: bold;
            text-align: center;
            color: #343a40;
        }

        table.display tbody td {
            padding: 7px;
            border-bottom: 1px solid #e3e6f0;
            text-align: left;
            color: rgb(255, 255, 255);
        }

        table.display tbody tr:hover {
            background-color: rgba(243, 231, 177, 0.57);
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-link {
            text-decoration: none;
        }

        .nav-link:hover {
            text-decoration: none;
        }

        form {
            display: flex;
            justify-content: flex-start;
            gap: 0;
            align-items: center;
            margin-bottom: 0;
            flex-wrap: wrap;
        }

        #filterDate,
        #mat_desc,
        #searchInput {
            padding: 5px;
            font-size: 14px;
            margin-right: 5px;
        }

        .datatable-top {
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            gap: 0;
            margin: 8px 0;
        }

        .datatable-top button {
            padding: 5px 15px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .datatable-top button:hover {
            background-color: #0056b3;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            form {
                flex-direction: column;
                align-items: flex-start;
            }

            #filterDate,
            #mat_desc,
            #searchInput {
                width: 100%;
            }

            .datatable-top {
                flex-direction: row;
            }
        }

        @media (max-width: 480px) {
            .datatable-top {
                padding: 8px;
                width: 100%;
            }

            #filterDate,
            #mat_desc,
            #searchInput {
                width: 100%;
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid px-4">
        <a class="nav-link" href="dashboard.php?plant=<?php echo urlencode($plant); ?>&mat_desc=<?php echo urlencode($mat_desc); ?>&filterDate=
    <?php echo urlencode($filterDate); ?>" style="text-decoration: none;">
            <h2 class="text-center" style="color:rgb(255, 255, 255); text-align: center;">
                <?php echo htmlspecialchars($mat_desc); ?> for Plant <?php echo htmlspecialchars($plant); ?></h2>
        </a>
        <form method="GET" action="">
            <div style="color: white;">
                <input type="hidden" name="plant" value="<?php echo htmlspecialchars($plant); ?>" />
                <label for="filterDate">Date:</label>
                <input type="date" id="filterDate" name="filterDate" value="<?php echo htmlspecialchars($filterDate); ?>">
                <div>
                    <label for="mat_desc">Material Desc:</label>
                    <select name="mat_desc" id="mat_desc">
                        <option value="">Select Materials</option>
                        <?php foreach ($matDesc as $mats): ?>
                            <option value="<?php echo htmlspecialchars($mats['mat_desc']); ?>"
                                <?php echo $mat_desc === $mats['mat_desc'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mats['mat_desc']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" style="padding: 5px;">Search</button>
        </form>
        <?php if (!empty($results)): ?>
            <table id="matsTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PO No</th>
                        <th>PO Item</th>
                        <th>PO Group</th>
                        <th>Mat Code</th>
                        <th>Mat Desc</th>
                        <th>Vendor Code</th>
                        <th>Vendor Name</th>
                        <th>Order Qty</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['po_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['po_item']); ?></td>
                            <td><?php echo htmlspecialchars($row['po_group']); ?></td>
                            <td><?php echo htmlspecialchars($row['mat_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['mat_desc']); ?></td>
                            <td><?php echo htmlspecialchars($row['vendor_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_qty']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_on']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found.</p>
        <?php endif; ?>
    </div>
    <div class="datatable-top">
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dataTable = new simpleDatatables.DataTable("#matsTable", {
                    perPage: 10,
                    perPageSelect: false,
                    searchable: true,
                });
            });
        </script>
    </div>
</body>

</html>