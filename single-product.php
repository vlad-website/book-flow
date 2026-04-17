<?php get_header();
if (
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'ФИО' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Цветы' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Святые' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Иконы' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Ангелы' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Виньетки' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Свечки' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Храмы' ||
    get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Природа'
) {
?>
    <style>
        .product_img {
            display: block;
            height: 180px;
        }
    </style>
<?php }

if (get_the_terms(get_the_ID(), 'product_cat')[1]->name == 'Мемориальные комплексы') {
?>
    <style>
        .product_card_img img {
            max-width: 400px;
            width: 100%;
            object-fit: cover;
        }
    </style>
<?php
}
?>
<?php $custom_product_type_bool = is_custom_product_type(get_the_ID()); ?>
<?php if ($custom_product_type_bool) {
?>
    <style>
        /* .product_card_block {
            padding-top: 50px;
        } */

        /* .product_card_img img {
            margin: 0;
            max-width: 100%;
            max-height: 100%;
            width: 100%;
            height: 100%;
        } */

        .product_order_window .order_option:last-child {
            align-items: center;
            /* white-space: nowrap; */
            /* font-size: 19px; */
        }

        /* .product_order_window .order_option:last-child .final_price_value {
            font-size: 26px;
        } */
    </style>
    <?php
    if (!has_term('Мемориальные комплексы', 'product_cat', $product_id)) {
    ?>
        <style>
            .product_order_window .order_option:last-child {
                margin-top: 120px;
                /* font-weight: normal; */
            }
        </style>
    <?php
    } else {
    ?>
        <style>
            .product_order_window .order_option:last-child {
                margin-top: 30px;
            }
        </style>
<?php
    }
} ?>
<div class="main_content">
    <?php echo get_sidebar('products-menu'); ?>
    <section class="products_section">
        <div class="page_path">
            <a href="<?php echo get_home_url(); ?>" class="prev_page">Главная</a>
            <span class="page_path_arrow">></span>
            <a href="#" class="prev_page"><?php echo get_the_terms(get_the_ID(), 'product_cat')[0]->name; ?></a>
            <span class="page_path_arrow">></span>
            <a href="<?php echo get_term_link(get_the_terms(get_the_ID(), 'product_cat')[1]->term_id, 'product_cat'); ?>" class="prev_page">
                <?php echo get_the_terms(get_the_ID(), 'product_cat')[1]->name; ?>
            </a>
            <span class="page_path_arrow">></span>
            <a href="#" class="current_page"><?php the_title(); ?></a>
        </div>
        <!-- <?php
                $order_current_counter = get_field('product_order_counter', get_the_ID());
                $order_current_counter += 1;
                update_field('product_order_counter', $order_current_counter, get_the_ID());
                ?> -->
        <div class="product_block">
            <div class="product_card_block">
                <div class="product_card_info">
                    <h1 class="product_card_title" id="product_title"><?php the_title(); ?></h1>
                    <div class="product_card_price">
                        Цена <span class="card_price_value" id="card_price_value"></span>
                    </div>
                </div>
                <div class="product_card_img">
                    <?php
                    $product_on_sale = get_the_terms(get_the_ID(), 'product_tag');
                    if (! empty($product_on_sale) && ! is_wp_error($product_on_sale)) {
                    ?>
                        <div class="prom_block">Акция</div>
                    <?php
                    }
                    ?>
                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID()); ?>" alt="" class="product_card_img_view">
                </div>
                <?php
                if (!$custom_product_type_bool) {
                ?>
                    <div class="product_card_big_descr">
                        <div class="product_card_option_title">Описание</div>
                        <div class="card_descr_text">
                            <?php echo wpautop($product->get_short_description()); ?>
                        </div>
                    </div>
                    <div class="product_card_photos">
                        <div class="product_card_option_title">
                            Примеры наших работ
                        </div>
                        <div class="photos">
                            <?php
                            $photo_num = 0;
                            $gallery_id = $product->get_gallery_image_ids();
                            foreach ($gallery_id as $photo_id) {
                                $photo_num += 1;
                            ?>
                                <div class="photo">
                                    <img src="<?php echo wp_get_attachment_url($photo_id); ?>" alt="" class="product_photo_img">
                                    <div class="photo_back" data-photo-num="<?php echo $photo_num; ?>"></div>
                                </div>
                            <?php
                            } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php
            $field_groups = acf_get_field_groups(array(
                'post_id' => get_the_ID(),
            ));

            $field_groups_1 = array();
            $field_groups_2 = array();
            $field_groups_3 = array();
            $field_groups_4 = array();
            $label = "Фильтр";
            foreach ($field_groups as $field_group) {
                $text_fields = array();
                $group_fields = array();

                if (stripos($field_group['title'], '(тип 1)')) {
                    $fields_first_type = acf_get_fields($field_group['ID']);
                    foreach ($fields_first_type as $field) {
                        if ($field['label'] === $label) {
                            $field_name = $field['name'];
                            if (get_field($field_name, get_the_ID()) == 'add') {
                                $field_groups_1[] = $field_group;
                            }
                        }
                    }
                }
                if (stripos($field_group['title'], '(тип 2)')) {
                    $fields_second_type = acf_get_fields($field_group['ID']);
                    foreach ($fields_second_type as $field) {
                        if ($field['label'] === $label) {
                            $field_name = $field['name'];
                            if (get_field($field_name, get_the_ID()) == 'add') {
                                $field_groups_2[] = $field_group;
                            }
                        }
                    }
                }
                if (stripos($field_group['title'], '(тип 3)')) {
                    $fields_third_type = acf_get_fields($field_group['ID']);
                    foreach ($fields_third_type as $field) {
                        if ($field['label'] === $label) {
                            $field_name = $field['name'];
                            if (get_field($field_name, get_the_ID()) == 'add') {
                                $field_groups_3[] = $field_group;
                            }
                        }
                    }
                }
                if (stripos($field_group['title'], '(тип 4)')) {
                    $fields_fourth_type = acf_get_fields($field_group['ID']);
                    foreach ($fields_fourth_type as $field) {
                        if ($field['label'] === $label) {
                            $field_name = $field['name'];
                            if (get_field($field_name, get_the_ID()) == 'add') {
                                $field_groups_4[] = $field_group;
                            }
                        }
                    }
                }
            }
            ?>
            <div class="product_order_block">
                <div class="product_order_window" data-is-custom-product-type="<?php echo (!$custom_product_type_bool) ? 'no' : 'yes'; ?>">
                    <h2 class="order_title">Вы выбрали</h2>
                    <div class="order_options">
                        <?php if (!$custom_product_type_bool) { ?>
                            <?php
                            for ($i = 0; $i < 1; $i++) {
                                $field_group = $field_groups_2[$i];
                            ?>
                                <div class="order_option second_type_option">
                                    <div class="title">
                                        <?= str_replace('(тип 2)', '', $field_group['title']); ?>
                                    </div>
                                    <?php $fields = acf_get_fields($field_group['ID']);
                                    $text_fields = array();
                                    $group_fields = array();
                                    foreach ($fields as $field) {
                                        if ($field['type'] === 'group') {
                                            $group_fields[] = $field;
                                        }
                                    }
                                    $sub_fields = $group_fields[0]['sub_fields'];
                                    ?>
                                    <div class="price" id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']); ?>_price" data-price="<?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[1]['name']]; ?>" data-price-add="<?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[2]['name']]; ?>">
                                        <?php
                                        $sub_field_num = 0;
                                        if (preg_replace("/_\d+/", '', $group_fields[0]['name']) != 'material') {
                                            $sub_field_num = 1;
                                        }
                                        echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[$sub_field_num]['name']]; ?>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <?php
                            foreach ($field_groups_1 as $field_group) {
                            ?>
                                <div class="order_option first_type_option">
                                    <div class="title">
                                        <?= str_replace('(тип 1)', '', $field_group['title']); ?>
                                        <div class="final_size_value">
                                            <?php $fields = acf_get_fields($field_group['ID']);
                                            $text_fields = array();
                                            $group_fields = array();
                                            foreach ($fields as $field) {
                                                if ($field['type'] === 'group') {
                                                    $group_fields[] = $field;
                                                }
                                            }
                                            $sub_fields = $group_fields[0]['sub_fields'];
                                            ?>
                                            <span id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']) . '_' . $sub_fields[0]['name']; ?>">
                                                <?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[0]['name']]; ?>
                                            </span>
                                            <!-- ,
                                        <span id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']) . '_'  . $sub_fields[1]['name']; ?>">
                                            <?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[1]['name']]; ?>
                                        </span> -->
                                        </div>
                                    </div>
                                    <div class="price" id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']); ?>_price">
                                        <?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[1]['name']]; ?>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <?php
                            for ($i = 1; $i < 3; $i++) {
                                $field_group = $field_groups_2[$i];
                            ?>
                                <div class="order_option second_type_option">
                                    <div class="title">
                                        <?= str_replace('(тип 2)', '', $field_group['title']); ?>
                                    </div>
                                    <?php $fields = acf_get_fields($field_group['ID']);
                                    $text_fields = array();
                                    $group_fields = array();
                                    foreach ($fields as $field) {
                                        if ($field['type'] === 'group') {
                                            $group_fields[] = $field;
                                        }
                                    }
                                    $sub_fields = $group_fields[0]['sub_fields'];
                                    ?>
                                    <div class="price" id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']); ?>_price" data-price="<?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[1]['name']]; ?>">
                                        <?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[1]['name']]; ?>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <?php
                            foreach ($field_groups_3 as $field_group) {
                            ?>
                                <div class="order_option third_type_option">
                                    <div class="title">
                                        <?php $fields = acf_get_fields($field_group['ID']);
                                        $text_fields = array();
                                        $group_fields = array();
                                        foreach ($fields as $field) {
                                            if ($field['type'] === 'group') {
                                                $group_fields[] = $field;
                                            }
                                        }
                                        $sub_fields = $group_fields[0]['sub_fields'];
                                        ?>
                                        <span class="decor_parameter" id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']); ?>_parameter">
                                            <?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[0]['name']]; ?>
                                        </span>
                                        <?php $price_value = get_field($group_fields[0]['name'], get_the_ID())['price_options']['price_1'];
                                        ?>
                                        <span class="decor_quantity" id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']); ?>_quantity">
                                            <?php echo mb_substr($price_value, 0, 5);  ?>
                                        </span>
                                    </div>
                                    <div class="price" id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']); ?>_price">
                                        <?php echo mb_substr($price_value, 5);  ?>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <?php
                            for ($i = 3; $i < count($field_groups_2); $i++) {
                                $field_group = $field_groups_2[$i];
                            ?>
                                <div class="order_option second_type_option">
                                    <div class="title">
                                        <?= str_replace('(тип 2)', '', $field_group['title']); ?>
                                    </div>
                                    <?php $fields = acf_get_fields($field_group['ID']);
                                    $text_fields = array();
                                    $group_fields = array();
                                    foreach ($fields as $field) {
                                        if ($field['type'] === 'group') {
                                            $group_fields[] = $field;
                                        }
                                    }
                                    $sub_fields = $group_fields[0]['sub_fields'];
                                    ?>
                                    <div class="price" id="<?php echo preg_replace("/_\d+/", '', $group_fields[0]['name']); ?>_price" data-price="<?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[1]['name']]; ?>">
                                        <?php echo get_field($group_fields[0]['name'], get_the_ID())[$sub_fields[0]['name']]; ?>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            $custom_products_group_keys = [
                                'Оформление' => 'group_679e074c69b99',
                                'Аксессуары на памятник' => 'group_679e2c72f102c',
                                'Аксессуары' => 'group_679e2c72f102c',
                                'Мемориальные комплексы' => 'group_67ab5bbc609f2'
                            ];

                            // if (get_custom_product_type('Оформление')) {
                            //     $key_field_group = 'group_679e074c69b99';
                            // }
                            // if (get_custom_product_type('Аксессуары')) {
                            //     $key_field_group = 'group_679e2c72f102c';
                            // }
                            foreach ($custom_products_group_keys as $custom_product => $key_field_group) {
                                if (get_custom_product_type($custom_product)) {
                                    $fields = acf_get_fields($key_field_group);
                                }
                            }

                            if ($fields) {
                                foreach ($fields as $field) {
                                    if ($field['type'] == 'group') {
                                        $field_label = get_field($field['name'])['name'];
                                        $value = get_field($field['name'])['value'];
                                        if (!empty($field_label) && !empty($value)) {
                                            echo '<div class="order_option">';
                                            echo '<div class="title">' . esc_html($field_label) . '</div>';
                                            echo '<div class="price">' . esc_html($value) . '</div>';
                                            echo '</div>';
                                        }
                                    }
                                    if ($field['type'] != 'group' && $field['type'] !== 'true_false' && !empty(get_field($field['name']))) {
                                        $field_label = $field['label'];
                                        $value = get_field($field['name']);
                                        echo '<div class="order_option">';
                                        echo '<div class="title">' . esc_html($field_label) . '</div>';
                                        echo '<div class="price">' . esc_html($value) . '</div>';
                                        echo '</div>';
                                    }
                                }
                            }
                        }
                        ?>
                        <div class="order_option">
                            <div class="final_price_title">
                                Итоговая стоимость <?php if (has_term('Мемориальные комплексы', 'product_cat', $product_id)) {
                                                        echo ' (под ключ)';
                                                    } ?>
                            </div>
                            <?php
                            $is_on_sale = false;
                            $product_on_sale = get_the_terms(get_the_ID(), 'product_tag');
                            if (! empty($product_on_sale) && ! is_wp_error($product_on_sale)) {
                                $is_on_sale = true;
                            }
                            ?>
                            <div class="final_price_value">
                                <div class="final_price_old<?php if ($is_on_sale) {
                                                                echo ' crossed';
                                                            } ?>" id="full_price">
                                    <?php
                                    $final_price_old = 0;
                                    if ($custom_product_type_bool) {
                                        $final_price_old = number_format(wc_get_product(get_the_ID())->get_regular_price(), 0, '', ' ') . ' р.';
                                        if (get_custom_product_type("ФИО")) {
                                            $final_price_old = 'от ' . $final_price_old . ' за 1 букву';
                                        }
                                    }
                                    echo $final_price_old;
                                    ?>
                                    <!-- <?php echo wc_get_product(get_the_ID())->get_regular_price(); ?> -->
                                    <!-- <?php echo wc_get_product(get_the_ID())->get_regular_price() - wc_get_product(get_the_ID())->get_sale_price(); ?> -->
                                    <!-- 0 -->
                                </div>
                                <?php if ($is_on_sale) { ?>
                                    <div class="final_price_new" id="discount_price">
                                        <!-- <?php echo wc_get_product(get_the_ID())->get_sale_price(); ?> -->
                                        0
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="order_bottom">
                        <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/main/product_card/payment_methods.png" alt="" class="payment_options">
                        <div class="order_button scalable" id="order_open_button">Заказать</div>
                    </div>
                </div>
                <?php if (!$custom_product_type_bool) { ?>
                    <div class="product_options_descr">
                        <h4>Выберите параметры памятника</h4>
                        <p class="static_descr"> Итоговая стоимость и параметры отобразятся в синей плашке выше</p>
                        <p class="adaptive_descr"> Итоговая стоимость и параметры отобразятся в синей плашке ниже</p>
                    </div>
                    <div class="product_options">
                        <?php
                        for ($i = 0; $i < 1; $i++) {
                            $field_group = $field_groups_2[$i];
                        ?>
                            <div class="options_block second_type">
                                <h3 class="options_title">
                                    <?= str_replace('(тип 2)', '', $field_group['title']); ?>
                                </h3>
                                <?php
                                $fields = acf_get_fields($field_group['ID']);
                                $group_fields = array();
                                foreach ($fields as $field) {
                                    if ($field['type'] === 'group') {
                                        $group_fields[] = $field;
                                    }
                                }
                                ?>
                                <?php
                                foreach ($group_fields as $group_field) {
                                    $sub_fields = $group_field['sub_fields'];
                                    if (get_field($group_field['name'], get_the_ID())[$sub_fields[0]['name']] != '') {
                                ?>
                                        <div class="option">
                                            <input type="radio" class="option_button" name="<?= preg_replace("/_\d+/", '', $group_field['name']); ?>">
                                            <div class="option_labels">
                                                <?php
                                                $sub_field_num = 0;
                                                foreach ($sub_fields as $sub_field) {
                                                    $sub_field_num += 1;
                                                ?>
                                                    <div class="option_column_name <?php echo $sub_field['name']; ?>" style="<?php if ($sub_field_num == 2 || $sub_field_num == 3) {
                                                                                                                                    echo 'display:none;';
                                                                                                                                } ?>">
                                                        <?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <?php

                        foreach ($field_groups_1 as $field_group) {
                        ?>
                            <div class="options first_type options_block">
                                <h3 class="options_title">
                                    <?= str_replace('(тип 1)', '', $field_group['title']); ?>
                                </h3>
                                <?php
                                $text_fields = array();
                                $group_fields = array();
                                // var_dump($field_group['ID']);
                                $fields = acf_get_fields($field_group['ID']);

                                foreach ($fields as $field) {
                                    if ($field['type'] === 'text') {

                                        $text_fields[] = $field;
                                    }
                                    if ($field['type'] === 'group') {

                                        $group_fields[] = $field;
                                    }
                                }
                                if (!empty($text_fields)) {
                                ?>
                                    <div class="option_columns_name">
                                        <?php
                                        foreach ($text_fields as $text_field) {
                                        ?>
                                            <div class="option_column_name">
                                                <?php the_field($text_field['name'], get_the_ID()); ?>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                if (!empty($group_fields)) {

                                    $option_count = 0;
                                    $options_max_count = 2;
                                    foreach ($group_fields as $group_field) {
                                        $sub_fields = $group_field['sub_fields'];
                                        // var_dump($sub_fields);
                                        if (
                                            !empty(get_field($group_field['name'], get_the_ID())[$sub_fields[0]['name']]) &&
                                            !empty(get_field($group_field['name'], get_the_ID())[$sub_fields[1]['name']])
                                            // !empty(get_field($group_field['name'], get_the_ID())[$sub_fields[2]['name']])
                                        ) {
                                            $option_count += 1;
                                    ?>
                                            <div class="option">
                                                <input type="radio" class="option_button" name="<?= preg_replace("/_\d+/", '', $group_field['name']); ?>">
                                                <div class="option_labels">
                                                    <?php
                                                    foreach ($sub_fields as $sub_field) {

                                                    ?>
                                                        <div class="option_column_name <?php echo $sub_field['name']; ?>" <?php if ($sub_field['name'] == 'price') { ?> data-first-price="<?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?>" <?php }; ?>>
                                                            <!-- <?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?> -->
                                                            <?php if ($sub_field['name'] == 'price') {
                                                                $price_with_suffix = get_field($group_field['name'], get_the_ID())[$sub_field['name']];

                                                                $price = preg_replace('/[^\d]/', '', $price_with_suffix);

                                                                echo number_format($price, 0, '', ' ') . ' р.';
                                                            } else {
                                                                echo get_field($group_field['name'], get_the_ID())[$sub_field['name']];
                                                            } ?>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                            if ($option_count == $options_max_count) {
                                                $option_count = 0;
                                                $options_max_count += 1;
                                            ?>
                                                <div class="option_line"></div>
                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        for ($i = 1; $i < 4; $i++) {
                            $field_group = $field_groups_2[$i];
                        ?>
                            <div class="options_block second_type">
                                <h3 class="options_title">
                                    <?= str_replace('(тип 2)', '', $field_group['title']); ?>
                                </h3>
                                <?php
                                $fields = acf_get_fields($field_group['ID']);
                                $group_fields = array();
                                foreach ($fields as $field) {
                                    if ($field['type'] === 'group') {

                                        $group_fields[] = $field;
                                    }
                                }
                                ?>
                                <?php
                                foreach ($group_fields as $group_field) {
                                    $sub_fields = $group_field['sub_fields'];
                                    if (get_field($group_field['name'], get_the_ID())[$sub_fields[0]['name']] != '') {
                                ?>
                                        <div class="option">
                                            <input type="radio" class="option_button" name="<?= preg_replace("/_\d+/", '', $group_field['name']); ?>">
                                            <div class="option_labels">
                                                <?php
                                                foreach ($sub_fields as $sub_field) {
                                                ?>
                                                    <div class="option_column_name <?php echo $sub_field['name']; ?>" <?php if ((($sub_field['name'] == 'price') && (trim(str_replace('(тип 2)', '', $field_group['title'])) == 'Подставка')) || ($sub_field['name'] == 'price') && (trim(str_replace('(тип 2)', '', $field_group['title'])) == 'Выбор цветника')) { ?> data-first-price="<?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?>" <?php } ?>>
                                                        <!-- <?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?> -->
                                                        <?php if ($sub_field['name'] == 'price' && get_field($group_field['name'], get_the_ID())[$sub_field['name']] != 'Бесплатно') {
                                                            $price_with_suffix = get_field($group_field['name'], get_the_ID())[$sub_field['name']];

                                                            $price = preg_replace('/[^\d]/', '', $price_with_suffix);

                                                            echo number_format($price, 0, '', ' ') . ' р.';
                                                        } else {
                                                            echo get_field($group_field['name'], get_the_ID())[$sub_field['name']];
                                                        } ?>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <?php
                        foreach ($field_groups_3 as $field_group) {
                        ?>
                            <div class="product_decor_options options_block square_options third_type">
                                <h3 class="options_title">
                                    <?= str_replace('(тип 3)', '', $field_group['title']); ?>
                                </h3>
                                <?php
                                $fields = acf_get_fields($field_group['ID']);
                                $group_fields = array();
                                foreach ($fields as $field) {
                                    if ($field['type'] === 'group') {

                                        $group_fields[] = $field;
                                    }
                                }
                                ?>
                                <?php
                                foreach ($group_fields as $group_field) {
                                    $sub_fields = $group_field['sub_fields'];
                                    if (get_field($group_field['name'], get_the_ID())[$sub_fields[0]['name']] != '') {
                                ?>
                                        <div class="option">
                                            <div class="option_labels">
                                                <?php
                                                foreach ($sub_fields as $sub_field) {
                                                    if ($sub_field['type'] === 'text') {
                                                ?>
                                                        <div class="decor_option">
                                                            <input type="radio" class="option_button" name="<?= preg_replace("/_\d+/", '', $group_field['name']); ?>">
                                                            <label for="<?= preg_replace("/_\d+/", '', $group_field['name']); ?>" class="option_label">
                                                                <?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?>
                                                            </label>
                                                        </div>
                                                    <?php
                                                    }
                                                    if ($sub_field['type'] === 'group') {
                                                    ?>
                                                        <div class="select_prices_options_container">
                                                            <select name="decor_prices" id="" class="prices_options">
                                                                <?php
                                                                for ($i = 0; $i < count($sub_field['sub_fields']); $i++) {
                                                                    // echo $i . ','; 
                                                                    $price_value = get_field($group_field['name'], get_the_ID())[$sub_field['name']][$sub_field['sub_fields'][$i]['name']];
                                                                    if ($price_value != '') {
                                                                ?>
                                                                        <option value="<?php echo mb_substr($price_value, 5);  ?>" data-quantity="<?php echo mb_substr($price_value, 0, 4);  ?>">
                                                                            <span class="decor_quantity_option"><?php echo mb_substr($price_value, 0, 4);  ?></span>
                                                                            <span class="decor_price_option"><?php echo mb_substr($price_value, 5);  ?></span>
                                                                        </option>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                            <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/main/product_card/open_price_options_icon.png" alt="" class="select_prices_icon open">
                                                        </div>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        foreach ($field_groups_4 as $field_group) {
                        ?>
                            <div class="product_more_decor_options options_block square_options more_options fourth_type">
                                <h3 class="options_title">
                                    <?= str_replace('(тип 4)', '', $field_group['title']); ?>
                                </h3>
                                <?php
                                $fields = acf_get_fields($field_group['ID']);
                                $group_fields = array();
                                foreach ($fields as $field) {
                                    if ($field['type'] === 'group') {

                                        $group_fields[] = $field;
                                    }
                                }
                                ?>
                                <?php
                                foreach ($group_fields as $group_field) {
                                    $sub_fields = $group_field['sub_fields'];
                                    if (get_field($group_field['name'], get_the_ID())[$sub_fields[0]['name']] != '') {
                                ?>
                                        <div class="option">
                                            <input type="radio" class="option_button" name="<?= preg_replace("/_\d+/", '', $group_field['name']); ?>">
                                            <div class="option_labels">
                                                <?php
                                                foreach ($sub_fields as $sub_field) {
                                                    if (get_field($group_field['name'], get_the_ID())[$sub_field['name']] != '') {
                                                ?>
                                                        <div class="option_column_name <?= $sub_field['name']; ?>">
                                                            <!-- <?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?> -->
                                                            <?php if ($sub_field['name'] == 'price' && get_field($group_field['name'], get_the_ID())[$sub_field['name']] != 'Бесплатно') {
                                                                $price_with_suffix = get_field($group_field['name'], get_the_ID())[$sub_field['name']];

                                                                $price = preg_replace('/[^\d]/', '', $price_with_suffix);

                                                                echo number_format($price, 0, '', ' ') . ' р.';
                                                            } else {
                                                                echo get_field($group_field['name'], get_the_ID())[$sub_field['name']];
                                                            } ?>
                                                        </div>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        // foreach ($field_groups_2 as $field_group) {
                        for ($i = 4; $i < count($field_groups_2); $i++) {
                            $field_group = $field_groups_2[$i];
                        ?>
                            <div class="options_block second_type">
                                <h3 class="options_title">
                                    <?= str_replace('(тип 2)', '', $field_group['title']); ?>
                                </h3>
                                <?php
                                $fields = acf_get_fields($field_group['ID']);
                                $group_fields = array();
                                foreach ($fields as $field) {
                                    if ($field['type'] === 'group') {

                                        $group_fields[] = $field;
                                    }
                                }
                                ?>
                                <?php
                                foreach ($group_fields as $group_field) {
                                    $sub_fields = $group_field['sub_fields'];
                                    if (get_field($group_field['name'], get_the_ID())[$sub_fields[0]['name']] != '') {
                                ?>
                                        <div class="option">
                                            <input type="radio" class="option_button" name="<?= preg_replace("/_\d+/", '', $group_field['name']); ?>">
                                            <div class="option_labels">
                                                <?php
                                                foreach ($sub_fields as $sub_field) {
                                                ?>
                                                    <div class="option_column_name <?php echo $sub_field['name']; ?>">
                                                        <!-- <?php echo get_field($group_field['name'], get_the_ID())[$sub_field['name']]; ?> -->
                                                        <?php if ($sub_field['name'] == 'price' && get_field($group_field['name'], get_the_ID())[$sub_field['name']] != 'Бесплатно') {
                                                            $price_with_suffix = get_field($group_field['name'], get_the_ID())[$sub_field['name']];

                                                            $price = preg_replace('/[^\d]/', '', $price_with_suffix);

                                                            echo number_format($price, 0, '', ' ') . ' р.';
                                                        } else {
                                                            echo get_field($group_field['name'], get_the_ID())[$sub_field['name']];
                                                        } ?>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="product_price_window_descr">
                        <button class="price_window_scroll_button" id="price_window_scroll_up">Рассчитать стоимость</button>
                        <p>Итоговая стоимость и параметры отобразятся в синей плашке</p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if (!$custom_product_type_bool) { ?>
            <div class="mobile_product_descr">
                <div class="product_card_big_descr">
                    <div class="product_card_option_title">Описание</div>
                    <p class="card_descr_text">
                        <?php echo $product->get_short_description(); ?>
                    </p>
                </div>
                <div class="product_card_photos">
                    <div class="product_card_option_title">
                        Примеры наших работ
                    </div>
                    <div class="photos">
                        <?php
                        $photo_num = 0;
                        $gallery_id = $product->get_gallery_image_ids();
                        foreach ($gallery_id as $photo_id) {
                            $photo_num += 1;
                        ?>
                            <div class="photo">
                                <img src="<?php echo wp_get_attachment_url($photo_id); ?>" alt="" class="product_photo_img">
                                <div class="photo_back" data-photo-num="<?php echo $photo_num; ?>"></div>
                            </div>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php
        $product_count = 0;
        $my_products = get_posts(array(
            'numberposts' => -1,
            'post_type'    => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => get_the_terms(get_the_ID(), 'product_cat')[1]->slug
                )
            ),
            'order'       => 'ASC',
        ));
        if (count($my_products) > 0) {
        ?>
            <div class="products_block">
                <?php $product_main_term = get_the_terms(get_the_ID(), 'product_cat')[0]->name;
                $current_product_type = '';
                $current_product_types = ["Памятники" => "памятники", "Оформление" => "оформления", "Аксессуары" => "аксессуары"];
                foreach ($current_product_types as $product_type => $order) {
                    if ($product_main_term == $product_type) {
                        $current_product_type = $order;
                    }
                } ?>
                <h2 class="products_title">Похожие <?php echo $current_product_type; ?></h2>
                <div class="products">
                    <?php
                    $product_count = 0;
                    foreach ($my_products as $product) {
                        // setup_postdata( $product );
                        if ($product->ID != get_the_ID() && $product_count < 4) {
                            $product_count += 1;
                            $product_on_sale = get_the_terms($product->ID, 'product_tag');
                            if (! empty($product_on_sale) && ! is_wp_error($product_on_sale)) {
                    ?>
                                <div class="product prom" data-artic="<?= get_post_meta($product->ID, '_sku')[0]; ?>" data-views="<?= pvc_get_post_views($product->ID); ?>">
                                    <div class="prom_block">Акция</div>
                                    <img src="<?php echo get_the_post_thumbnail_url($product->ID); ?>" alt="" class="product_img">
                                    <div class="product_name"><?php echo get_the_title($product->ID); ?></div>
                                    <div class="product_price_prom"><?php echo number_format(wc_get_product($product->ID)->get_sale_price(), 0, '', ' '); ?>р.</div>
                                    <div class="product_price_old"><?php echo number_format(wc_get_product($product->ID)->get_regular_price(), 0, '', ' '); ?>р.</div>
                                    <a href="<?php echo get_permalink($product->ID); ?>" class="product_order">заказать</a>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="product" data-artic="<?= get_post_meta($product->ID, '_sku')[0]; ?>" data-views="<?= pvc_get_post_views($product->ID); ?>">
                                    <img src="<?php echo get_the_post_thumbnail_url($product->ID); ?>" alt="" class="product_img">
                                    <div class="product_name"><?php echo get_the_title($product->ID); ?></div>
                                    <div class="product_price">
                                        <?php echo number_format(wc_get_product($product->ID)->get_regular_price(), 0, '', ' '); ?>р.
                                    </div>
                                    <a href="<?php echo get_permalink($product->ID); ?>" class="product_order">заказать</a>
                                </div>

                    <?php
                            }
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
        <a href="<?php the_field('main_ad_banner_link'); ?>" class="banner">
            <div class="banner_text">
                <h3 class="banner_title">
                    <?php echo get_field('banner_title', 2); ?>
                </h3>
                <p class="banner_descr">
                    <?php echo get_field('banner_descr', 2); ?>
                </p>
            </div>
            <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/main/banner/pensioners.png" alt="" class="banner_img">
        </a>
    </section>
</div>
</div>
</main>
<?php $product_main_term = get_the_terms(get_the_ID(), 'product_cat')[0]->name;
$order_product_type = '';
$order_product_types = ["Памятники" => "памятника", "Оформление" => "оформления", "Аксессуары" => "аксессуара"];
foreach ($order_product_types as $product_type => $order) {
    if ($product_main_term == $product_type) {
        $order_product_type = $order;
    }
} ?>
<div class="order_product_block" id="order_product_block">
    <div class="order_product_window">
        <div class="close_window">
            <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/close_window.png" alt="" class="close_window_img rotate" id="close_order">
        </div>
        <div class="order_product_window_icon">
            <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/header/header_logo.png" alt="">
        </div>
        <h3 class="order_product_title">
            Заявка на изготовление <?php echo $order_product_type; ?>
        </h3>
        <div class="order_product_content">
            <div class="order_product_message">Вы выбрали</div>
            <div class="order_product_name"><?php the_title(); ?></div>
            <!-- <div class="order_product_size order_product_info">
                    <div class="product_size_title">
                        Размер
                        <span class="size_1_value" id="size_1_value">80 x 40 x 5 </span>,
                        <span class="size_2_value" id="size_2_value">12 x 50 x 15</span>
                    </div>
                    <div class="product_size_price" id="size_price_value">33.300 р.</div>
                </div>
                <div class="order_product_color order_product_info">
                    <div class="product_parterre_title">Выбор цветника</div>
                    <div class="product_parterre_price" id="parterre_price_value">0 р.</div>
                </div>
                <div class="order_product_polish order_product_info">
                    <div class="product_polish_title">Выбор полировки</div>
                    <div class="product_polish_price" id="polishing_price_value">4.500 р.</div>
                </div>
                <div class="order_product_grav order_product_info">
                    <div class="product_grav_title" id="decor_title_value">
                        Фото (Графировка) 
                    </div>
                    <span class="grav_number" id="decor_quantity_value">1 шт.</span>
                    <div class="product_grav_price" id="decor_price_value">4.500 р.</div>
                </div>
                <div class="order_product_install order_product_info">
                    <div class="product_install_title">Установка</div>
                    <div class="product_install_value" id="install_price_value">Без установки</div>
                </div>
                <div class="order_product_delivery order_product_info">
                    <div class="product_delivery_title">Самовывоз</div>
                    <div class="product_delivery_price" id="delivery_price_value">Бесплатно</div>
                </div> -->
            <div class="order_product_about" id="order_product_info">

            </div>
            <div class="order_product_price">
                <div class="product_price_title">Итоговая стоимость</div>
                <div class="product_price_value" id="final_price_value">42.300 р.</div>
            </div>
        </div>
        <div class="order_product_success_message">
            <h4 class="success_title">Спасибо!</h4>
            <p class="success_descr">
                Ваше сообщение отправлено. <br>
                Мы с вами свяжемся в самое ближайшее время.
            </p>
        </div>
        <?php echo do_shortcode('[contact-form-7 id="2e4fc51" title="Форма заказа товара" html_id="client_order_form"]'); ?>
    </div>
</div>
<div class="photo_view_block" id="photo_view_block">
    <div class="photo_view">
        <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/main/photo_view_slider/close_slider.png" alt="" class="close_photo_view rotate" id="close_photo_view">
        <div class="photo_view_slider">
            <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/main/photo_view_slider/arrow_left.png" alt="" class="photo_slider_arrow left">
            <div class="photo_slides">
                <?php
                $gallery_id = $product->get_gallery_image_ids();
                // foreach ($gallery_id as $photo_id) {
                for ($i = 0; $i < count($gallery_id); $i++) {
                ?>
                    <img src="<?php echo wp_get_attachment_url($gallery_id[$i]); ?>" alt="" class="photo_slide<?php if ($i == 0) {
                                                                                                                    echo ' active';
                                                                                                                } ?>" data-photo-num="<?php echo $i + 1; ?>">
                <?php
                }
                ?>
            </div>
            <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/main/photo_view_slider/arrow_right.png" alt="" class="photo_slider_arrow right">
        </div>
    </div>
</div>

<div class="photo_product_view_block" id="photo_product_view_block">
    <div class="photo_product_view">
        <img src="<?php echo get_bloginfo('template_url'); ?>/assets/imgs/main/photo_view_slider/close_slider.png" alt="" class="close_photo_product_view rotate" id="close_photo_product_view">
        <div class="photo_product_view_slider">
            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID()); ?>" alt="">
        </div>
    </div>
</div>
<?php
add_action('wpcf7_mail_sent', 'custom_wpcf7_mail_sent');

function custom_wpcf7_mail_sent($contact_form)
{
    $form_id = $contact_form->id();

    if ($form_id == '2e4fc51') {
        $order_current_counter = get_field('product_order_counter', get_the_ID());
        $order_current_counter += 1;
        update_field('product_order_counter', $order_current_counter += 1, get_the_ID());
    }
}
?>
<?php get_footer(); ?>