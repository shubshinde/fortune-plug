<?php

/**
 * Dashboard Page Markup.
 */
function fortune_plug_admin_dashboard_page_markup()
{
    global $wpdb;

    $table_name = $wpdb->prefix . FORTUNE_PLUG_DB_TABLE;
    $results = $wpdb->get_results("SELECT * FROM $table_name");
?>
    <div class="fortune-plug">

        <div class="container mt-4">
            <h2>
                Fortune Plug - <span class="text-muted">Dashboard</span>
            </h2>

            <hr>

            <div class="row mt-5 pl-0 p-3">

                <div class="card shadow-sm col-lg-3 m-1">
                    <h5 class="pt-2">Total Fortunes</h5>
                    <hr>
                    <h4 class="text-center text-success"> <?php echo count($results); ?> </h4>
                </div>

                <div class="card shadow-sm col-lg-3 m-1">
                    <h5 class="pt-2">Total Viwes</h5>
                    <hr>
                    <h4 class="text-center"> 3566 </h4>
                    <small class="text-muted text-center"> Implementing in future </small>
                </div>

                <div class="card shadow-sm col-lg-3 m-1">
                    <h5 class="pt-2">Total Users</h5>
                    <hr>
                    <h4 class="text-center"> 387</h4>
                    <small class="text-muted text-center"> Implementing in future </small>
                </div>

            </div>

        </div>

    </div>
<?php
}

/**
 * List all fortunes Page Markup
 */
function fortune_plug_fortune_list_page_markup()
{
    global $wpdb;
    $table_name = $wpdb->prefix . FORTUNE_PLUG_DB_TABLE;
    
    if (isset($_GET['del_id']) && !empty($_GET['del_id'])) {

        $del_id = $_GET['del_id'];
        $wpdb->query("DELETE FROM $table_name WHERE id='$del_id'");

        echo "<script>location.replace('" . admin_url('admin.php?page=fortune-plug-fortune-list') . "');</script>";
        exit;
    }

    $table_name = $wpdb->prefix . FORTUNE_PLUG_DB_TABLE;
    $results = $wpdb->get_results("SELECT * FROM $table_name");

?>
    <div class="fortune-plug">

        <div class=" mt-4">

            <div class="mt-3 bg-white p-3 col-lg-6">

                <h2>
                    <?php esc_html_e('All Fortunes', 'fortune-plug'); ?>
                </h2>

                <div class="mb-3 mt-4">

                    <?php

                    if (count($results)) {
                    ?>
                        <table class="table table-bordered shadow">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Fortune Message</th>
                                    <th scope="col">Date Added</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($results as $index => $fortune) { ?>
                                    <tr>
                                        <td scope="row"><?php echo $fortune->id; ?> </td>
                                        <td><?php echo $fortune->fortune_message; ?></td>
                                        <td><?php echo $fortune->date_created; ?></td>
                                        <td>
                                            <a href='<?php echo admin_url('admin.php?page=fortune-plug-add-fortune&upd_id=' . $fortune->id); ?>'>
                                                <button class="btn btn-sm btn-warning mr-3" type='button'>
                                                    <span class="text-muted dashicons dashicons-edit"></span>
                                                </button>
                                            </a>
                                            <a class="delete-btn" href='<?php echo admin_url('admin.php?page=fortune-plug-fortune-list&del_id=' . $fortune->id); ?>'>
                                                <button class="btn btn-sm btn-danger" type='button'>
                                                    <span class="dashicons dashicons-trash"></span>
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    <?php } else { ?>

                        <div class=" p-3">
                            <h5>No fortunes found</h5>

                            <a href="<?php echo admin_url('admin.php?page=fortune-plug-add-fortune'); ?>">
                                <button id="createFortune" class="mt-4 btn btn-sm btn-success" name="createFortune" type="submit">Add New</button>
                            </a>
                        </div>

                    <?php } ?>

                </div>

            </div>
        </div>

    </div>
<?php
}


/**
 * Add New Fortune Page Markup.
 */
function fortune_plug_add_new_fortune_page_markup()
{
    global $wpdb;
    $table_name = $wpdb->prefix . FORTUNE_PLUG_DB_TABLE;

    // Insert into database.
    if (isset($_POST['createFortune']) && isset($_POST['fortune_message'])) {

        $fortune_message = $_POST['fortune_message'];
        $wpdb->query("INSERT INTO $table_name(fortune_message) VALUES('$fortune_message')");

        echo "<script>location.replace('" . admin_url('admin.php?page=fortune-plug-fortune-list') . "');</script>";
        exit;
    }

    // Update data
    if (isset($_POST['updateFortune'])) {

        $id = $_POST['uptid'];
        $fortune_message = $_POST['fortune_message'];
        $wpdb->query("UPDATE $table_name SET fortune_message='$fortune_message' WHERE id='$id'");

        echo "<script>location.replace('" . admin_url('admin.php?page=fortune-plug-fortune-list') . "');</script>";
        exit;
    }

?>
    <div class="fortune-plug">

        <div class="card ml-5 bg-white  mt-4">

            <?php

            if (!empty($_GET['upd_id'])) {

                $upt_id = $_GET['upd_id'];
                $fortune = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$upt_id'");

            ?>
                <!-- Update Form -->
                <form action="" class="col-lg-12 mt-4" method="POST">
                    <h2 class="mb-5">
                        <?php esc_html_e('Update Fortune', 'fortune-plug'); ?>
                    </h2>

                    <input type="text" value="<?php echo $upt_id; ?>" name="uptid" hidden>

                    <div class="form-group">
                        <textarea rows="5" cols="20" class="form-control" name="fortune_message" placeholder="Type fortune description here..."><?php echo $fortune[0]->fortune_message; ?></textarea>
                    </div>
                    <div class="form-group">
                        <button id="updateFortune" class="mt-4 btn btn-warning" name="updateFortune" type="submit">Update</button>
                    </div>
                </form>
            <?php
            } else {
            ?>
                <!-- Create Form -->
                <form action="" class="col-lg-12 mt-4" method="POST">
                    <h2 class="mb-5">
                        <?php esc_html_e('Add New Fortune', 'fortune-plug'); ?>
                    </h2>
                    <div class="form-group">
                        <textarea rows="5" cols="20" class="form-control" name="fortune_message" placeholder="Type fortune description here..."></textarea>
                    </div>
                    <div class="form-group">
                        <button id="createFortune" class="mt-4 btn btn-success" name="createFortune" type="submit">Create</button>
                    </div>
                </form>
            <?php
            }

            ?>

        </div>

    </div>
<?php
}
