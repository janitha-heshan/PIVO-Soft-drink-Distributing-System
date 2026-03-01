<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Valid Roles (as per ReadMe)
define('ROLE_ADMIN', 'Admin');
define('ROLE_SALES_REP', 'SalesRep');
define('ROLE_SUPERVISOR', 'SalesSupervisor');
define('ROLE_STORE_MANAGER', 'StoreManager');
define('ROLE_IT_SUPPORT', 'ITSupport');
define('ROLE_FACTORY_OWNER', 'FactoryOwner');
define('ROLE_SHOP_OWNER', 'ShopOwner');

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: /PIVO-Soft-drink-Distributing-System%20-%20Copy/login.php");
        exit;
    }
}

function hasRole($role)
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireRole($allowed_roles)
{
    requireLogin();

    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        die("Access Denied: You do not have permission to view this page.");
    }
}

function getCurrentUser()
{
    return $_SESSION;
}

function getDashboardPath($role)
{
    switch ($role) {
        case 'ShopOwner':
            return 'shop_dashboard.php';
        case 'StoreManager':
            return 'manager/dashboard.php';
        case 'SalesSupervisor':
        case 'SalesRep':
            return 'logistics/dashboard.php';
        case 'Admin':
            return 'admin/dashboard.php';
        case 'FactoryOwner':
            return 'Comp/DataAnalysis/insights.php';
        default:
            return 'index.php';
    }
}
?>