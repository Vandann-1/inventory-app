<?php
ini_set('display_errors', 'Off'); // Not to show errors on page
session_start();
require 'api.php';

// Check Admin_Login session
if (!isset($_SESSION['Admin_Login']) && !isset($_SESSION['token']) && $_SESSION['Admin_Login'] == '' && $_SESSION['Admin_Login'] != 'yes') {
    // Redirect to login page
    header('Location: login');
    exit();
}

$categories = '';
$token = $_SESSION['token'];
$response = sendRequestToDjango('categories/', [], $token, 'GET');

if (isset($response['error'])) {
    echo "Error: " . $response['error'];
} else {
    $categories = $response['Categories'];
    //$categoryCount = count($categories); // Get total number of categories
}

// To show success msg after creation of user and reload
if (isset($_SESSION['success_msg'])) {
    $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>"
        . $_SESSION['success_msg'] .
        "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
    unset($_SESSION['success_msg']); // Clear the session after storing in a variable
}

// Check if action and requestid are set
if (isset($_GET['ac']) && !empty($_GET['ac']) && isset($_GET['requestid']) && !empty($_GET['requestid'])) {
    $action = filter_input(INPUT_GET, 'ac', FILTER_SANITIZE_SPECIAL_CHARS);
    $request_id = filter_input(INPUT_GET, 'requestid', FILTER_SANITIZE_SPECIAL_CHARS);
    $type = filter_input(INPUT_GET, 't', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($action == 'status') {
        // Sending data to Django
        $response = sendRequestToDjango('active_inactive/', [
            "type" => "categories",
            "action" => $type, // active or inactive
            "request_code" => $request_id
        ], $_SESSION['token']);

        // Handling the response
        if ($response) {
            $_SESSION['success_msg'] = htmlspecialchars($response['message']);
            $file_name = basename($_SERVER['PHP_SELF'], ".php"); // Get the filename without extension
            header("location: $file_name"); // Redirect without .php
        } else {
            $msg = "An error occurred while processing your request.";
        }
    }

    if ($action == "delete") {
        $categoryCode = $request_id;

        if (!empty($categoryCode)) {
            $response = sendRequestToDjango('bulk_delete/', [
                "type" => "categories",
                'deletion_codes' => [$categoryCode] 
            ], $_SESSION['token']);

            if (isset($response['success']) && $response['success'] === true) {
                $_SESSION['success_msg'] = htmlspecialchars($response['message']);
                session_write_close();
                $file_name = basename($_SERVER['PHP_SELF'], ".php");
                header("location: $file_name");
                exit();
            } else {
                $msg = "<div class='alert alert-danger'>Error: " . htmlspecialchars($response['message'] ?? 'Unknown error') . "</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>No category selected for deletion.</div>";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['selected_categories'])) {
        $selectedCategories = $_POST['selected_categories'];

        $response = sendRequestToDjango('bulk_delete/', [
            "type" => "categories",
            'deletion_codes' => $selectedCategories
        ], $_SESSION['token']);

        if (isset($response['success']) && $response['success'] === true) {
            $_SESSION['success_msg'] =  htmlspecialchars($response['message']);
            session_write_close(); // to ensure that session is saved before redirect
            $file_name = basename($_SERVER['PHP_SELF'], ".php"); // Get the filename without extension
            header("location: $file_name"); // Redirect without .php
            exit();
        } else {
            $msg = "<div class='alert alert-danger'>Error: " . htmlspecialchars($response['message'] ?? 'Unknown error') . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>No category selected for deletion.</div>";
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
    <title>Category | TS</title>

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
                        <i class="fa-solid fa-user"></i>
                        <span class="status online"></span></span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profilename">
                            <div class="profileset">
                                <i class="fa-solid fa-user"></i>
                                <span class="status online"></span></span>
                                <div class="profilesets">
                                    <h6><?= htmlspecialchars($_SESSION['username']); ?></h6>
                                </div>
                            </div>
                            <hr class="m-0">
                            <a class="dropdown-item" href="profile"> <i class="me-2" data-feather="user"></i> My Profile</a>
                            <a class="dropdown-item" href="generalsettings"><i class="me-2" data-feather="settings"></i>Settings</a>
                            <hr class="m-0">
                            <a class="dropdown-item logout pb-0" href="logout"><img src="assets/img/icons/log-out.svg" class="me-2" alt="img">Logout</a>
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
                            <a href="javascript:void(0);" class="active"><img src="assets/img/icons/product.svg" alt="img"><span> Product</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="productlist">Product List</a></li>
                                <li><a href="addproduct">Add Product</a></li>
                                <li><a href="categorylist" class="active">Category List</a></li>
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
                                <a href="javascript:void(0);"><img src="assets/img/icons/users1.svg" alt="img"><span> Users</span> <span class="menu-arrow"></span></a>
                                <ul>
                                    <li><a href="newuser">New User </a></li>
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
                        <h4>Product Category list</h4>
                        <h6>View/Search product Category</h6>
                    </div>
                    <div class="page-btn">
                        <a href="addcategory" class="btn btn-added">
                            <img src="assets/img/icons/plus.svg" class="me-1" alt="img">Add Category
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-top">
                            <div class="search-set">
                                <div class="search-path">
                                    <a class="btn btn-filter" id="filter_search">
                                        <img src="assets/img/icons/filter.svg" alt="img">
                                        <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                    </a>
                                </div>
                                <div class="search-input">
                                    <a class="btn btn-searchset"><img src="assets/img/icons/search-white.svg" alt="img"></a>
                                </div>
                            </div>
                            <div class="wordset">
                                <ul>
                                    <li>
                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="assets/img/icons/pdf.svg" alt="img"></a>
                                    </li>
                                    <li>
                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img src="assets/img/icons/excel.svg" alt="img"></a>
                                    </li>
                                    <li>
                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img src="assets/img/icons/printer.svg" alt="img"></a>
                                    </li>
                                    <li>
                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="delete" onclick="confirmBulkDelete()"><img src="assets/img/icons/delete.svg" alt="img" id="deleteBtn"></a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card" id="filter_inputs">
                            <div class="card-body pb-0">
                                <div class="row">
                                    <div class="col-lg-2 col-sm-6 col-12">
                                        <div class="form-group">
                                            <select class="select">
                                                <option>Choose Category</option>
                                                <option>Computers</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-6 col-12">
                                        <div class="form-group">
                                            <select class="select">
                                                <option>Choose Sub Category</option>
                                                <option>Fruits</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-6 col-12">
                                        <div class="form-group">
                                            <select class="select">
                                                <option>Choose Sub Brand</option>
                                                <option>Iphone</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-sm-6 col-12 ms-auto">
                                        <div class="form-group">
                                            <a class="btn btn-filters ms-auto"><img src="assets/img/icons/search-whites.svg" alt="img"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <form method="post" id="category">
                                <table class="table  datanew">
                                    <?php if (isset($msg)) {
                                        echo $msg;
                                    } ?>
                                    <thead>
                                        <tr>
                                            <th>
                                                <label class="checkboxs">
                                                    <input type="checkbox" id="select-all">
                                                    <span class="checkmarks"></span>
                                                </label>
                                            </th>
                                            <th>Category name</th>
                                            <th>Category Code</th>
                                            <th>Description</th>
                                            <th>Created On</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category) {
                                            
                                                $createdAt = $category['created_at'] ?? null; // Ensure correct field name

                                                if (!empty($createdAt)) {
                                                    $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $createdAt) 
                                                    ?: DateTime::createFromFormat('Y-m-d H:i:s', $createdAt);

                                                } else {
                                                    $date = false;
                                                }
                                            
                                                $formatted_date = ($date) ? $date->format('Y-m-d h:i A') : "NA"; // Fallback to NA
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="checkboxs">
                                                        <input type="checkbox" class="categoryCheckbox" name="selected_categories[]" value="<?= $category['category_code']; ?>">
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                </td>
                                                <td><?= htmlspecialchars($category['name']); ?></td>
                                                <td><?= htmlspecialchars($category['custom_code']); ?></td>
                                                <td><?= htmlspecialchars($category['desc']); ?></td>
                                                <td><?= htmlspecialchars($formatted_date); ?></td><!--  Example: 2025-03-25 -->
                                                <td>
                                                    <?php if ($category['status'] == 1) { ?>
                                                        <a href='?ac=status&t=inactive&requestid=<?= htmlspecialchars($category['category_code']) ?>'>
                                                            <span class="bg-lightgreen badges">Active</span>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href='?ac=status&t=active&requestid=<?= htmlspecialchars($category['category_code']) ?>'>
                                                            <span class="bg-lightred badges">Inactive</span>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <a class="me-3" href="editcategory?editid=<?= htmlspecialchars($category['category_code']) ?>">
                                                        <img src="assets/img/icons/edit.svg" alt="img">
                                                    </a>
                                                    <a class="me-2 confirm-text" href="javascript:void(0);" name="selected_category_individual[]" data-category-code="<?= htmlspecialchars($category['category_code']); ?>">
                                                        <img src="assets/img/icons/delete.svg" alt="Delete">
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
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
    <script>
        // To delete multiple categories
        function confirmBulkDelete() {
            // Check if the button is disabled
            if (deleteBtn.style.pointerEvents === 'none') {
                return; // Do nothing if the button is disabled
            }
            const selectedUsers = document.querySelectorAll('.categoryCheckbox:checked');

            if (selectedUsers.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Users Selected',
                    text: 'Please select at least one user to delete.'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff9f43',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('category').submit();
                }
            });
        }
        // To delete single category
        document.addEventListener('click', function(e) {
            if (e.target.closest('.confirm-text')) {
                const button = e.target.closest('.confirm-text');
                const categoryCode = button.getAttribute('data-category-code');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#dc3545",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `?ac=delete&requestid=${categoryCode}`;
                    }
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            toggleDeleteButton(); // Ensure the initial state is correct
        });
        // Select all checkboxes when header checkbox is clicked
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.categoryCheckbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleDeleteButton();
        });

        // Enable or disable the Delete button (image) based on checkbox selection
        document.querySelectorAll('.categoryCheckbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                toggleDeleteButton();
            });
        });

        function toggleDeleteButton() {
            const checkedCount = document.querySelectorAll('.categoryCheckbox:checked').length;
            const deleteBtn = document.getElementById('deleteBtn');

            if (checkedCount === 0) {
                deleteBtn.style.opacity = '0.5'; // Visually disable
                deleteBtn.style.pointerEvents = 'none'; // Disable interaction
            } else {
                deleteBtn.style.opacity = '1000';
                deleteBtn.style.pointerEvents = 'auto'; // Enable interaction
            }
        }
        /* to stop displaying this error alert
        DataTables warning: table id=DataTables_Table_0 - Cannot reinitialise DataTable. For more information about this error, please see http://datatables.net/tn/3 */
        $.fn.dataTable.ext.errMode = 'log';
    </script>
</body>

</html>