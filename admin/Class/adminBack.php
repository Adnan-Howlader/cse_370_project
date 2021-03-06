<?php
class adminBack
{
    private $conn;

    public function __construct() ##connecting the database
    {

        $dbhost = "localhost";
        $dbuser = "root";
        $dbpass = "";
        $dbname = "ecom";

        $this->conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname); //connecting with database

        if (!$this->conn) {
            die("Failed Database connect");
        }
    }

    function admin_login($data)
    {

        $admin_email = $data['admin_email']; //user inputting admin_email and pass
        $admin_pass = $data['admin_pass'];

        $query = "select * from adminlog where admin_email='$admin_email' AND admin_pass='$admin_pass'"; //checking if that email and pass exists in database
        if (mysqli_query($this->conn, $query)) { //ran the sql
            $result = mysqli_query($this->conn, $query);
            $admin_info = mysqli_fetch_assoc($result); //if data exist we can fetch it

            if ($admin_info) { //if i get admin info fetched that means i am admin and let me in the dashboard
                header('location:dashboard.php');
                session_start(); //starting session with admin info
                $_SESSION['id'] = $admin_info['id'];
                $_SESSION['adminEmail'] = $admin_info['admin_email'];
                $_SESSION['adminPass'] = $admin_info['admin_pass'];
            } else {
                $errmsg = "Your username or Password is incorrect!"; ##data not fetched ,,so not letting into dashboard
                return $errmsg;
            }
        }
    }

    function adminLogout() //if admin logouts,destroy the variables and take back to index.php
    {
        unset($_SESSION['id']);
        unset($_SESSION['adminEmail']);
        unset($_SESSION['adminPass']);
        header('location:index.php');
    }


    function add_category($data) //taking the data from add category dashboard and updating category database,,uses add-cat.view php
    {

        $ctg_name = $data['ctg_name'];
        $ctg_des = $data['ctg_des'];
        $ctg_status = $data['ctg_status'];

        //taking the values in variable and running the query

        $query = "INSERT INTO category(ctg_name,ctg_des,ctg_status) VALUE('$ctg_name','$ctg_des',$ctg_status)";

        if (mysqli_query($this->conn, $query)) {
            $message = "Category Added Successfully.";
            return $message;
        } else {
            $message = "Category Not Added";
            return $message;
        }
    }

    function display_category() // from manage-category-view
    {
        $query = "SELECT * FROM category";
        if (mysqli_query($this->conn, $query)) {
            $return_ctg = mysqli_query($this->conn, $query); //the variable has all the information about category table
            return $return_ctg;
        }
    }

    function publish_category($id) //from manage-category-view
    { //functions and sql to change pubish or uplish status from manage-category using id of the object and updating database
        $query = "UPDATE category SET ctg_status=1 WHERE ctg_id=$id";
        mysqli_query($this->conn, $query);
    }
    function unpublish_category($id) //from manage-category view
    {
        $query = "UPDATE category SET ctg_status=0 WHERE ctg_id=$id";
        mysqli_query($this->conn, $query);
    }
    function delete_category($id)//from manage-category view
    { //delete the category from database
        $query = "DELETE FROM category WHERE ctg_id=$id";
        if (mysqli_query($this->conn, $query)) {
            $msg = "Category Deleted Successfully";
            return $msg;
        }
    }
    function getCatinfo_toupdate($id)//from edit-category view
    {
        $query = "SELECT * FROM category WHERE ctg_id=$id";
        if (mysqli_query($this->conn, $query)) {
            $cat_info = mysqli_query($this->conn, $query);
            $ct_info = mysqli_fetch_assoc($cat_info);
            return $ct_info;
        }
    }

    function update_category($receive_data)//edit_cat_view
    {
        $ctg_name = $receive_data['u_ctg_name'];
        $ctg_des = $receive_data['u_ctg_des'];
        $ctg_id = $receive_data['u_ctg_id'];

        $query = "UPDATE category SET ctg_name='$ctg_name',ctg_des='$ctg_des' WHERE ctg_id=$ctg_id";

        if (mysqli_query($this->conn, $query)) {
            $return_msg = "Category Updated Successfully!";
            return $return_msg;
        }
    }

    function p_display_category() //show the published category in add product settings in admin
    {
        $query = "SELECT * FROM category WHERE ctg_status=1";// show the published category
        if (mysqli_query($this->conn, $query)) {
            $return_ctg = mysqli_query($this->conn, $query);
            return $return_ctg;
        }
    }

    function add_product($data) //add the product in database,,add-product-view php
    {
        $pdt_name = $data['pdt_name'];
        $pdt_price = $data['pdt_price'];
        $pdt_des = $data['pdt_des'];
        $pdt_ctg = $data['pdt_ctg'];
        $pdt_img_name = $_FILES['pdt_image']['name'];
        $pdt_img_size = $_FILES['pdt_image']['size'];
        $pdt_tmp_name = $_FILES['pdt_image']['tmp_name'];
        $pdt_ext = pathinfo($pdt_img_name, PATHINFO_EXTENSION);

        $pdt_status = $data['pdt_status'];

        if ($pdt_ext == 'jpg' or $pdt_ext == 'png' or $pdt_ext == 'jpeg') {
            if ($pdt_img_size <= 2097152) {
                $query = "INSERT INTO products(pdt_name,pdt_price,pdt_des,pdt_ctg,pdt_img,pdt_status) VALUE('$pdt_name',$pdt_price,'$pdt_des',$pdt_ctg,'$pdt_img_name',$pdt_status)";

                if (mysqli_query($this->conn, $query)) {
                    move_uploaded_file($pdt_tmp_name, 'upload/' . $pdt_img_name);
                    $msg = "Product Added Successfully!";
                    return $msg;
                }
            } else {
                $msg = "Your File Size Should Be Less or Equal 2 MB!";
            }
        } else {
            $msg = "Your File Must Be a JPG or PNG File!";
            return $msg;
        }
    }

    function display_product()
    { //display product info to manage-product-view
        $query = "SELECT * FROM product_info_ctg";
        if (mysqli_query($this->conn, $query)) {
            $product = mysqli_query($this->conn, $query);
            return $product;
        }
    }

    function delete_product($id)
    { //function tp delete product from manage-product-view
        $query = "DELETE FROM products WHERE pdt_id=$id";
        if (mysqli_query($this->conn, $query)) {
            $msg = "Product Deleted Successfully!";
            return $msg;
        }
    }

    function getEditProduct_info($id)
    {
        $query = "SELECT * FROM product_info_ctg WHERE pdt_id=$id";
        if (mysqli_query($this->conn, $query)) {
            $product_info = mysqli_query($this->conn, $query);
            $pdt_data = mysqli_fetch_assoc($product_info);
            return $pdt_data;
        }
    }

    function update_product($data)
    {
        $pdt_id = $data['u_pdt_id'];
        $pdt_name = $data['u_pdt_name'];
        $pdt_price = $data['u_pdt_price'];
        $pdt_des = $data['u_pdt_des'];
        $pdt_ctg = $data['u_pdt_ctg'];
        $pdt_img_name = $_FILES['u_pdt_image']['name'];
        $pdt_img_size = $_FILES['u_pdt_image']['size'];
        $pdt_tmp_name = $_FILES['u_pdt_image']['tmp_name'];
        $pdt_ext = pathinfo($pdt_img_name, PATHINFO_EXTENSION);

        $pdt_status = $data['u_pdt_status'];

        if ($pdt_ext == 'jpg' or $pdt_ext == 'png' or $pdt_ext == 'jpeg') {
            if ($pdt_img_size <= 2097152) {
                $query = "UPDATE products SET pdt_name='$pdt_name',pdt_price=$pdt_price,pdt_des='$pdt_des',pdt_ctg=$pdt_ctg,pdt_img='$pdt_img_name',pdt_status=$pdt_status WHERE pdt_id=$pdt_id";

                if (mysqli_query($this->conn, $query)) {
                    move_uploaded_file($pdt_tmp_name, 'upload/' . $pdt_img_name);
                    $msg = "Product Updated Successfully!";
                    return $msg;
                }
            } else {
                $msg = "Your File Size Should Be Less or Equal 2 MB!";
            }
        } else {
            $msg = "Your File Must Be a JPG or PNG File!";
            return $msg;
        }
    }

    function product_by_ctg($id)//category php 
    { //in homepage when user clicks category the function shows all products by this category
        $query = "SELECT * FROM product_info_ctg WHERE ctg_id=$id";
        if (mysqli_query($this->conn, $query)) {
            $proinfo = mysqli_query($this->conn, $query);
            return $proinfo;
        }
    }

    function product_by_id($id)
    { //shows individual product by id
        $query = "SELECT * FROM product_info_ctg WHERE pdt_id=$id";
        if (mysqli_query($this->conn, $query)) {
            $proinfo = mysqli_query($this->conn, $query);
            return $proinfo;
        }
    }

    function related_product($id)
    {
        //shows same category product
        $query = "SELECT * FROM product_info_ctg WHERE ctg_id=$id ORDER BY pdt_id DESC LIMIT 1";
        if (mysqli_query($this->conn, $query)) {
            $proinfo = mysqli_query($this->conn, $query);
            return $proinfo;
        }
    }

    function ctg_by_id($id)
    {
        $query = "SELECT * FROM product_info_ctg WHERE ctg_id=$id";
        if (mysqli_query($this->conn, $query)) {
            $proinfo = mysqli_query($this->conn, $query);
            $ctg = mysqli_fetch_assoc($proinfo);
            return $ctg;
        }
    }

    function user_login($data)
    {
        $user_email = $data['user_email'];
        $user_pass = $data['user_pass'];

        $query = "SELECT * FROM users WHERE user_email='$user_email' AND user_password='$user_pass'";

        if (mysqli_query($this->conn, $query)) {
            $result = mysqli_query($this->conn, $query);
            $user_info = mysqli_fetch_assoc($result);

            if ($user_info) {
                header('location:user_profile.php');
                session_start();
                $_SESSION['user_id'] = $user_info['user_id'];
                $_SESSION['email'] = $user_info['user_email'];
                $_SESSION['user_pass'] = $user_info['user_password'];
                $_SESSION['user_name'] = $user_info['user_name'];
            } else {
                $errmsg = "Your Email or Password is incorrect!";
                return $errmsg;
            }
        }
    }

    function user_register($data)
    {
        $username = $data['username'];
        $user_firstName = $data['user_firstName'];
        $user_lastName = $data['user_lastName'];
        $useremail = $data['useremail'];
        $user_pass = $data['user_pass'];
        $user_mobile = $data['user_mobile'];
        $user_roles = $data['user_roles'];

        $get_user_data = "SELECT * FROM users WHERE user_name= '$username' or user_email= '$useremail'";
        $sent_data = mysqli_query($this->conn, $get_user_data);
        $row = mysqli_num_rows($sent_data);

        if ($row == 1) {
            $msg = "This Username or Email Already Exist!";
            return $msg;
        } else {
            if (strlen($user_mobile) < 11 or strlen($user_mobile) > 11) {
                $msg = "Your Mobile Number Should Not Be Less Than or Greater Then 11 Digit";
                return $msg;
            } else {
                $query = "INSERT INTO users(user_name,user_firstname,user_lastname,user_email,user_password,user_mobile,user_roles) VALUE('$username','$user_firstName','$user_lastName','$useremail','$user_pass',$user_mobile,$user_roles)";

                if (mysqli_query($this->conn, $query)) {
                    $msg = "Your account successfully registered!";
                    return $msg;
                }
            }
        }
    }

    function user_logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['email']);
        unset($_SESSION['user_pass']);
        unset($_SESSION['user_name']);
        header('location:user_login.php');
    }
}
