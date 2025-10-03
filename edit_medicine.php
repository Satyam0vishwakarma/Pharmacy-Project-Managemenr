<?php 
include 'header.php';
requireAdmin();

$id = intval($_GET['id']);
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $expiry = $_POST['expiry'];
    
    $query = "UPDATE medicines SET 
              med_name='$name', med_type='$type', med_price=$price, 
              med_quantity=$quantity, expiry_date='$expiry' 
              WHERE med_id=$id";
    
    if(mysqli_query($conn, $query)) {
        $success = "Medicine updated successfully!";
    } else {
        $error = "Error updating medicine!";
    }
}

$medicine = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE med_id=$id"));
?>

<h2>✏️ Edit Medicine</h2>

<?php if($success): ?>
    <div class="success-msg"><?php echo $success; ?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="error-msg"><?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="">
        <div class="form-group">
            <label>Medicine Name *</label>
            <input type="text" name="name" value="<?php echo $medicine['med_name']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Type *</label>
            <select name="type" required>
                <option value="Tablet" <?php echo $medicine['med_type']=='Tablet'?'selected':''; ?>>Tablet</option>
                <option value="Capsule" <?php echo $medicine['med_type']=='Capsule'?'selected':''; ?>>Capsule</option>
                <option value="Syrup" <?php echo $medicine['med_type']=='Syrup'?'selected':''; ?>>Syrup</option>
                <option value="Injection" <?php echo $medicine['med_type']=='Injection'?'selected':''; ?>>Injection</option>
                <option value="Cream" <?php echo $medicine['med_type']=='Cream'?'selected':''; ?>>Cream</option>
                <option value="Drops" <?php echo $medicine['med_type']=='Drops'?'selected':''; ?>>Drops</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Price (₹) *</label>
            <input type="number" name="price" step="0.01" value="<?php echo $medicine['med_price']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Quantity *</label>
            <input type="number" name="quantity" value="<?php echo $medicine['med_quantity']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Expiry Date *</label>
            <input type="date" name="expiry" value="<?php echo $medicine['expiry_date']; ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Update Medicine</button>
            <a href="medicines.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>