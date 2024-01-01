<?php
// Replace with your database connection details
$hostname = 'mysql-database';
$username = 'user';
$password = 'supersecretpw';
$database = 'password_manager';


$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Add Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUsername']) && isset($_POST['addWebsite']) && isset($_POST['addPassword']) && isset($_POST['vaultId'])) {
    $addUsername = $_POST['addUsername'];
    $addWebsite = $_POST['addWebsite'];
    $addPassword = $_POST['addPassword'];
    $addNotes = $_POST['addNotes'];
    $vaultId = $_POST['vaultId'];

    $queryAddPassword = "INSERT INTO vault_passwords (vault_id, username, website, password, notes) 
                         VALUES ($vaultId, '$addUsername', '$addWebsite', '$addPassword', '$addNotes')";
    $resultAddPassword = $conn->query($queryAddPassword);

    if (!$resultAddPassword) {
        die("Error adding password: " . $conn->error);
    }

    // Redirect to the current page after adding the password
    header("Location: {$_SERVER['PHP_SELF']}?vault_id=$vaultId");
    exit();
}

// Edit Password
if ($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_POST['editPasswordId']) && isset($_POST['editUsername']) && isset($_POST['editPassword']) && isset($_POST['editWebsite']) && isset($_POST['vaultId'])) {
    $editUsername = $_POST['editUsername'];
    $editWebsite = $_POST['editWebsite'];
    $editPassword = $_POST['editPassword'];
    $editNotes = $_POST['editNotes'];
    $editPasswordId = $_POST['editPasswordId'];
    $vaultId = $_POST['vaultId'];

    $queryEditPassword = "UPDATE vault_passwords 
                          SET username = '$editUsername', website = '$editWebsite', 
                          password = '$editPassword', notes = '$editNotes' 
                          WHERE password_id = $editPasswordId";
    $resultEditPassword = $conn->query($queryEditPassword);

    if (!$resultEditPassword) {
        die("Error updating password: " . $conn->error);
    }

    // Redirect to the current page after updating the password
    header("Location: {$_SERVER['PHP_SELF']}?vault_id=$vaultId");
    exit();
}

// Delete Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletePasswordId']) && isset($_POST['vaultId'])) {
    $deletePasswordId = $_POST['deletePasswordId'];
    $vaultId = $_POST['vaultId'];

    $queryDeletePassword = "DELETE FROM vault_passwords WHERE password_id = $deletePasswordId";
    $resultDeletePassword = $conn->query($queryDeletePassword);

    if (!$resultDeletePassword) {
        die("Error deleting password: " . $conn->error);
    }

    // Redirect to the current page after deleting the password
    header("Location: {$_SERVER['PHP_SELF']}?vault_id=$vaultId");
    exit();
}


// Retrieve vault information
$vaultId = isset($_GET['vault_id']) ? $_GET['vault_id'] : 0;

$query = "SELECT vault_name FROM vaults WHERE vault_id = $vaultId";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$row = $result->fetch_assoc();
$vaultName = $row['vault_name'];

// Retrieve passwords for the vault
$queryPasswords = "SELECT * FROM vault_passwords WHERE vault_id = $vaultId";
$resultPasswords = $conn->query($queryPasswords);

if (!$resultPasswords) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $vaultName; ?> Vault</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<?php include '../components/nav-bar.php';?>


<div class="container mt-4">
    <h2><?php echo $vaultName; ?> Vault Passwords</h2>
    
        <!-- Add button to open a modal for adding a new password -->
    <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#addPasswordModal">
        Add Password
    </button>
    <!-- Table to display passwords -->
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for passwords..." class="form-control mb-3">
    <table class="table table-bordered" id="passwordTable">
        <thead>
        <tr>
            
            <th>Username</th>
            <th>Website</th>
            <th>Password</th>
            <th>Notes</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($rowPassword = $resultPasswords->fetch_assoc()) : ?>
            <tr data-password-id="<?php echo $rowPassword['password_id']; ?>">                
                <td><?php echo $rowPassword['username']; ?></td>
                <td><?php echo $rowPassword['website']; ?></td>
                <td type="password">
                    <details>
                        <summary class="btn btn-primary btn-sm" data-toggle="modal">Show Password</summary><br>
                        <?php echo $rowPassword['password']; ?>
                    </details>
                </td>
                <td><?php echo $rowPassword['notes']; ?></td>
                <td>
                    <!-- Edit button to open a modal for editing a password -->
                    <button class="btn btn-warning btn-sm edit-password-btn" data-toggle="modal" data-target="#editPasswordModal" data-password-notes="<?php echo $rowPassword['notes']; ?>" data-password-password="<?php echo $rowPassword['password']; ?>"  data-password-website="<?php echo $rowPassword['website']; ?>" data-password-username="<?php echo $rowPassword['username']; ?>" data-password-id="<?php echo $rowPassword['password_id']; ?>">Edit</button>

                    <!-- Delete button to open a modal for deleting a password -->
                    <button class="btn btn-danger btn-sm delete-password-btn" data-toggle="modal" data-target="#deletePasswordModal" data-password-id="<?php echo $rowPassword['password_id']; ?>">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>



<div class="modal" id="addPasswordModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add New Password</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Add form for adding a new password here -->
                <form method="POST" id="addPasswordForm">
                    <input type="hidden" id="addVaultId" name="vaultId" value="<?php echo $vaultId; ?>">
                    <div class="form-group">
                        <label for="addUsername">Username:</label>
                        <input type="text" class="form-control" id="addUsername" name="addUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="addWebsite">Website:</label>
                        <input type="text" class="form-control" id="addWebsite" name="addWebsite" required>
                    </div>
                    <div class="form-group">
                        <label for="addPassword">Password:</label>
                        <input type="password" class="form-control" id="addPassword" name="addPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="addNotes">Notes:</label>
                        <textarea class="form-control" id="addNotes" name="addNotes" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Password</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal for editing a password -->
<div class="modal" id="editPasswordModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Edit Password</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Add form for editing a password here -->
                <form method="POST" id="editPasswordForm">
                    <input type="hidden" id="editVaultId" name="vaultId" value="<?php echo $vaultId; ?>">            
                    <div class="form-group">
                        <label for="editUsername">Username:</label>
                        <input type="text" class="form-control" id="editUsername" name="editUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="editWebsite">Website:</label>
                        <input type="text" class="form-control" id="editWebsite" name="editWebsite" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Password:</label>
                        <input type="password" class="form-control" id="editPassword" name="editPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="editNotes">Notes:</label>
                        <textarea class="form-control" id="editNotes" name="editNotes" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="editPasswordId" name="editPasswordId">
                    <button type="submit" class="btn btn-warning">Update Password</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal for deleting a password -->
<div class="modal" id="deletePasswordModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Password</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <p>Are you sure you want to delete this password?</p>
                <!-- Add hidden input for password ID -->
                <form method="POST" id="deletePasswordForm">
                    <input type="hidden" id="deleteVaultId" name="vaultId" value="<?php echo $vaultId; ?>">                    
                    <input  type="hidden" id="deletePasswordId" name="deletePasswordId">
                    <button type="submit" class="btn btn-danger" id="confirmDeletePasswordBtn">Delete</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Add Bootstrap JS and Popper.js scripts here -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<!-- Add your custom JavaScript script for handling modals and row click redirection -->
<script>

function searchTable() {
    // Declare variables
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("passwordTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows
    for (i = 0; i < tr.length; i++) {
        // Skip the header row
        if (i === 0) {
            continue;
        }

        // Flag to indicate if the row should be displayed
        var shouldDisplay = false;

        // Loop through all td elements in the current row
        for (j = 0; j < tr[i].getElementsByTagName("td").length; j++) {
            td = tr[i].getElementsByTagName("td")[j];
            if (td) {
                txtValue = td.textContent || td.innerText;
                // Check if the search term is found in the current td
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    shouldDisplay = true;
                    break;  // Break out of the inner loop if a match is found in any td
                }
            }
        }

        // Set the display style based on the search result
        tr[i].style.display = shouldDisplay ? "" : "none";
    }
}


    document.addEventListener("DOMContentLoaded", function() {
        // Handle edit button click for passwords
        var editPasswordButtons = document.querySelectorAll('.edit-password-btn');
        editPasswordButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                
                document.getElementById('editPasswordId').value = button.getAttribute('data-password-id');;
                document.getElementById('editUsername').value = button.getAttribute('data-password-username');;
                document.getElementById('editWebsite').value = button.getAttribute('data-password-website');;
                document.getElementById('editPassword').value = button.getAttribute('data-password-password');;
                document.getElementById('editNotes').value = button.getAttribute('data-password-notes');;
            });
        });

        // Handle delete button click for passwords
        var deletePasswordButtons = document.querySelectorAll('.delete-password-btn');
        deletePasswordButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var passwordId = button.getAttribute('data-password-id');                
                console.log('Setting Delete Password ID to : ' + passwordId);
                 document.getElementById('deletePasswordId').value = passwordId                       
            });
        });
    });
</script>
</body>
</html>

<?php
$conn->close();
?>
