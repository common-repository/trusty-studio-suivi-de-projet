<?php
/**
 * Plugin Name: Trusty Studio - Suivi de projet
 * Description: Utile aux clients de Trusty Studio pour suivre leur projet
 * Version: 1.0.1
 * Author: Trusty Studio
 * Author URI: https://trustystudio.fr
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: trusty-studio-suivi-de-projet
 */


function trusty_studio_post_type() {
    register_post_type('trusty_studio',
        array(
            'labels' => array(
                'name' => __('Trusty Studio'),
                'singular_name' => __('Trusty Studio')
            ),
            'public' => true,
            'has_archive' => false,
            'show_in_menu' => false,
            'menu_position' => 0,
        )
    );
}
add_action('init', 'trusty_studio_post_type');

function trusty_studio_enqueue_assets() {
    $plugin_url = plugin_dir_url(__FILE__);

    wp_enqueue_style('trusty-studio-suivi-de-projet-style', $plugin_url . 'ressources/style.css');
    wp_enqueue_script('trusty-studio-suivi-de-projet-script', $plugin_url . 'ressources/script.js', array('jquery'), false, true);
}
add_action('admin_enqueue_scripts', 'trusty_studio_enqueue_assets');



// Ajout des onglets pour le Post Type en tant que menu principal
function trusty_studio_admin_menu() {
    add_menu_page(
        __('Trusty Studio Onglets'),
        __('Trusty Studio'),
        'manage_options',
        'trusty_studio_onglets',
        'trusty_studio_onglets_callback',
        'dashicons-welcome-widgets-menus',
        0
    );
}
add_action('admin_menu', 'trusty_studio_admin_menu');


function trusty_studio_dashboard_alerts() {
    $site_title = get_bloginfo('name');
    $sanitized_site_title = sanitize_file_name(sanitize_title($site_title));
    $url = "https://tma.trusty-projet.fr/{$sanitized_site_title}.json";

    $show_alert = false;
    $alert_message = '';

    $handle = @fopen($url, 'r');
    if ($handle === false) {
        $show_alert = true;
        $alert_message = __("Vous n'avez pas encore de maintenance. Contactez Trusty Studio pour en savoir plus.", "trusty-studio-suivi-de-projet");
    } else {
        fclose($handle);
        $response = wp_remote_get($url);
        $json_data = wp_remote_retrieve_body($response);

        $data = json_decode($json_data, true);

        $remaining_time = $data["remaining_time"];
        $minutes = floor($remaining_time / 60);
        $hours = floor($minutes / 60);

        if ($hours == 2) {
            $show_alert = true;
            $alert_message = __("Attention, il ne vous reste que 2h00 de maintenance.", "trusty-studio-suivi-de-projet");
        }
    }

    if ($show_alert) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo esc_html( $alert_message ); ?></p>
        </div>
        <?php
    }
}

add_action('admin_notices', 'trusty_studio_dashboard_alerts');



// Affichage des onglets et du contenu
function trusty_studio_onglets_callback() {
	
    // Traitement du formulaire de support
if (isset($_POST['submit'])) {
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    $to = 'support@trustystudio.fr'; // Remplacez par l'adresse e-mail de votre support
    $subject = 'Demande de support depuis le tableau de bord WordPress - Art et creation';
    $headers = 'From: ' . $name . ' <' . $email . '>' . "\r\n";
    $body = "Nom: " . $name . "\n";
    $body .= "Email: " . $email . "\n\n";
    $body .= "Message:\n" . $message . "\n";

    if (wp_mail($to, $subject, $body, $headers)) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Votre message a été envoyé avec succès. Notre équipe de support vous répondra dans les plus brefs délais.', 'trusty-studio-suivi-de-projet') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__("Échec de l'envoi du message. Veuillez réessayer ou contacter notre support par d'autres moyens.", "trusty-studio-suivi-de-projet") . '</p></div>';
    }
}

    $ressources = get_site_url(null, '/wp-content/plugins/plugin-trusty/ressources/', 'https');
    ?>
    <div class="wrap">
        <h1><?php _e('Trusty Studio - Suivi de projet', 'trusty-studio-suivi-de-projet'); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a href="#tab1" class="nav-tab nav-tab-active"><?php _e('Informations', 'trusty-studio-suivi-de-projet'); ?></a>
            <a href="#tab2" class="nav-tab"><?php _e('Suivi de maintenance', 'trusty-studio-suivi-de-projet'); ?></a>
            <a href="#tab3" class="nav-tab"><?php _e('Informations de contact', 'trusty-studio-suivi-de-projet'); ?></a>
            <a href="#tab4" class="nav-tab"><?php _e('Support', 'trusty-studio-suivi-de-projet'); ?></a>
          
        </h2>
        <div id="tab1" class="tab-content">
    <h2><?php _e('Documentation et tutoriels', 'trusty-studio-suivi-de-projet'); ?></h2>
    <p><?php _e("Voici quelques informations et tutoriels pour vous aider à tirer le meilleur parti de nos services de TMA.", "trusty-studio-suivi-de-projet"); ?></p>
   
    <h3><?php _e('Guide de démarrage', 'trusty-studio-suivi-de-projet'); ?></h3>
    <p><?php _e("Apprenez comment configurer et utiliser votre TMA pour votre projet WordPress.", "trusty-studio-suivi-de-projet"); ?></p>

    <h3><?php _e('Bonnes pratiques', 'trusty-studio-suivi-de-projet'); ?></h3>
    <p><?php _e("Découvrez les bonnes pratiques pour gérer votre TMA et assurer le succès de votre projet.", "trusty-studio-suivi-de-projet"); ?></p>

    <h3><?php _e('FAQ', 'trusty-studio-suivi-de-projet'); ?></h3>
    <p><?php _e("Consultez les questions fréquemment posées sur nos services de TMA et leur utilisation.", "trusty-studio-suivi-de-projet"); ?></p>
</div>
<div id="tab2" class="tab-content" style="display:none;">
            <h2><?php _e('Temps restant de leur TMA', 'trusty-studio-suivi-de-projet'); ?></h2>
            <?php
                $site_title = get_bloginfo('name');
                $sanitized_site_title = sanitize_file_name(sanitize_title($site_title));
                $url = "https://tma.trusty-projet.fr/{$sanitized_site_title}.json";

                $handle = @fopen($url, 'r');
                if ($handle !== false) {
                    fclose($handle);
                    $response = wp_remote_get($url);
                    $json_data = wp_remote_retrieve_body($response);

                    $data = json_decode($json_data, true);

                    $project_name = $data["project_name"];
                    $remaining_time = $data["remaining_time"];
                    $minutes = floor($remaining_time / 60);
                    $seconds = $remaining_time % 60;
                    $hours = floor($minutes / 60);

                    ?>

<h3><?php _e('Nom du projet:', 'trusty-studio-suivi-de-projet'); ?> <?php echo esc_html($project_name); ?></h3>
<p><?php _e('Temps restant:', 'trusty-studio-suivi-de-projet'); ?> <?php echo esc_html($hours); ?>h <?php echo esc_html($minutes % 60); ?>m <?php echo esc_html($seconds); ?>s</p>


                    <?php
                } else {
                    ?>
                    <p><?php _e("Pour commander une TMA et assurer un suivi de projet, veuillez contacter Trusty Studio.", "trusty-studio-suivi-de-projet"); ?></p>
                    <?php
                }
            ?>
        </div>

        <div id="tab3" class="tab-content" style="display:none;">
            <h2><?php _e('Informations de contact', 'trusty-studio-suivi-de-projet'); ?></h2>
            <p><?php _e('Email: contact@trustystudio.com', 'trusty-studio-suivi-de-projet'); ?></p>
            <p><?php _e('Téléphone: 01 78 71 00 25', 'trusty-studio-suivi-de-projet'); ?></p>
            <p><?php _e('Site internet: https://trustystudio.com/', 'trusty-studio-suivi-de-projet'); ?></p>
        </div>
    </div>
    <div id="tab4" class="tab-content" style="display:none;">
        <h2><?php _e('Support Trusty Studio', 'trusty-studio-suivi-de-projet'); ?></h2>
        <p><?php _e("Si vous avez besoin d'aide ou si vous avez des questions, veuillez remplir le formulaire ci-dessous pour contacter notre équipe de support.", "trusty-studio-suivi-de-projet"); ?></p>
        
        <form action="" method="post" id="support-form">
            <p>
                <label for="name"><?php _e('Nom:', 'trusty-studio-suivi-de-projet'); ?></label>
                <input type="text" name="name" id="name" required>
            </p>
            <p>
                <label for="email"><?php _e('Email:', 'trusty-studio-suivi-de-projet'); ?></label>
                <input type="email" name="email" id="email" required>
            </p>
            <p>
                <label for="message"><?php _e('Message:', 'trusty-studio-suivi-de-projet'); ?></label>
                <textarea name="message" id="message" rows="6" required></textarea>
            </p>
            <p>
                <input type="submit" name="submit" value="<?php _e('Envoyer', 'trusty-studio-suivi-de-projet'); ?>">
            </p>
        </form>
    </div>

    <?php
}
