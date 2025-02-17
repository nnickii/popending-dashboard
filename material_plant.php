<?php
include 'connection.php';

$mat_desc = $_GET['mat_desc'] ?? '';
$filterDate = $_GET['filterDate'] ?? '';

try {

    $sqlMatDesc = "SELECT DISTINCT mat_desc, SUM(order_qty) AS total_ordered 
                   FROM po_pending 
                   GROUP BY mat_desc 
                   ORDER BY total_ordered DESC 
                   LIMIT 10";
    $query = $conn->prepare($sqlMatDesc);
    $query->execute();
    $matDesc = $query->fetchAll(PDO::FETCH_ASSOC);


    $sql = "SELECT * FROM po_pending WHERE 1";


    if (!empty($mat_desc)) {
        $sql .= " AND mat_desc = :mat_desc";
    }
    if (!empty($filterDate)) {
        $sql .= " AND DATE(created_on) = :filterDate"; 
    }

    $query = $conn->prepare($sql);

    if (!empty($mat_desc)) {
        $query->bindParam(':mat_desc', $mat_desc, PDO::PARAM_STR);
    }
    if (!empty($filterDate)) {
        $query->bindParam(':filterDate', $filterDate, PDO::PARAM_STR);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgba(118, 118, 128, 0.2);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 14px;
        }

        table.display {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
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
        <a class="nav-link" href="index.php" style="text-decoration: none;">
            <h2 class="text-center" style="color: #495057; text-align: center;"><?php echo htmlspecialchars($mat_desc); ?></h2>
        </a>
        <form method="GET" action="">
            <label for="filterDate">Date:</label>
            <input type="date" id="filterDate" name="filterDate" value="<?php echo htmlspecialchars($filterDate); ?>">
            <label for="mat_desc">Material Desc:</label>
            <select name="mat_desc" id="mat_desc">
                <option value="">Select Materials</option>
                <?php foreach ($matDesc as $mats): ?>
                    <option value="<?php echo htmlspecialchars($mats['mat_desc']); ?>" <?php echo $mat_desc === $mats['mat_desc'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mats['mat_desc']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="padding: 5px;">Search</button>
        </form>

        <?php if (count($results) > 0): ?>
            <table id="matsTable" class="display">
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dataTable = new simpleDatatables.DataTable("#matsTable", {
                perPage: 12,
                perPageSelect: false,
                searchable: true,
            });
        });
    </script>
</body>

</html>