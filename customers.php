<?php 
include 'header.php';
requireAdmin();

if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM customers WHERE cust_id = $id");
    header("Location: customers.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    mysqli_query($conn, "INSERT INTO customers (cust_name, cust_phone, cust_email) 
                         VALUES ('$name', '$phone', '$email')");
    header("Location: customers.php");
    exit();
}

$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $customers = mysqli_query($conn, 
        "SELECT * FROM customers 
         WHERE cust_name LIKE '%$search%' OR cust_phone LIKE '%$search%' OR cust_email LIKE '%$search%' 
         ORDER BY cust_name ASC");
} else {
    $customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY cust_name ASC");
}
?>

<h2>👥 Customers</h2>

<div class="form-container" style="max-width: 500px; margin-bottom: 30px;">
    <h3>Add New Customer</h3>
    <form method="POST" action="">
        <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email">
        </div>
        <button type="submit" class="btn-primary">Add Customer</button>
    </form>
</div>

<form method="get" action="customers.php" 
      style="margin:20px 0; display:flex; align-items:center; gap:10px; max-width:600px; flex-wrap:wrap;">
    
    🔍<input type="text" name="search" placeholder=" Search customers by name, phone, or email..." 
           value="<?php echo htmlspecialchars($search); ?>" 
           style="flex:1; padding:10px 15px; border:1px solid #ccc; border-radius:30px; font-size:15px; outline:none; transition:0.3s;" 
           onfocus="this.style.borderColor='#4A90E2';" 
           onblur="this.style.borderColor='#ccc';" />

    <button type="submit" 
            style="background:#4A90E2; color:white; padding:10px 20px; border:none; border-radius:30px; font-size:15px; cursor:pointer; transition:0.3s;"
            onmouseover="this.style.background='#357ABD';" 
            onmouseout="this.style.background='#4A90E2';">
        Search
    </button>

    <?php if(!empty($search)): ?>
        <a href="customers.php" 
           style="background:#E74C3C; color:white; padding:10px 20px; border-radius:30px; font-size:15px; text-decoration:none; transition:0.3s;"
           onmouseover="this.style.background='#C0392B';" 
           onmouseout="this.style.background='#E74C3C';">
            Clear
        </a>
    <?php endif; ?>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Registered On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($customers)): ?>
        <tr>
            <td><?php echo $row['cust_id']; ?></td>
            <td><?php echo $row['cust_name']; ?></td>
            <td><?php echo $row['cust_phone'] ? $row['cust_phone'] : 'N/A'; ?></td>
            <td><?php echo $row['cust_email'] ? $row['cust_email'] : 'N/A'; ?></td>
            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
            <td>
                <a href="customers.php?delete=<?php echo $row['cust_id']; ?>" 
                   class="btn-small btn-danger" 
                   onclick="return confirm('Delete this customer?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>  
