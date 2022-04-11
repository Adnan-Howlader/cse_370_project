<?php
class adminBack
{
    private $conn;

    public function __construct()
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

        $query = "select * from adminlog where admin_email='$admin_email' AND admin_pass=$admin_pass"; //checking if that email and pass exists in database
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


    function add_category($data) //taking the data from add category dashboard and updating category database
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

    function display_category()
    {
        $query = "SELECT * FROM category";
        if (mysqli_query($this->conn, $query)) {
            $return_ctg = mysqli_query($this->conn, $query);//the variable has all the information about category table
            return $return_ctg;
        }
    }
}
