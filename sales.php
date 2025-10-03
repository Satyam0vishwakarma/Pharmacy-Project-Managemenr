<?php 
include 'header.php';

$sales = mysqli_query($conn, "
    SELECT s.*, c.cust_name, p.name as emp_name 
    FROM sales s 
    LEFT JOIN customers c ON s.cust_id = c.cust_id 
    LEFT JOIN pharmacist p ON s.emp_id = p.id 
    ORDER BY s.sale_date DESC
");
?>

<h2>📊 Sales History</h2>

<table>
    <thead>
        <tr>
            <th>Sale ID</th>
            <th>Customer</th>
            <th>Employee</th>
            <th>Total Amount</th>
            <th>Date & Time</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($sales)): ?>
        <tr>
            <td>#<?php echo $row['sale_id']; ?></td>
            <td><?php echo $row['cust_name'] ?? 'Walk-in'; ?></td>
            <td><?php echo $row['emp_name']; ?></td>
            <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
            <td><?php echo date('d M Y, h:i A', strtotime($row['sale_date'])); ?></td>
            <td>
                <a href="sale_details.php?id=<?php echo $row['sale_id']; ?>" class="btn-small">View</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>