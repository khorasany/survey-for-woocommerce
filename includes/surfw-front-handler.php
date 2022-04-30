<?php

if (!defined('ABSPATH')) return;

add_action('wp_ajax_addSurveyResult', 'addSurveyResult');
add_action('wp_ajax_nopriv_addSurveyResult', 'addSurveyResult');
add_action('wp_footer', 'surfw_show_survey');

function surfw_show_survey()
{
    if (is_checkout() && is_user_logged_in()) {
        global $wpdb;
        $surveys = $wpdb->get_results('select * from ' . $wpdb->prefix . 'survey_results where user_id=' . get_current_user_id());
        $survey_items = $wpdb->get_results('select * from ' . $wpdb->prefix . 'survey_options');
        if (empty($surveys) && $survey_items) {
            $surveySec = wp_create_nonce('survey-add-result');
            ?>
            <style>
                <?php
                if (is_checkout() && file_exists(ABSPATH.'wp-content/plugins/survey-for-woocommerce/style.css')) {
                        echo file_get_contents(ABSPATH.'wp-content/plugins/survey-for-woocommerce/style.css');
                    }
                 ?>
            </style>
            <script type="text/javascript">
                (async () => {
                    const inputOptions = new Promise((resolve) => {
                        setTimeout(() => {
                            resolve({
                                <?php
                                foreach ($survey_items as $survey_item) {
                                ?>
                                '<?= $survey_item->id ?>': '<?= $survey_item->survey_name ?>',
                                <?php
                                }
                                ?>

                            })
                        }, 1000)
                    })
                    const {value: survey} = await Swal.fire({
                        title: 'از چه طریقی با ما آشنا شده اید؟',
                        input: 'radio',
                        inputOptions: inputOptions,
                        width: 800,
                        confirmButtonText: 'ثبت',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'جهت شرکت در نظرسنجی یک مورد را انتخاب نمایید!'
                            }
                        }
                    })
                    if (survey) {
                        jQuery.post('<?= admin_url('admin-ajax.php') ?>',
                            {
                                'action': 'addSurveyResult',
                                'security': '<?= $surveySec ?>',
                                'survey': survey
                            }, (response) => {
                                if (response === 'success') {
                                    Swal.fire({html: 'نظر شما با موفقیت ثبت گردید.'})
                                } else if (response === 'failed') {
                                    Swal.fire({html: 'خطا در ثبت نظر!'})
                                } else if (response === 'unauthorized') {
                                    Swal.fire({html: 'اجازه دسترسی به این صفحه محدود شده است!'})
                                }
                            })
                    }
                })()
            </script>
            <?php
        }
    }
}

function addSurveyResult()
{
    check_ajax_referer('survey-add-result', 'security');
    $survey_id = (int)strip_tags(trim($_POST['survey']));
    if (is_user_logged_in() && is_numeric($survey_id)) {
        global $wpdb;
        if ($wpdb->get_results('select * from ' . $wpdb->prefix . 'survey_options where id=' . $survey_id)
            && empty($wpdb->get_row('select * from ' . $wpdb->prefix . 'survey_results where user_id=' . get_current_user_id()))) {
            $wpdb->insert($wpdb->prefix . 'survey_results',
                [
                    'survey_id' => $survey_id,
                    'user_id' => get_current_user_id()
                ]);
            echo 'success';
            wp_die();
        }
        echo 'failed';
        wp_die();
    }
    echo 'unauthorized';
    wp_die();
}