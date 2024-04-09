<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\theAuth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\RoleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|


Route::get('/fetchData', [ProductController::class, 'fetchData']);
Route::post('/add-product', [ProductController::class, 'addProduct']);
*/



// login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('showLoginForm');
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('showRegisterForm');
Route::post('/register-process', [LoginController::class, 'registerProcess']);
Route::post('/login-process', [LoginController::class, 'loginProcess']);
Route::get('/home', [LoginController::class, 'homepage'])->name('homepage');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/Login-check', [LoginController::class, 'login_check_or_not'])->name('login_check_or_not');

// ->middleware(theAuth::class)



// Product Controller

Route::get('/Add-Product', [ProductController::class, 'showProductForm'])->name('showProductForm');
Route::post('/Add-Product-Process', [ProductController::class, 'addProduct'])->name('addProduct');
Route::get('/get-category', [ProductController::class, 'getCategories'])->name('getCategories');
Route::post('/get-company', [ProductController::class, 'get_company_id'])->name('get_company_id');
// 



Route::get('/products', [ProductController::class, 'showProductPage'])->name('showProductPage');
Route::get('/products-get-products-data', [ProductController::class, 'get_products_ProductPage'])->name('get_products_ProductPage');
Route::get('/products-view/{id}', [ProductController::class, 'get_Product_single'])->name('get_Product_single');




Route::get('/categories', [CategoryController::class, 'showCatgeories'])->name('showCatgeories');
Route::get('/categories-products/{id}', [ProductController::class, 'get_category_products_by_id'])->name('get_category_products_by_id');

Route::get('/about-us', [UserController::class, 'about_us_page'])->name('about_us_page');





Route::post('/navbar-search-query', [UserController::class, 'search_products'])->name('search_products');

Route::get('/products-search', [UserController::class, 'search_products_view'])->name('search_products_view');


Route::get('/forgot-password', [UserController::class, 'enter_email_forget_password'])->name('enter_email_forget_password');

Route::post('/check-email-process', [RoleController::class, 'check_forget_passowrd_email'])->name('check_forget_passowrd_email');

Route::get('/reset-password/{token}', [RoleController::class, 'reset_password_page'])->name('reset_password_page');


Route::post('/reset-password-process', [RoleController::class, 'reset_password_process'])->name('reset_password_process');


Route::get('/demo-demo', [RoleController::class, 'get_user'])->name('get_user');


Route::group(['middleware' => ['theAuth']], function () {



    Route::post('/add-to-cart', [ProductController::class, 'add_to_cart'])->name('add_to_cart')->middleware('theAuth');

    Route::get('/cart-checkout', [OrderController::class, 'cart_checkout'])->name('cart_checkout');
    Route::post('/cart-checkout-increment', [OrderController::class, 'increment_item_checkout_page'])->name('increment_item_checkout_page');
    Route::post('/cart-checkout-decrement', [OrderController::class, 'decrement_item_checkout_page'])->name('decrement_item_checkout_page');
    Route::post('/cart-checkout-item-delete', [OrderController::class, 'deleted_Cart_Item'])->name('deleted_Cart_Item');
    Route::get('/cart-checkout-place-order', [OrderController::class, 'place_order_confirmation'])->name('place_order_confirmation');
    Route::post('/change-address-process', [OrderController::class, 'set_Address_order'])->name('set_Address_order');

    Route::get('/place-order-final-process', [OrderController::class, 'final_order_process'])->name('final_order_process');



    Route::get('/add-address', [UserController::class, 'Add_address'])->name('Add_address');
    Route::post('/add-address-process', [UserController::class, 'Add_address_process'])->name('Add_address_process');
    Route::get('/get-data-address', [UserController::class, 'get_address'])->name('get_address');
    Route::get('/user-profile-page/{id}', [UserController::class, 'Profile_page'])->name('Profile_page');

    Route::get('/orders-page', [OrderController::class, 'order_list_page_show'])->name('order_list_page_show');
    Route::post('/orders-page-get-data', [OrderController::class, 'order_list_page'])->name('order_list_page');

    Route::get('/orders-page-view/{id}', [OrderController::class, 'Orders_detail_page'])->name('Orders_detail_page');

    Route::get('/user-info-profile', [UserController::class, 'user_info'])->name('user_info');

    Route::get('/change-password', [UserController::class, 'change_password_view'])->name('change_password_view');


    Route::post('/get-password-status', [UserController::class, 'current_password_check'])->name('current_password_check');

    Route::post('/change-password-process', [UserController::class, 'change_password'])->name('change_password');










});




// Route::get('/admin-datatable-order-get', [AdminController::class, 'get_data_order_datatable'])->name('get_data_order_datatable');



// Admin Routes



Route::group(['middleware' => ['theAuth']], function () {


    Route::get('/admin-dashboard', [AdminController::class, 'show_admin_login'])->name('show_admin_login');

    // Datatables routes

    Route::get('/admin-address-datatable', [AdminController::class, 'show_address_datatable'])->name('show_address_datatable');
    Route::get('/admin-datatable-address-get', [AdminController::class, 'get_data_address_datatable'])->name('get_data_address_datatable');


    Route::get('/admin-order-datatable', [AdminController::class, 'show_order_datatable'])->name('show_order_datatable');
    Route::get('/admin-datatable-order-get', [AdminController::class, 'get_data_order_datatable'])->name('get_data_order_datatable');



    Route::get('/admin-cart-datatable', [AdminController::class, 'show_cart_datatable'])->name('show_cart_datatable');
    Route::get('/admin-datatable-cart-get', [AdminController::class, 'get_data_cart_datatable'])->name('get_data_cart_datatable');



    Route::get('/admin-cart-items-datatable', [AdminController::class, 'show_cart_items_datatable'])->name('show_cart_items_datatable');
    Route::get('/admin-datatable-cart-items-get', [AdminController::class, 'get_data_cart_items_datatable'])->name('get_data_cart_items_datatable');




    Route::get('/admin-category-datatable', [AdminController::class, 'show_category_datatable'])->name('show_category_datatable');
    Route::get('/admin-datatable-category-get', [AdminController::class, 'get_data_category_datatable'])->name('get_data_category_datatable');




    Route::get('/admin-companies-datatable', [AdminController::class, 'show_companies_datatable'])->name('show_companies_datatable');
    Route::get('/admin-datatable-companies-get', [AdminController::class, 'get_data_companies_datatable'])->name('get_data_companies_datatable');



    Route::get('/admin-order-items-datatable', [AdminController::class, 'show_order_items_datatable'])->name('show_order_items_datatable');
    Route::get('/admin-datatable-order-items-get', [AdminController::class, 'get_data_order_items_datatable'])->name('get_data_order_items_datatable');



    Route::get('/admin-posts-datatable', [AdminController::class, 'show_posts_datatable'])->name('show_posts_datatable');
    Route::get('/admin-datatable-posts-get', [AdminController::class, 'get_data_posts_datatable'])->name('get_data_posts_datatable');



    Route::get('/admin-products-datatable', [AdminController::class, 'show_products_datatable'])->name('show_products_datatable');
    Route::get('/admin-datatable-products-get', [AdminController::class, 'get_data_products_datatable'])->name('get_data_products_datatable');


    Route::get('/admin-role-datatable', [AdminController::class, 'show_role_datatable'])->name('show_role_datatable');
    Route::get('/admin-datatable-role-get', [AdminController::class, 'get_data_role_datatable'])->name('get_data_role_datatable');



    Route::get('/admin-user-datatable', [AdminController::class, 'show_user_datatable'])->name('show_user_datatable');
    Route::get('/admin-datatable-user-get', [AdminController::class, 'get_data_user_datatable'])->name('get_data_user_datatable');



    // Product crud routes

    Route::post('/delete-product-table', [AdminController::class, 'Delete_Product_Record'])->name('Delete_Product_Record');

    Route::get('/edit-product-table', [AdminController::class, 'product_edit_page_show'])->name('product_edit_page_show');

    Route::post('/get-product-data-editForm', [AdminController::class, 'get_product_data'])->name('get_product_data');

    Route::post('/submit-product-data-editForm', [AdminController::class, 'edit_product_details'])->name('edit_product_details');

    Route::post('/cancel-order-btn-process', [OrderController::class, 'cancel_order_process'])->name('cancel_order_process');


    //company crud routes

    Route::post('/admin-add-company-form', [CompaniesController::class, 'add_company_form_datatable'])->name('add_company_form_datatable');

    Route::post('/admin-edit-company-form-get-data', [CompaniesController::class, 'get_company_data_by_id'])->name('get_company_data_by_id');

    Route::post('/admin-edit-company-form-submit', [CompaniesController::class, 'edit_company_details'])->name('edit_company_details');

    Route::post('/admin-delete-company-record', [CompaniesController::class, 'delete_company_by_id'])->name('delete_company_by_id');

    // category crud  routes

    Route::post('/admin-category-insert', [CategoryController::class, 'Category_insert_form'])->name('Category_insert_form');

    Route::post('/get-category-data-by-id', [CategoryController::class, 'get_category_by_id'])->name('get_category_by_id');

    Route::post('/category-edit-submit', [CategoryController::class, 'edit_category_by_id'])->name('edit_category_by_id');

    Route::post('/category-delete-admin', [CategoryController::class, 'category_delete_by_id'])->name('category_delete_by_id');


    // user crud routes

    Route::get('/get-roles-data-dropdown', [UserController::class, 'get_roles'])->name('get_roles');

    Route::post('/user-created-admin', [UserController::class, 'create_user_admin'])->name('create_user_admin');

    Route::post('/user-get-data-by-id', [UserController::class, 'get_user_by_id'])->name('get_user_by_id');

    Route::post('/user-edit-details-datatable', [UserController::class, 'edit_user_process'])->name('edit_user_process');

    Route::post('/user-delete-datatable', [UserController::class, 'user_delete_by_id'])->name('user_delete_by_id');


    // Role routes
    Route::post('/get-data-from-role-rights', [RoleController::class, 'get_data_role_rights'])->name('get_data_role_rights');

    Route::post('/roles-submit-form', [RoleController::class, 'Roles_submit'])->name('Roles_submit');

    Route::get('/get-roles-view-status', [RoleController::class, 'get_view_roles_rights'])->name('get_view_roles_rights');

    Route::post('/new-role-insert-submit', [RoleController::class, 'new_role_create'])->name('new_role_create');

    Route::get('/get-orders-count', [RoleController::class, 'order_counts_dashboard'])->name('order_counts_dashboard');




    Route::get('/order-view-page/{id}', [OrderController::class, 'get_order_info'])->name('get_order_info');


    Route::post('/change-order-status', [OrderController::class, 'change_order_status'])->name('change_order_status');


    Route::get('/admin-user-view/{id}', [OrderController::class, 'user_view_page_admin'])->name('user_view_page_admin');


    Route::get('admin-datatable-order-view-get/{id}', [OrderController::class, 'getOrders_by_userid'])->name('getOrders_by_userid');



    Route::get('/get-user-name-dropdown', [RoleController::class, 'get_user_names'])->name('get_user_names');

    Route::post('/filter-order-table', [OrderController::class, 'filter_order_table'])->name('filter_order_table');



    Route::get('/product-view-admin/{id}', [RoleController::class, 'product_view'])->name('product_view');


    Route::post('/change-payment-status', [OrderController::class, 'change_payment_status'])->name('change_payment_status');








});
