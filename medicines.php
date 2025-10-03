<?php 
include 'header.php';
requireAdmin();

// Handle delete
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM medicines WHERE med_id = $id");
    header("Location: medicines.php");
    exit();
}

// Handle search
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $medicines = mysqli_query($conn, 
        "SELECT * FROM medicines 
         WHERE med_name LIKE '%$search%' OR med_type LIKE '%$search%' 
         ORDER BY med_name ASC");
} else {
    $medicines = mysqli_query($conn, "SELECT * FROM medicines ORDER BY med_name ASC");
}
?>

<h2>💊 Medicines Inventory</h2>

<div class="actions">
    <a href="add_medicine.php" class="btn-primary">+ Add New Medicine</a>
</div>

<!-- 🔍 Professional Search Form -->
<form method="get" action="medicines.php" 
      style="margin:20px 0; display:flex; align-items:center; gap:10px; max-width:600px; flex-wrap:wrap;">
    
    🔍<input type="text" name="search" placeholder=" Search medicine by name or type..." 
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
        <a href="medicines.php" 
           style="background:#E74C3C; color:white; padding:10px 20px; border-radius:30px; font-size:15px; text-decoration:none; transition:0.3s;"
           onmouseover="this.style.background='#C0392B';" 
           onmouseout="this.style.background='#E74C3C';">
            Clear
        </a>
    <?php endif; ?>

    <!-- Back Button -->
    <a href="javascript:history.back()" 
       style="background:#2ECC71; color:white; padding:10px 20px; border-radius:30px; font-size:15px; text-decoration:none; transition:0.3s;"
       onmouseover="this.style.background='#27AE60';" 
       onmouseout="this.style.background='#2ECC71';">
        ⬅ Back
    </a>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($medicines)): ?>
        <tr>
            <td><?php echo $row['med_id']; ?></td>
            <td><?php echo $row['med_name']; ?></td>
            <td><?php echo $row['med_type']; ?></td>
            <td>₹<?php echo number_format($row['med_price'], 2); ?></td>
            <td class="<?php echo $row['med_quantity'] < 20 ? 'text-danger' : ''; ?>">
                <?php echo $row['med_quantity']; ?>
            </td>
            <td><?php echo date('d M Y', strtotime($row['expiry_date'])); ?></td>
            <td>
                <a href="edit_medicine.php?id=<?php echo $row['med_id']; ?>" class="btn-small">Edit</a>
                <a href="medicines.php?delete=<?php echo $row['med_id']; ?>" 
                   class="btn-small btn-danger" 
                   onclick="return confirm('Delete this medicine?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>  
