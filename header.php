<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Balloon Billing</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/cc957b8971.js" crossorigin="anonymous"></script>
    

<?php wp_head(); ?>
</head>

<body>
    <!-- for minimum height -->
    <div id="rb-page">
    
    <!-- modal menu -->
    <div id="rb-modal-menu">
        <div id="rb-modal-menu__close-button">
            <i class="fas fa-times"></i>
        </div>
        <?php
            wp_nav_menu( array( 
                'theme_location' => 'rb-handheld-menu', 
            ) );
        ?>
    </div>

    <!-- header -->
    <div class="rb-container-fullwidth header">
    
        <!-- hamburger -->
        <div class="rb-hamburger mobile" id="rb-hamburger">
            <i class="fas fa-bars"></i>
        </div>

        <!-- site_title -->
        <div class="rb-container site_title">
            <h2 class="title">Red <span class="balloon">Balloon</span></h2>
            <p class="strapline">Billing Platform</p>
        </div>

        <!-- desktop navigation -->
        <div class="rb-container rb-desktop-navigation desktop">
            <div class="menu">
                <?php
                wp_nav_menu( array( 
                    'theme_location' => 'rb-main-menu', 
                ) );
                ?>
            </div>
        </div>

    </div>

    <!-- response area --><?php
    if ($_GET['resp']) { ?>
        <div class="rb-container response-area"><?php
            if ($_GET['resp'] == 'f_val') { ?>
                <div class="response alert">
                    <p>Please check the details below</p>
                </div> <?php
            } else if ($_GET['resp'] == 'client-created') { ?>
                <div class="response success">
                    <p>Client Created</p>
                </div> <?php
            } else if ($_GET['resp'] == 'client-updated') { ?>
                <div class="response success">
                    <p>Client Updated</p>
                </div> <?php
            } else if ($_GET['resp'] == 'login-sent') {?>
                <div class="response success">
                    <p>Login Link Emailed to Client</p>
                </div> <?php
            } else if ($_GET['resp'] == 'achdel') {?>
                <div class="response success">
                    <p><?php echo count($_POST['client_archive_delete']) . ' client(s) ' . $_POST['client_arcdel_action']; ?></p>
                </div> <?php
            } else if ($_GET['resp'] == 'invoice-item-created') {?>
                <div class="response success">
                    <p>Invoice Item Created</p>
                </div> <?php
            } else if ($_GET['resp'] == 'invoice-item-updated') {?>
                <div class="response success">
                    <p>Invoice Item Updated</p>
                </div> <?php
            } else if ($_GET['resp'] == 'delete_ii') {?>
                <div class="response success">
                    <p>Invoice Item Deleted</p>
                </div> <?php
            } else if ($_GET['resp'] == 'invoice_functions') {?>
                <div class="response success"> <?php
                    if ($_POST['invoice_option'] == 'cancel') {
                        echo '<p>Invoice(s) Cancelled';
                    } else if ($_POST['invoice_option'] == 'mark_paid') {
                        echo '<p>Invoice(s) Marked as Paid';
                    } ?>
                </div> <?php
            } ?>
        </div><?php
    }
