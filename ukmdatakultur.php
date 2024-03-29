<?php
/* 
Plugin Name: UKMdatakultur
Plugin URI: http://www.ukm-norge.no
Description: Datakultur i arrangørsystemet. Henter ut innhold fra UKM.no/arrangorer/datakultur-siden
Author: UKM Norge / Kushtrim Aliu
Version: 1.0
Author URI: http://www.ukm-norge.no
*/

use UKMNorge\Wordpress\Modul;

class UKMdatakultur extends Modul
{
    const SLUG = 'datakultur';
    static $action = 'datakultur';
    public static $path_plugin = null;

    public static function hook()
    {
        add_action('user_admin_menu', [static::class, 'meny']);
    }

    public static function meny()
    {
        // $page = add_menu_page(
        //     'Datakultur',
        //     'Datakultur',
        //     'subscriber', //Deffinerer hva slags brukerrettigheter brukeren måtte ha for å vise menyvalg "Datakultur"
        //     static::SLUG,
        //     [static::class, 'renderAdmin'],
        //     'dashicons-star-filled',
        //     46
        // );
        add_action(
            'admin_print_styles-' . $page,
            [static::class, 'scripts_and_styles']
        );

        # Legg til menyelementer og enqueue scripts + styles
        foreach (static::getSubpages() as $child) {
            $subpage = add_submenu_page(
                'datakultur',
                $child->post_title,
                $child->post_title,
                'subscriber', //Deffinerer hva slags brukerrettigheter brukeren måtte ha for å vise menyvalg "Verktøykasse"
                'UKMdatakultur_' . $child->post_name,
                [static::class, 'renderAdmin']
            );
            add_action(
                'admin_print_styles-' . $subpage,
                [static::class, 'scripts_and_styles']
            );
        }
    }

    public static function getSubpages()
    {
        // LIST UT ALLE IDÉBANKER
        global $ID_ARRANGOR;

        # Bytt til arrangor
        switch_to_blog(UKM_HOSTNAME == 'ukm.dev' ? 13 : 881);
        # Hent alle sider
        $parent_page = get_page_by_path('datakultur-kokebok');

        # Hent alle sider
        $my_wp_query = new WP_Query();
        $subpages = $my_wp_query->query(array('post_parent' => $parent_page->ID, 'post_type' => 'page', 'posts_per_page' => 100, 'orderby' => 'menu_order', 'order' => 'ASC'));

        foreach ($subpages as $subpage) {
            $subpage->meta = new stdClass();
            $subpage->meta->dashicon = $subpage->__get('dashicon');
            $subpage->meta->description = $subpage->__get('description');
        }

        # Restore til aktiv side
        restore_current_blog();

        return $subpages;
    }

    public static function scripts_and_styles()
    {
        wp_enqueue_script('WPbootstrap3_js');
        wp_enqueue_style('WPbootstrap3_css');
        wp_enqueue_style('UKMdatakultur_css', static::getPluginUrl() . 'ukmidebank.css');
    }

    public static function renderAdmin()
    {
        if( $_GET['page'] != static::SLUG ) {
            $_GET['PAGE_SLUG'] = str_replace('UKMdatakultur_', '', $_GET['page']);
            if (isset($_GET['subpage'])) {
                $_GET['PAGE_SLUG'] = $_GET['PAGE_SLUG'] . '/' . $_GET['subpage'];
            }
            static::setAction('page');
            static::addViewData('current_page', $_GET['page']);
        }

        return parent::renderAdmin();
    }
}

UKMdatakultur::init(__DIR__);
UKMdatakultur::hook();
