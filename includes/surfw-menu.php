<?php

if (!defined('ABSPATH')) return;

add_action('admin_menu', 'surfw_add_menu');
add_action('wp_ajax_removeSurvey', 'removeSurvey');
add_action('wp_ajax_nopriv_removeSurvey', 'removeSurvey');

function surfw_add_menu()
{
    add_menu_page('افزونه نظر سنجی از کاربران', 'نظر سنجی', 'manage_options', 'survey-for-woocomemrce', 'surfw_mainForm', '', 25);
    add_submenu_page('survey-for-woocommerce', 'لیست نظرات کاربران', 'نتایج نظرسنجی', 'manage_options', 'surfw-result-surveys', 'ListResultSurveys');
    add_submenu_page('survey-for-woocommerce', 'تنظیمات', 'تنظیمات', 'manage_options', 'surfw-settings', 'surfw_settings');
}

function surfw_mainForm()
{
    $deleteSec = wp_create_nonce('delete-survey');
    global $wpdb;
    $error = '';
    if (isset($_POST['submit'])) {
        if (empty($_POST['name'])) {
            $error = 'نام مورد نظر سنجی خالی است';
        }
        if (!empty($_POST['name'])) {
            $survey_name = strip_tags(trim($_POST['name']));
            $wpdb->insert($wpdb->prefix . 'survey_options', [
                'survey_name' => $survey_name
            ]);
        }
    }
    $surveys = $wpdb->get_results('select * from ' . $wpdb->prefix . 'survey_options');
    ?>
    <div class="wrap"><h2>افزونه نظر سنجی از کاربران</h2>
        <div class="container-fluid">
            <button type="button" data-bs-toggle="modal" id="addButton" data-bs-target="#staticBackdrop">افزودن مورد
            </button>
            <hr>
            <table id="myTable" class="table">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>مورد نظرسنجی</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($surveys) {
                    $count = 0;
                    foreach ($surveys as $survey) {
                        $count++;
                        ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $survey->survey_name ?></td>
                            <td>
                                <button onclick="removeSurvey(<?= $survey->id ?>)">حذف</button>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="staticBackdropLabel">ثبت مورد نظر</h5>
                </div>
                <form action="#" method="post">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-12 col-xs-12">
                                    <label for="name" class="form-label">نام مورد نظر سنجی</label>
                                    <input type="text" class="form-control <?= $error !== '' ? 'is-invalid' : '' ?>"
                                           id="name" name="name">
                                    <?php
                                    if (!empty($error)) {
                                        ?>
                                        <script>
                                            document.getElementById('addButton').click()
                                        </script>
                                        <div class="invalid-feedback" style="">
                                            نام مورد نظر سنجی خالی است
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                        <button type="submit" name="submit" class="btn btn-success">ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery('#myTable').DataTable({
            'language': {
                'info': 'نمایش صفحه _PAGE_ از _PAGES_',
                'emptyTable': 'رکوردی ثبت نشده است',
                'infoEmpty': 'مشاهده 0 از 0 ورودی',
                'lengthMenu': 'نمایش _MENU_ ورودی',
                'loadingRecords': 'درحال بارگیری...',
                'processing': 'درحال پردازش...',
                'search': 'جستجو:',
                'zeroRecord': 'رکورد مشابهی یافت نشد',
                'paginate': {
                    'first': 'اولین',
                    'last': 'آخرین',
                    'next': 'بعدی',
                    'previous': 'قبلی'
                }
            }
        });

        function removeSurvey(survey_id) {
            jQuery.post('<?= admin_url('admin-ajax.php') ?>',
                {
                    'action': 'removeSurvey',
                    'security': '<?= $deleteSec ?>',
                    'survey_id': survey_id
                }, (response) => {
                    if (response === 'success') {
                        location.reload(true)
                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'خطا، با پشتیبانی تماس بگیرید',
                            showConfirmButton: false,
                            timer: 5000
                        })
                    }
                })

        }
    </script>
    <?php
}

function removeSurvey()
{
    check_ajax_referer('delete-survey', 'security');
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'survey_options', [
        'id' => strip_tags(trim($_POST['survey_id']))
    ]);
    echo 'success';
    wp_die();
}

function ListResultSurveys()
{
    global $wpdb;
    $surveys = $wpdb->get_results('select * from ' . $wpdb->prefix . 'survey_options');
    ?>
    <div class="wrap"><h2>لیست نتایج نظر سنجی</h2>
        <div class="container-fluid">
            <table id="myTable" class="table">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>مورد نظرسنجی</th>
                    <th>تعداد کاربران</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($surveys) {
                    $count = 0;
                    foreach ($surveys as $survey) {
                        $result = $wpdb->get_results('select count(user_id) as result from ' . $wpdb->prefix . 'survey_results where survey_id=' . $survey->id);
                        $count++;
                        ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $survey->survey_name ?></td>
                            <td><?= $result[0]->result ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        jQuery('#myTable').DataTable({
            'language': {
                'info': 'نمایش صفحه _PAGE_ از _PAGES_',
                'emptyTable': 'رکوردی ثبت نشده است',
                'infoEmpty': 'مشاهده 0 از 0 ورودی',
                'lengthMenu': 'نمایش _MENU_ ورودی',
                'loadingRecords': 'درحال بارگیری...',
                'processing': 'درحال پردازش...',
                'search': 'جستجو:',
                'zeroRecord': 'رکورد مشابهی یافت نشد',
                'paginate': {
                    'first': 'اولین',
                    'last': 'آخرین',
                    'next': 'بعدی',
                    'previous': 'قبلی'
                }
            }
        });
    </script>
    <?php
}

function surfw_settings()
{
    $error = false;
    $file = ABSPATH.'wp-content/plugins/survey-for-woocommerce/style.css';
    if (isset($_POST['submit'])) {
        if (empty($_POST['surveyStyle'])) {
            $error = true;
        }
        if (!empty($_POST['surveyStyle'])) {
            file_put_contents($file,$_POST['surveyStyle']);
        }
    }
    $styles = "";
    if (file_exists($file)) {
        $styles = file_get_contents($file);
    }
    ?>
    <div class="wrap"><h2>تنظیمات</h2>
        <div class="container-fluid">
            <form action="#" method="post">
                <label for="surveyStyle" class="form-label">استایل فرم نظرسنجی</label>
                <textarea name="surveyStyle" id="surveyStyle" class="form-control <?= $error === true ? 'is-invalid' : '' ?>" style="text-align: left;direction: ltr;" cols="30" rows="10"><?= $styles ?></textarea>
                <?php
                if ($error):
                    ?>
                    <div class="invalid-feedback">
                        استایل خالی است.
                    </div>
                <?php
                endif;
                ?>
                <button type="submit" name="submit" class="btn btn-success mt-2">ذخیره</button>
            </form>
        </div>
    </div>
    <?php
}