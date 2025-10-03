<?php 
include 'header.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cust_id = !empty($_POST['cust_id']) ? intval($_POST['cust_id']) : NULL;
    $medicines = isset($_POST['medicines']) ? $_POST['medicines'] : [];
    $quantities = isset($_POST['quantities']) ? $_POST['quantities'] : [];
    $total = 0;
    $items_to_insert = [];
    
    // Validate and calculate total
    if(empty($medicines) || empty($quantities)) {
        $error = "Please select at least one medicine!";
    } else {
        foreach($medicines as $key => $med_id) {
            if(!empty($med_id) && !empty($quantities[$key]) && intval($quantities[$key]) > 0) {
                $med_id = intval($med_id);
                $qty = intval($quantities[$key]);
                
                $med_query = mysqli_query($conn, "SELECT med_price, med_quantity, med_name FROM medicines WHERE med_id=$med_id");
                if(mysqli_num_rows($med_query) > 0) {
                    $med = mysqli_fetch_assoc($med_query);
                    
                    if($med['med_quantity'] < $qty) {
                        $error = "Insufficient stock for " . $med['med_name'] . "! Available: " . $med['med_quantity'];
                        break;
                    }
                    
                    $item_total = $med['med_price'] * $qty;
                    $total += $item_total;
                    $items_to_insert[] = [
                        'med_id' => $med_id,
                        'qty' => $qty,
                        'price' => $med['med_price'],
                        'total' => $item_total
                    ];
                }
            }
        }
    }
    
    if(empty($error) && $total > 0 && count($items_to_insert) > 0) {
        // Insert sale
        $emp_id = $_SESSION['user_id'];
        if($cust_id) {
            $sale_query = "INSERT INTO sales (cust_id, emp_id, total_amount) VALUES ($cust_id, $emp_id, $total)";
        } else {
            $sale_query = "INSERT INTO sales (cust_id, emp_id, total_amount) VALUES (NULL, $emp_id, $total)";
        }
        
        if(mysqli_query($conn, $sale_query)) {
            $sale_id = mysqli_insert_id($conn);
            
            // Insert sale items
            foreach($items_to_insert as $item) {
                $insert_item = "INSERT INTO sales_items (sale_id, med_id, quantity, price, total) 
                               VALUES ($sale_id, {$item['med_id']}, {$item['qty']}, {$item['price']}, {$item['total']})";
                mysqli_query($conn, $insert_item);
            }
            
            $success = "Sale completed successfully! Sale ID: #$sale_id";
        } else {
            $error = "Error processing sale. Please try again.";
        }
    } elseif(empty($error) && $total == 0) {
        $error = "Please add valid medicines and quantities!";
    }
}

$medicines_list = mysqli_query($conn, "SELECT * FROM medicines WHERE med_quantity > 0 ORDER BY med_name");
$customers_list = mysqli_query($conn, "SELECT * FROM customers ORDER BY cust_name");
?>

<h2>🛒 Make New Sale</h2>

<?php if($success): ?>
    <div class="success-msg"><?php echo htmlspecialchars($success); ?> <a href="sales.php">View Sales</a></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="" id="saleForm">
        <div class="form-group">
            <label>Customer (Optional)</label>
            <select name="cust_id">
                <option value="">Walk-in Customer</option>
                <?php while($cust = mysqli_fetch_assoc($customers_list)): ?>
                <option value="<?php echo $cust['cust_id']; ?>"><?php echo htmlspecialchars($cust['cust_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <h3>Select Medicines</h3>
        <div id="medicineRows">
            <div class="medicine-row">
                <div class="form-group">
                    <label>Medicine</label>
                    <select name="medicines[]" class="medicine-select" onchange="updatePrice(this)">
                        <option value="">Select Medicine</option>
                        <?php 
                        mysqli_data_seek($medicines_list, 0);
                        while($med = mysqli_fetch_assoc($medicines_list)): 
                        ?>
                        <option value="<?php echo $med['med_id']; ?>" 
                                data-price="<?php echo $med['med_price']; ?>" 
                                data-stock="<?php echo $med['med_quantity']; ?>">
                            <?php echo htmlspecialchars($med['med_name']); ?> 
                            (₹<?php echo number_format($med['med_price'], 2); ?>) 
                            - Stock: <?php echo $med['med_quantity']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantities[]" min="1" class="quantity-input" onchange="calculateTotal()">
                </div>
            </div>
        </div>
        
        <button type="button" onclick="addMedicineRow()" class="btn-secondary">+ Add More Medicine</button>
        
        <div class="total-section">
            <h3>Total Amount: ₹<span id="totalAmount">0.00</span></h3>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Complete Sale</button>
            <a href="dashboard.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function addMedicineRow() {
    const container = document.getElementById('medicineRows');
    const firstRow = container.querySelector('.medicine-row');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(input => input.value = '');
    container.appendChild(newRow);
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.medicine-row').forEach(row => {
        const select = row.querySelector('.medicine-select');
        const quantityInput = row.querySelector('.quantity-input');
        const quantity = quantityInput.value;
        
        if(select.value && quantity && quantity > 0) {
            const price = parseFloat(select.options[select.selectedIndex].dataset.price);
            const stock = parseInt(select.options[select.selectedIndex].dataset.stock);
            
            if(parseInt(quantity) > stock) {
                quantityInput.value = stock;
                alert('Quantity adjusted to available stock: ' + stock);
            }
            
            total += price * parseInt(quantityInput.value || 0);
        }
    });
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

function updatePrice(select) {
    calculateTotal();
}

// Validate before submit
document.getElementById('saleForm').addEventListener('submit', function(e) {
    let hasValidItem = false;
    document.querySelectorAll('.medicine-row').forEach(row => {
        const med = row.querySelector('.medicine-select').value;
        const qty = row.querySelector('.quantity-input').value;
        if(med && qty && qty > 0) {
            hasValidItem = true;
        }
    });
    
    if(!hasValidItem) {
        e.preventDefault();
        alert('Please select at least one medicine with quantity!');
    }
});
</script>

<?php include 'footer.php'; ?>