<?php
$obj_adminBack = new adminBack();
$ctg_data = $obj_adminBack->display_category();



?>

<h2>Manage Category</h2>
<?php if (isset($msg)) {
    echo $msg;
}
?>
<table class="table">

    <thead>
        <tr>
            <th>Ctg id</th>
            <th>Category</th>
            <th>Description</th>

            <th>Update/Delete</th>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($ctg = mysqli_fetch_assoc($ctg_data)) {
        ?>

            <tr>
                <td><?php echo $ctg['ctg_id']; ?></td>
                <td><?php echo $ctg['ctg_name']; ?></td>
                <td><?php echo $ctg['ctg_des']; ?></td>
                <td>
                    <a href="">Update</a>
                    <a href="">Delete</a>
                </td>

            </tr>

        <?php }
        ?>

    </tbody>

</table>