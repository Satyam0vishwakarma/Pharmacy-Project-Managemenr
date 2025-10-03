<?php 
include 'header.php';
requireAdmin();

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $expiry = $_POST['expiry'];
    
    $query = "INSERT INTO medicines (med_name, med_type, med_price, med_quantity, expiry_date) 
              VALUES ('$name', '$type', $price, $quantity, '$expiry')";
    
    if(mysqli_query($conn, $query)) {
        $success = "Medicine added successfully!";
    } else {
        $error = "Error adding medicine!";
    }
}
?>

<h2>➕ Add New Medicine</h2>

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
            <input type="text" name="name" required>
        </div>
        
        <div class="form-group">
            <label>Type *</label>
            <select name="type" required>
                <option value="">Select Type</option>
                <option value="Tablet">Tablet</option>
                <option value="Capsule">Capsule</option>
                <option value="Syrup">Syrup</option>
                <option value="Injection">Injection</option>
                <option value="Cream">Cream</option>
                <option value="Drops">Drops</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Price (₹) *</label>
            <input type="number" name="price" step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
            <label>Quantity *</label>
            <input type="number" name="quantity" min="0" required>
        </div>
        
        <div class="form-group">
            <label>Expiry Date *</label>
            <input type="date" name="expiry" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Add Medicine</button>
            <a href="medicines.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>