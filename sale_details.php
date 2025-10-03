<?php 
include 'header.php';

$sale_id = intval($_GET['id']);

$sale = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT s.*, c.cust_name, c.cust_phone, p.name as emp_name 
    FROM sales s 
    LEFT JOIN customers c ON s.cust_id = c.cust_id 
    LEFT JOIN pharmacist p ON s.emp_id = p.id 
    WHERE s.sale_id = $sale_id
"));

$items = mysqli_query($conn, "
    SELECT si.*, m.med_name, m.med_type 
    FROM sales_items si 
    JOIN medicines m ON si.med_id = m.med_id 
    WHERE si.sale_id = $sale_id
");
?>

<h2>📄 Sale Details - Invoice #<?php echo $sale_id; ?></h2>

<div class="invoice-container">
    <div class="invoice-header">
        <h3>Pharmacia</h3>
        <p>Invoice #<?php echo $sale_id; ?></p>
        <p>Date: <?php echo date('d M Y, h:i A', strtotime($sale['sale_date'])); ?></p>
    </div>
    
    <div class="invoice-info">
        <div>
            <strong>Customer:</strong> <?php echo $sale['cust_name'] ?? 'Walk-in Customer'; ?><br>
            <?php if($sale['cust_phone']): ?>
            <strong>Phone:</strong> <?php echo $sale['cust_phone']; ?>
            <?php endif; ?>
        </div>
        <div>
            <strong>Served By:</strong> <?php echo $sale['emp_name']; ?>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Medicine</th>
                <th>Type</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td><?php echo $item['med_name']; ?></td>
                <td><?php echo $item['med_type']; ?></td>
                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₹<?php echo number_format($item['total'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                <td><strong>₹<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="form-actions">
        <a href="sales.php" class="btn-secondary">Back to Sales</a>
        <button onclick="window.print()" class="btn-primary">Print Invoice</button>
    </div>
</div>

<?php include 'footer.php'; ?>