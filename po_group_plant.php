<?php
include 'connection.php';

$filterDate = $_GET['filterDate'] ?? '';
$po_group = $_GET['po_group'] ?? '';
$plant = $_GET['plant'] ?? '';

if (empty($plant)) {
    echo "<p>Plant is required.</p>";
    exit;
}

$conditions = ["plant = :plant"];
$params = [':plant' => $plant];

try {

    $sql = "SELECT DISTINCT po_group FROM po_pending WHERE plant = :plant";
    $query = $conn->prepare($sql);
    $query->bindParam(':plant', $plant, PDO::PARAM_STR);
    $query->execute();
    $poGroups = $query->fetchAll(PDO::FETCH_ASSOC);


    if (!empty($po_group)) {
        $conditions[] = "po_group = :po_group";
        $params[':po_group'] = $po_group;
    }


    if (!empty($filterDate)) {
        $conditions[] = "DATE(created_on) = :filterDate";
        $params[':filterDate'] = $filterDate;
    }


    $finalCondition = implode(' AND ', $conditions);


    $sql = "SELECT id, po_no, po_item, po_type, mat_code, po_group, vendor_code, vendor_name, order_qty, created_on
            FROM po_pending WHERE $finalCondition";

    $query = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $query->bindParam($key, $value);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Po Group Plant Table</title>
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
            gap: 0px;
            align-items: center;
            margin-bottom: 0px;
        }

        #filterDate,
        #po_group,
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
        <a class="nav-link" href="dashboard.php?plant=<?php echo urlencode($plant); ?>&po_group=<?php echo urlencode($po_group); ?>&filterDate=
    <?php echo urlencode($filterDate); ?>" style="text-decoration: none;">
            <h2 class="text-center" style="color:rgb(255, 255, 255); text-align: center;">
                <?php echo htmlspecialchars($po_group); ?> for Plant <?php echo htmlspecialchars($plant); ?></h2>
        </a>
        <form method="GET" action="">
            <div style="color: white;">
                <input type="hidden" name="plant" value="<?php echo htmlspecialchars($plant); ?>" />
                <label for="filterDate">Date:</label>
                <input type="date" id="filterDate" name="filterDate" value="<?php echo htmlspecialchars($filterDate); ?>">
                <label for="po_group">PO Group:</label>
                <select name="po_group" id="po_group">
                    <option value="">Select PO Group</option>
                    <?php foreach ($poGroups as $group): ?>
                        <option value="<?php echo $group['po_group']; ?>" <?php echo $po_group === $group['po_group'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($group['po_group']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" style="padding: 5px;">Search</button>
        </form>
        <?php if (!empty($results)): ?>
            <table id="pogroupTable" class="display">
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
            <p>No records found for the selected criteria.</p>
        <?php endif; ?>
    </div>
    <div class="datatable-top">
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dataTable = new simpleDatatables.DataTable("#pogroupTable", {
                    perPage: 10,
                    perPageSelect: false,
                    searchable: true,
                });
            });
        </script>
    </div>
</body>

</html>