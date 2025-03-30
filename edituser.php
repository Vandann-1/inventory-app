<?php
ini_set('display_errors', 'Off'); // Not to show errors on page
session_start();
require 'api.php';
require 'function.inc.php';

// Check Admin_Login session
if (!isset($_SESSION['Admin_Login']) && !isset($_SESSION['token']) && $_SESSION['Admin_Login'] == '' && $_SESSION['Admin_Login'] != 'yes') {
    // Redirect to login page
    header('Location: login');
    exit();
} else {
    if ($_SESSION['role'] !== 'Admin') {
        header('Location: index');
        exit();
    }
}

// To show success msg after creation of user and reload
if (isset($_SESSION['success_msg'])) {
    $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>"
        . $_SESSION['success_msg'] .
        "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
    unset($_SESSION['success_msg']); // Clear the session after storing in a variable
}

// Check if editid are set
if (isset($_GET['editid']) && !empty($_GET['editid'])) {
    $edit_id = filter_input(INPUT_GET, 'editid', FILTER_SANITIZE_SPECIAL_CHARS);

    // Sending data to Django
    $response = sendRequestToDjango('users/?user_code=' . $edit_id, [], $_SESSION['token'], 'GET');

    // Handling the response
    if ($response) {
        $user_data = $response;

        if (isset($user_data['message'])) {
            echo htmlspecialchars($user_data['message']);
            $user_data = []; // Reset data to prevent errors
        }
    } else {
        $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            An error occurred while fetching user data.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        $user_data = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = validate_input($_POST['full_name']);
    $mobile = validate_input($_POST['mobile']);
    $email = validate_input($_POST['email']);
    $role = validate_input($_POST['role']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);

    if (empty($full_name) || empty($mobile) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
        All fields are required!
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    } else {
        if (!validateMobile($mobile)) {
            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Enter a valid Mobile Number!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        } else {
            if ($password == $confirm_password) {
                $response = sendRequestToDjango('register/', [
                    'user_code' => $edit_id,
                    'full_name' => $full_name,
                    'email' => $email,
                    'role' => $role,
                    'password' => $password,
                    'mobile_no' => $mobile
                ], $_SESSION['token']);


                if (isset($response['token']) && isset($_SESSION['token'])) {
                    $_SESSION['success_msg'] = htmlspecialchars($response['message']);
                    session_write_close(); // to ensure that session is saved before redirect
                    $file_name = basename($_SERVER['PHP_SELF'], ".php"); // Get the filename without extension
                    header("location: $file_name" . "?editid=" . urlencode($edit_id)); // Redirect without .php
                    exit();
                } else {
                    $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    " . htmlspecialchars($response['message']) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";  // default $response['message'];
                }
            } else {
                $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                The passwords you entered do not match. Please ensure both fields contain the same password.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Edit User | TS</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/animate.css">

    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/all.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>

    <div class="main-wrapper">

        <div class="header">

            <div class="header-left active">
                <a href="index" class="logo">
                    <img src="assets/img/logo.png" alt="">
                </a>
                <a href="index" class="logo-small">
                    <img src="assets/img/logo-small.png" alt="">
                </a>
                <a id="toggle_btn" href="javascript:void(0);">
                </a>
            </div>

            <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <ul class="nav user-menu">

                <li class="nav-item">
                    <div class="top-nav-search">
                        <a href="javascript:void(0);" class="responsive-search">
                            <i class="fa fa-search"></i>
                        </a>
                        <form action="#">
                            <div class="searchinputs">
                                <input type="text" placeholder="Search Here ...">
                                <div class="search-addon">
                                    <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                </div>
                            </div>
                            <a class="btn" id="searchdiv"><img src="assets/img/icons/search.svg" alt="img"></a>
                        </form>
                    </div>
                </li>


                <li class="nav-item dropdown has-arrow flag-nav">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
                        <img src="assets/img/flags/us1.png" alt="" height="20">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img src="assets/img/flags/us.png" alt="" height="16"> English
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img src="assets/img/flags/fr.png" alt="" height="16"> French
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img src="assets/img/flags/es.png" alt="" height="16"> Spanish
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <img src="assets/img/flags/de.png" alt="" height="16"> German
                        </a>
                    </div>
                </li>


                <li class="nav-item dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <img src="assets/img/icons/notification-bing.svg" alt="img"> <span class="badge rounded-pill">4</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
                            <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <li class="notification-message">
                                    <a href="activities">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img alt="" src="assets/img/profiles/avatar-02.jpg">
                                            </span>
                                            <div class="media-body flex-grow-1">
                                                <p class="noti-details"><span class="noti-title">John Doe</span> added new task <span class="noti-title">Patient appointment booking</span></p>
                                                <p class="noti-time"><span class="notification-time">4 mins ago</span></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img alt="" src="assets/img/profiles/avatar-03.jpg">
                                            </span>
                                            <div class="media-body flex-grow-1">
                                                <p class="noti-details"><span class="noti-title">Tarah Shropshire</span> changed the task name <span class="noti-title">Appointment booking with payment gateway</span></p>
                                                <p class="noti-time"><span class="notification-time">6 mins ago</span></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img alt="" src="assets/img/profiles/avatar-06.jpg">
                                            </span>
                                            <div class="media-body flex-grow-1">
                                                <p class="noti-details"><span class="noti-title">Misty Tison</span> added <span class="noti-title">Domenic Houston</span> and <span class="noti-title">Claire Mapes</span> to project <span class="noti-title">Doctor available module</span></p>
                                                <p class="noti-time"><span class="notification-time">8 mins ago</span></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img alt="" src="assets/img/profiles/avatar-17.jpg">
                                            </span>
                                            <div class="media-body flex-grow-1">
                                                <p class="noti-details"><span class="noti-title">Rolland Webber</span> completed task <span class="noti-title">Patient and Doctor video conferencing</span></p>
                                                <p class="noti-time"><span class="notification-time">12 mins ago</span></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities">
                                        <div class="media d-flex">
                                            <span class="avatar flex-shrink-0">
                                                <img alt="" src="assets/img/profiles/avatar-13.jpg">
                                            </span>
                                            <div class="media-body flex-grow-1">
                                                <p class="noti-details"><span class="noti-title">Bernardo Galaviz</span> added new task <span class="noti-title">Private chat module</span></p>
                                                <p class="noti-time"><span class="notification-time">2 days ago</span></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="activities">View all Notifications</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                        <span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="">
                            <span class="status online"></span></span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profilename">
                            <div class="profileset">
                                <span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="">
                                    <span class="status online"></span></span>
                                <div class="profilesets">
                                    <h6>John Doe</h6>
                                    <h5>Admin</h5>
                                </div>
                            </div>
                            <hr class="m-0">
                            <a class="dropdown-item" href="profile"> <i class="me-2" data-feather="user"></i> My Profile</a>
                            <a class="dropdown-item" href="generalsettings"><i class="me-2" data-feather="settings"></i>Settings</a>
                            <hr class="m-0">
                            <a class="dropdown-item logout pb-0" href="signin"><img src="assets/img/icons/log-out.svg" class="me-2" alt="img">Logout</a>
                        </div>
                    </div>
                </li>
            </ul>


            <div class="dropdown mobile-user-menu">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile">My Profile</a>
                    <a class="dropdown-item" href="generalsettings">Settings</a>
                    <a class="dropdown-item" href="signin">Logout</a>
                </div>
            </div>

        </div>


        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li>
                            <a href="index"><img src="assets/img/icons/dashboard.svg" alt="img"><span> Dashboard</span> </a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/product.svg" alt="img"><span> Product</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="productlist">Product List</a></li>
                                <li><a href="addproduct">Add Product</a></li>
                                <li><a href="categorylist">Category List</a></li>
                                <li><a href="addcategory">Add Category</a></li>
                                <li><a href="subcategorylist">Sub Category List</a></li>
                                <li><a href="subaddcategory">Add Sub Category</a></li>
                                <li><a href="brandlist">Brand List</a></li>
                                <li><a href="addbrand">Add Brand</a></li>
                                <li><a href="importproduct">Import Products</a></li>
                                <li><a href="barcode">Print Barcode</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/sales1.svg" alt="img"><span> Sales</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="saleslist">Sales List</a></li>
                                <li><a href="pos">POS</a></li>
                                <li><a href="pos">New Sales</a></li>
                                <li><a href="salesreturnlists">Sales Return List</a></li>
                                <li><a href="createsalesreturns">New Sales Return</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/purchase1.svg" alt="img"><span> Purchase</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="purchaselist">Purchase List</a></li>
                                <li><a href="addpurchase">Add Purchase</a></li>
                                <li><a href="importpurchase">Import Purchase</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/expense1.svg" alt="img"><span> Expense</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="expenselist">Expense List</a></li>
                                <li><a href="createexpense">Add Expense</a></li>
                                <li><a href="expensecategory">Expense Category</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/return1.svg" alt="img"><span> Return</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="salesreturnlist">Sales Return List</a></li>
                                <li><a href="createsalesreturn">Add Sales Return </a></li>
                                <li><a href="purchasereturnlist">Purchase Return List</a></li>
                                <li><a href="createpurchasereturn">Add Purchase Return </a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/users1.svg" alt="img"><span> People</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="supplierlist">Supplier List</a></li>
                                <li><a href="addsupplier">Add Supplier </a></li>
                                <li><a href="storelist">Store List</a></li>
                                <li><a href="addstore">Add Store</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/time.svg" alt="img"><span> Report</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="purchaseorderreport">Purchase order report</a></li>
                                <li><a href="inventoryreport">Inventory Report</a></li>
                                <li><a href="salesreport">Sales Report</a></li>
                                <li><a href="invoicereport">Invoice Report</a></li>
                                <li><a href="purchasereport">Purchase Report</a></li>
                                <li><a href="supplierreport">Supplier Report</a></li>
                                <li><a href="customerreport">Customer Report</a></li>
                            </ul>
                        </li>
                        <?php if ($_SESSION['role'] == 'Admin') { ?>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="active"><img src="assets/img/icons/users1.svg" alt="img"><span> Users</span> <span class="menu-arrow"></span></a>
                                <ul>
                                    <li><a href="newuser" class="active">New User </a></li>
                                    <li><a href="userlists">Users List</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="assets/img/icons/settings.svg" alt="img"><span> Settings</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="generalsettings">General Settings</a></li>
                                <li><a href="emailsettings">Email Settings</a></li>
                                <li><a href="paymentsettings">Payment Settings</a></li>
                                <li><a href="currencysettings">Currency Settings</a></li>
                                <li><a href="grouppermissions">Group Permissions</a></li>
                                <li><a href="taxrates">Tax Rates</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="content">
                <div class="page-header">
                    <div class="page-title">
                        <h4>User Management</h4>
                        <h6>Edit/Update User</h6>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <?php if (isset($msg)) {
                            echo $msg;
                        } ?>
                        <form method="post" id="edit_user">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" placeholder="Enter user full name" name="full_name" value="<?php if (isset($_GET['editid']) && $_GET['editid'] != '') {
                                                                                                                            echo $user_data['username'];
                                                                                                                        } ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" placeholder="Enter a user email id" name="email" value="<?php if (isset($_GET['editid']) && $_GET['editid'] != '') {
                                                                                                                            echo $user_data['email'];
                                                                                                                        } ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" name="mobile" placeholder="Enter user mobile no" pattern="[6-9][0-9]{9}" maxlength="10" value="<?php if (isset($_GET['editid']) && $_GET['editid'] != '') {
                                                                                                                            echo $user_data['mobile_no'];
                                                                                                                        } ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select class="select" name="role">
                                            <option disabled selected>Select</option>
                                            <option <?php echo (isset($user_data['role']) && $user_data['role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                            <option <?php echo (isset($user_data['role']) && $user_data['role'] === 'User') ? 'selected' : ''; ?>>User</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <div class="pass-group">
                                            <input type="password" placeholder="Enter user password" name="password" class=" pass-input">
                                            <span class="fas toggle-password fa-eye-slash"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <div class="pass-group">
                                            <input type="password" placeholder="Confirm user password" class=" pass-inputs" name="confirm_password">
                                            <span class="fas toggle-passworda fa-eye-slash"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <a class="btn btn-submit me-2" onclick="document.getElementById('edit_user').submit();">Edit</a>
                                    <a class="btn btn-cancel" href="userlists">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <script src="assets/js/feather.min.js"></script>

    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>

    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/plugins/select2/js/select2.min.js"></script>

    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>

    <script src="assets/js/script.js"></script>
</body>

</html>