<?php
include 'connection.php';

$filterDate = $_GET['filterDate'] ?? '';
$aging = $_GET['aging'] ?? '';
$plant = $_GET['plant'] ?? '';
$status = $_GET['status'] ?? '';

if (empty($plant)) {
    echo "<p>Plant is required.</p>";
    exit;
}

$conditions = ["plant = :plant"];
$params = [':plant' => $plant];

try {
    $statusQuery = $conn->prepare("SELECT DISTINCT 
        CASE 
            WHEN receive_qty < order_qty THEN 'Pending'
            WHEN receive_qty >= order_qty THEN 'Complete'
        END AS status
        FROM po_pending");
    $statusQuery->execute();
    $Status = $statusQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching statuses: " . $e->getMessage());
}

if (!empty($status)) {
    $conditions[] = "(CASE 
        WHEN receive_qty < order_qty THEN 'Pending' 
        WHEN receive_qty >= order_qty THEN 'Complete' 
    END) = :status";
    $params[':status'] = $status;
}

if (!empty($filterDate)) {
    $conditions[] = "DATE(created_on) = :filterDate";
    $params[':filterDate'] = $filterDate;
}

$finalCondition = implode(' AND ', $conditions);

try {
    $sql = "SELECT 
                id, 
                po_no,
                po_item,
                po_type,
                mat_code, po_group, 
                vendor_code, 
                vendor_name,  
                order_qty, 
                created_on, 
                receive_qty, 
                delivery_date,
                CASE 
                    WHEN receive_qty < order_qty THEN 'Pending'
                    WHEN receive_qty >= order_qty THEN 'Complete'
                END AS status
            FROM po_pending
            WHERE $finalCondition
            LIMIT 1000";

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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: rgb(118, 118, 128, 0.2);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 14px;
        }

        table.display {
            width: 100%;
            border-collapse: collapse;
            margin: 0px auto;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table.display thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            padding: 7px;
            font-weight: bold;
            text-align: left;
            color: #343a40;
        }

        table.display tbody td {
            padding: 7px;
            border-bottom: 1px solid #e3e6f0;
            text-align: left;
            color: #495057;
        }

        table.display tbody tr:hover {
            background-color: #f1f1f1;
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
            gap: 0px;
            align-items: center;
            margin-bottom: 0px;
        }

        #filterDate,
        #status,
        #searchInput {
            padding: 5px;
            font-size: 14px;
            margin-right: 5px;
        }

        .datatable-top {
            padding: 0px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            gap: 0px;
            margin-bottom: 8px;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="container-fluid px-4">
        <a class="nav-link" href="dashboard.php?plant=<?php echo urlencode($plant); ?>&status=<?php echo urlencode($status); ?>&filterDate=
    <?php echo urlencode($filterDate); ?>" style="text-decoration: none;">
            <h2 class="text-center" style="color: #495057; text-align: center;">
                <?php echo htmlspecialchars($status); ?> for Plant <?php echo htmlspecialchars($plant); ?></h2>
        </a>
        <form method="GET" action="">
            <div>
                <input type="hidden" name="plant" value="<?php echo htmlspecialchars($plant); ?>" />
                <label for="filterDate">Date:</label>
                <input type="date" id="filterDate" name="filterDate" value="<?php echo htmlspecialchars($filterDate); ?>">
            </div>
            <div>
                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="">Select status</option>
                    <?php foreach ($Status as $group): ?>
                        <option value="<?php echo htmlspecialchars($group['status']); ?>"
                            <?php echo $status === $group['status'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($group['status']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" style="padding: 5px;">Search</button>
        </form>

        <?php if (!empty($results)): ?>
            <table id="statusTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PO No</th>
                        <th>PO Item</th>
                        <th>PO Type</th>
                        <th>Mat Code</th>
                        <th>PO Group</th>
                        <th>Vendor Code</th>
                        <th>Vendor Name</th>
                        <th>Order Qty</th>
                        <th>Created On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['po_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['po_item']); ?></td>
                            <td><?php echo htmlspecialchars($row['po_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['mat_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['po_group']); ?></td>
                            <td><?php echo htmlspecialchars($row['vendor_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_qty']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_on']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
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
                const dataTable = new simpleDatatables.DataTable("#statusTable", {
                    perPage: 12,
                    perPageSelect: false,
                    searchable: true,
                });
            });
        </script>
    </div>
</body>

</html>