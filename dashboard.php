<?php include 'header.php'; ?>

<?php
// Get statistics
$total_medicines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM medicines"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM customers"))['count'];
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sales"))['count'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM medicines WHERE med_quantity < 20"))['count'];

// Get recent sales
$recent_sales = mysqli_query($conn, "
    SELECT s.sale_id, s.total_amount, s.sale_date, c.cust_name 
    FROM sales s 
    LEFT JOIN customers c ON s.cust_id = c.cust_id 
    ORDER BY s.sale_date DESC LIMIT 5
");

// Get low stock medicines
$low_stock_meds = mysqli_query($conn, "SELECT * FROM medicines WHERE med_quantity < 20 ORDER BY med_quantity ASC LIMIT 5");
?>

<h2>📊 Dashboard</h2>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $total_medicines; ?></h3>
        <p>Total Medicines</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $total_customers; ?></h3>
        <p>Total Customers</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $total_sales; ?></h3>
        <p>Total Sales</p>
    </div>
    <div class="stat-card alert">
        <h3><?php echo $low_stock; ?></h3>
        <p>Low Stock Alert</p>
    </div>
</div>

<div class="dashboard-sections">
    <div class="section">
        <h3>📋 Recent Sales</h3>
        <?php if(mysqli_num_rows($recent_sales) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($recent_sales)): ?>
                <tr>
                    <td>#<?php echo $row['sale_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['cust_name'] ?? 'Walk-in'); ?></td>
                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($row['sale_date'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="text-align: center; color: #666; padding: 20px;">No sales recorded yet.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>⚠️ Low Stock Medicines</h3>
        <?php if(mysqli_num_rows($low_stock_meds) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Type</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($low_stock_meds)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['med_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['med_type']); ?></td>
                    <td class="text-danger"><?php echo $row['med_quantity']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="text-align: center; color: #28a745; padding: 20px;">✅ All medicines have sufficient stock!</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>