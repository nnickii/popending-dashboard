<?php
include 'connection.php';

$filterDate = $_GET['filterDate'] ?? '';
$aging = $_GET['aging'] ?? '';
$plant = $_GET['plant'] ?? '';

if (empty($plant)) {
    echo "<p>Plant is required.</p>";
    exit;
}

$conditions = ["plant = :plant"];
$params = [':plant' => $plant];

if ($aging) {
    switch ($aging) {
        case '0-15':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 0 AND 15";
            break;
        case '16-30':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 16 AND 30";
            break;
        case '31-60':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 31 AND 60";
            break;
        case '61-90':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 61 AND 90";
            break;
        case '91-120':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 91 AND 120";
            break;
        case '121-150':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 121 AND 150";
            break;
        case '151-180':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 151 AND 180";
            break;
        case '181-360':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) BETWEEN 181 AND 360";
            break;
        case '>360':
            $conditions[] = "DATEDIFF(CURDATE(), created_on) > 360";
            break;
        default:
            echo "<p>Invalid aging range.</p>";
            exit;
    }
}

if ($filterDate) {
    $conditions[] = "DATE(created_on) = :filterDate";
    $params[':filterDate'] = $filterDate;
}

$finalCondition = implode(' AND ', $conditions);

try {
    $sql = "SELECT id, po_no, po_item,po_type, mat_code, po_group, vendor_code, vendor_name,order_qty, created_on
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
    <title>Aging Plant Table</title>
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
        #aging,
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
        <a class="nav-link" href="dashboard.php?plant=<?php echo urlencode($plant); ?>&aging=<?php echo urlencode($aging); ?>&filterDate=
    <?php echo urlencode($filterDate); ?>" style="text-decoration: none;">
            <h2 class="text-center" style="color:rgb(255, 255, 255); text-align: center;">Aging
                <?php echo htmlspecialchars($aging); ?> Days for Plant <?php echo htmlspecialchars($plant); ?></h2>
        </a>
        <form method="GET" action="">
            <div style="color:rgb(255, 255, 255);">
                <label for="filterDate">Date:</label>
                <input type="hidden" name="plant" value="<?php echo htmlspecialchars($plant); ?>" />
                <input type="date" id="filterDate" name="filterDate" value="<?php echo htmlspecialchars($filterDate); ?>" />
                <label for="aging">Aging:</label>
                <select name="aging" id="aging">
                    <option value="">Select aging</option>
                    <option value="0-15" <?php echo $aging === '0-15' ? 'selected' : ''; ?>>0-15 Days</option>
                    <option value="16-30" <?php echo $aging === '16-30' ? 'selected' : ''; ?>>16-30 Days</option>
                    <option value="31-60" <?php echo $aging === '31-60' ? 'selected' : ''; ?>>31-60 Days</option>
                    <option value="61-90" <?php echo $aging === '61-90' ? 'selected' : ''; ?>>61-90 Days</option>
                    <option value="91-120" <?php echo $aging === '91-120' ? 'selected' : ''; ?>>91-120 Days</option>
                    <option value="121-150" <?php echo $aging === '121-150' ? 'selected' : ''; ?>>121-150 Days</option>
                    <option value="151-180" <?php echo $aging === '151-180' ? 'selected' : ''; ?>>151-180 Days</option>
                    <option value="181-360" <?php echo $aging === '181-360' ? 'selected' : ''; ?>>181-360 Days</option>
                    <option value=">360" <?php echo $aging === '>360' ? 'selected' : ''; ?>>>360 Days</option>
                </select>
            </div>
            <button type="submit" style="padding: 5px;">Search</button>
        </form>
        <?php if ($query->rowCount() > 0): ?>
            <table id="agingTable" class="display">
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
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dataTable = new simpleDatatables.DataTable("#agingTable", {
                perPage: 10,
                perPageSelect: false,
                searchable: true,
            });
        });
    </script>
</body>

</html>