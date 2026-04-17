<?php
/**
 * single-product.php - МИНИМАЛЬНАЯ ВЕРСИЯ (тестируем базу)
 */
get_header();
?>

<?php
// Подключаем jQuery, если не подключен
if (!wp_script_is('jquery', 'enqueued')) {
    wp_enqueue_script('jquery');
}
?>

<?php
/**
 * single-product.php - ШАГ 2 (добавляем категории)
 */
get_header();

// ==================== БЕЗОПАСНОЕ ПОЛУЧЕНИЕ КАТЕГОРИЙ ====================
$product_id = get_the_ID();
$product_terms = get_the_terms($product_id, 'product_cat');

$cat_name_0 = '';
$cat_name_1 = '';
$cat_slug_1 = '';
$cat_term_id_1 = 0;

if (is_array($product_terms) && !empty($product_terms)) {
    if (isset($product_terms[0])) {
        $cat_name_0 = $product_terms[0]->name;
    }
    if (isset($product_terms[1])) {
        $cat_name_1 = $product_terms[1]->name;
        $cat_slug_1 = $product_terms[1]->slug;
        $cat_term_id_1 = $product_terms[1]->term_id;
    }
}

// Проверяем, что получили
// echo '<!-- Категория 0: ' . esc_html($cat_name_0) . ' -->';
// echo '<!-- Категория 1: ' . esc_html($cat_name_1) . ' -->';


// ==================== УСЛОВНЫЕ CSS СТИЛИ ====================
$css_thumb_categories = array('ФИО', 'Цветы', 'Святые', 'Иконы', 'Ангелы', 'Виньетки', 'Свечки', 'Храмы', 'Природа');
if (in_array($cat_name_1, $css_thumb_categories)) {
    add_action('wp_head', function() {
        echo '<style>.product_img { display: block; height: 180px; }</style>';
    }, 100);
}

$is_memorial_complex = ($cat_name_1 === 'Мемориальные комплексы');
if ($is_memorial_complex) {
    add_action('wp_head', function() {
        echo '<style>.product_card_img img { max-width: 400px; width: 100%; object-fit: cover; }</style>';
    }, 100);
}

// ==================== ПОЛУЧЕНИЕ ACF ГРУПП ====================
$field_groups_1 = array();
$field_groups_2 = array();
$field_groups_3 = array();
$field_groups_4 = array();

if (function_exists('acf_get_field_groups') && function_exists('acf_get_fields')) {
    $field_groups = acf_get_field_groups(array('post_id' => $product_id));
    
    if (is_array($field_groups)) {
        foreach ($field_groups as $field_group) {
            if (!isset($field_group['ID']) || !isset($field_group['title'])) {
                continue;
            }
            
            $group_fields = acf_get_fields($field_group['ID']);
            if (!is_array($group_fields)) {
                continue;
            }
            
            $has_filter_add = false;
            foreach ($group_fields as $field) {
                if (isset($field['label']) && $field['label'] === 'Фильтр') {
                    $filter_value = get_field($field['name'], $product_id);
                    if ($filter_value === 'add') {
                        $has_filter_add = true;
                    }
                    break;
                }
            }
            
            if (!$has_filter_add) {
                continue;
            }
            
            if (strpos($field_group['title'], '(тип 1)') !== false) {
                $field_groups_1[] = $field_group;
            } elseif (strpos($field_group['title'], '(тип 2)') !== false) {
                $field_groups_2[] = $field_group;
            } elseif (strpos($field_group['title'], '(тип 3)') !== false) {
                $field_groups_3[] = $field_group;
            } elseif (strpos($field_group['title'], '(тип 4)') !== false) {
                $field_groups_4[] = $field_group;
            }
        }
    }
}

// Временно выведем количество для проверки
echo '<!-- ACF Groups found: тип1=' . count($field_groups_1) . ', тип2=' . count($field_groups_2) . ', тип3=' . count($field_groups_3) . ', тип4=' . count($field_groups_4) . ' -->';
?>

<div class="main_content">
    <?php echo get_sidebar('products-menu'); ?>
    <section class="products_section">
        
        <div class="page_path">
            <a href="<?php echo esc_url(home_url()); ?>" class="prev_page">Главная</a>
            <span class="page_path_arrow">></span>
            <?php if (!empty($cat_name_0)): ?>
                <a href="#" class="prev_page"><?php echo esc_html($cat_name_0); ?></a>
                <span class="page_path_arrow">></span>
            <?php endif; ?>
            <?php if (!empty($cat_name_1) && !empty($cat_term_id_1)): ?>
                <a href="<?php echo esc_url(get_term_link($cat_term_id_1, 'product_cat')); ?>" class="prev_page">
                    <?php echo esc_html($cat_name_1); ?>
                </a>
                <span class="page_path_arrow">></span>
            <?php endif; ?>
            <a href="#" class="current_page"><?php the_title(); ?></a>
        </div>

        <!-- остальной HTML как в шаге 1 -->
        <div class="product_block">
            <div class="product_card_block">
                <div class="product_card_info">
                    <h1 class="product_card_title"><?php the_title(); ?></h1>
                    <div class="product_card_price">
                        Цена <span class="card_price_value" id="card_price_value"></span>
                    </div>
                </div>
                
                <div class="product_card_img">
                    <img src="<?php echo esc_url(get_the_post_thumbnail_url($product_id)); ?>" alt="" class="product_card_img_view">
                </div>
            </div>

            <div class="product_order_block">
                <div class="product_order_window" data-is-custom-product-type="no">
					<h2 class="order_title">Вы выбрали</h2>
					<div class="order_options">
    
					<!-- 1. Выбор материала (тип 2, индекс 0) -->
					<?php if (!empty($field_groups_2) && isset($field_groups_2[0])): 
						$fg = $field_groups_2[0];
						$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
						$group_field = null;
						if (is_array($fg_fields)) {
							foreach ($fg_fields as $f) {
								if (isset($f['type']) && $f['type'] === 'group') {
									$group_field = $f;
									break;
								}
							}
						}
						if ($group_field && isset($group_field['sub_fields'][1])) {
							$group_value = get_field($group_field['name'], $product_id);
							if (is_array($group_value)) {
								$price_value = isset($group_value[$group_field['sub_fields'][1]['name']]) ? $group_value[$group_field['sub_fields'][1]['name']] : '0';
								$clean_price = preg_replace('/[^\d]/', '', $price_value);
					?>
						<div class="order_option second_type_option">
							<div class="title"><?php echo esc_html(str_replace('(тип 2)', '', $fg['title'])); ?></div>
							<div class="price" 
								 id="<?php echo esc_attr(preg_replace("/_\d+/", '', $group_field['name']) . '_price'); ?>"
								 data-price="<?php echo esc_attr($clean_price); ?>">
								<?php echo number_format(floatval($clean_price), 0, '', ' ') . ' р.'; ?>
							</div>
						</div>
					<?php 
							}
						}
					endif; 
					?>

					<!-- 2. Размер стелы (тип 1, индекс 0) -->
					<?php if (!empty($field_groups_1) && isset($field_groups_1[0])): 
						$fg = $field_groups_1[0];
						$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
						$group_field = null;
						if (is_array($fg_fields)) {
							foreach ($fg_fields as $f) {
								if (isset($f['type']) && $f['type'] === 'group') {
									$group_field = $f;
									break;
								}
							}
						}
						if ($group_field && isset($group_field['sub_fields'][1])) {
							$group_value = get_field($group_field['name'], $product_id);
							if (is_array($group_value)) {
								$size_text = isset($group_value[$group_field['sub_fields'][0]['name']]) ? $group_value[$group_field['sub_fields'][0]['name']] : '';
								$price_value = isset($group_value[$group_field['sub_fields'][1]['name']]) ? $group_value[$group_field['sub_fields'][1]['name']] : '0';
								$clean_price = preg_replace('/[^\d]/', '', $price_value);
					?>
						<div class="order_option first_type_option">
							<div class="title">
								<?php echo esc_html(str_replace('(тип 1)', '', $fg['title'])); ?>
								<div class="final_size_value">
									<span id="size_param"><?php echo esc_html($size_text); ?></span>
								</div>
							</div>
							<div class="price" id="size_price" data-price="<?php echo esc_attr($clean_price); ?>">
								<?php echo number_format(floatval($clean_price), 0, '', ' ') . ' р.'; ?>
							</div>
						</div>
					<?php 
							}
						}
					endif; 
					?>

					<!-- 3. Полировка (тип 2, индекс 1) -->
					<?php if (!empty($field_groups_2) && isset($field_groups_2[1])): 
						$fg = $field_groups_2[1];
						$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
						$group_field = null;
						if (is_array($fg_fields)) {
							foreach ($fg_fields as $f) {
								if (isset($f['type']) && $f['type'] === 'group') {
									$group_field = $f;
									break;
								}
							}
						}
						if ($group_field && isset($group_field['sub_fields'][1])) {
							$group_value = get_field($group_field['name'], $product_id);
							if (is_array($group_value)) {
								$price_value = isset($group_value[$group_field['sub_fields'][1]['name']]) ? $group_value[$group_field['sub_fields'][1]['name']] : '0';
								$clean_price = preg_replace('/[^\d]/', '', $price_value);
					?>
						<div class="order_option second_type_option">
							<div class="title"><?php echo esc_html(str_replace('(тип 2)', '', $fg['title'])); ?></div>
							<div class="price" 
								 id="polishing_price"
								 data-price="<?php echo esc_attr($clean_price); ?>">
								<?php echo number_format(floatval($clean_price), 0, '', ' ') . ' р.'; ?>
							</div>
						</div>
					<?php 
							}
						}
					endif; 
					?>

					<!-- 4. Выбор цветника (тип 2, индекс 2) -->
					<?php if (!empty($field_groups_2) && isset($field_groups_2[2])): 
						$fg = $field_groups_2[2];
						$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
						$group_field = null;
						if (is_array($fg_fields)) {
							foreach ($fg_fields as $f) {
								if (isset($f['type']) && $f['type'] === 'group') {
									$group_field = $f;
									break;
								}
							}
						}
						if ($group_field && isset($group_field['sub_fields'][1])) {
							$group_value = get_field($group_field['name'], $product_id);
							if (is_array($group_value)) {
								$price_value = isset($group_value[$group_field['sub_fields'][1]['name']]) ? $group_value[$group_field['sub_fields'][1]['name']] : '0';
								$clean_price = preg_replace('/[^\d]/', '', $price_value);
					?>
						<div class="order_option second_type_option">
							<div class="title"><?php echo esc_html(str_replace('(тип 2)', '', $fg['title'])); ?></div>
							<div class="price" 
								 id="parterre_price"
								 data-price="<?php echo esc_attr($clean_price); ?>">
								<?php echo number_format(floatval($clean_price), 0, '', ' ') . ' р.'; ?>
							</div>
						</div>
					<?php 
							}
						}
					endif; 
					?>

					<!-- 5. Подставка (тип 2, индекс 3) -->
					<?php if (!empty($field_groups_2) && isset($field_groups_2[3])): 
						$fg = $field_groups_2[3];
						$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
						$group_field = null;
						if (is_array($fg_fields)) {
							foreach ($fg_fields as $f) {
								if (isset($f['type']) && $f['type'] === 'group') {
									$group_field = $f;
									break;
								}
							}
						}
						if ($group_field && isset($group_field['sub_fields'][1])) {
							$group_value = get_field($group_field['name'], $product_id);
							if (is_array($group_value)) {
								$price_value = isset($group_value[$group_field['sub_fields'][1]['name']]) ? $group_value[$group_field['sub_fields'][1]['name']] : '0';
								$clean_price = preg_replace('/[^\d]/', '', $price_value);
					?>
						<div class="order_option second_type_option">
							<div class="title"><?php echo esc_html(str_replace('(тип 2)', '', $fg['title'])); ?></div>
							<div class="price" 
								 id="stand_price"
								 data-price="<?php echo esc_attr($clean_price); ?>">
								<?php echo number_format(floatval($clean_price), 0, '', ' ') . ' р.'; ?>
							</div>
						</div>
					<?php 
							}
						}
					endif; 
					?>

					<!-- 6. Гравировка/Декор (тип 3) -->
					<!-- 6. Гравировка/Декор (тип 3) с уникальными ID -->
					<!-- 6. Гравировка/Декор (тип 3) - отдельно для каждой группы -->
					<?php 
					if (!empty($field_groups_3)):
						$decor_counter = 0;
						foreach ($field_groups_3 as $fg):
							$decor_counter++;
							$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
							$group_field = null;
							if (is_array($fg_fields)) {
								foreach ($fg_fields as $f) {
									if (isset($f['type']) && $f['type'] === 'group') {
										$group_field = $f;
										break;
									}
								}
							}
							if ($group_field && isset($group_field['sub_fields'][1])) {
								$group_value = get_field($group_field['name'], $product_id);
								if (is_array($group_value)) {
									$decor_title = isset($group_value[$group_field['sub_fields'][0]['name']]) ? $group_value[$group_field['sub_fields'][0]['name']] : 'Оформление';
									// Пробуем получить цену из разных мест
									$price_value = '0';
									if (isset($group_value['price_options']['price_1'])) {
										$price_value = $group_value['price_options']['price_1'];
									} elseif (isset($group_value['price'])) {
										$price_value = $group_value['price'];
									} elseif (isset($group_value[$group_field['sub_fields'][1]['name']])) {
										$price_value = $group_value[$group_field['sub_fields'][1]['name']];
									}
									$clean_price = preg_replace('/[^\d]/', '', $price_value);
									$quantity = '';
									if (strlen($price_value) > 5) {
										$quantity = mb_substr($price_value, 0, 4);
									}
					?>
							<div class="order_option third_type_option" id="decor_block_<?php echo $decor_counter; ?>">
								<div class="title">
									<span class="decor_parameter"><?php echo esc_html($decor_title); ?></span>
									<span class="decor_quantity" id="decor_quantity_<?php echo $decor_counter; ?>"><?php echo esc_html($quantity); ?></span>
								</div>
								<div class="price" id="decor_price_<?php echo $decor_counter; ?>" data-price="<?php echo esc_attr($clean_price); ?>">
									<?php echo number_format(floatval($clean_price), 0, '', ' ') . ' р.'; ?>
								</div>
							</div>
					<?php 
								}
							}
						endforeach;
					endif; 
					?>

					<!-- 7. Итоговая стоимость -->
					<div class="order_option">
						<div class="final_price_title">Итоговая стоимость</div>
						<div class="final_price_value">
							<div class="final_price_old" id="full_price">
								<?php 
								$product = wc_get_product($product_id);
								if ($product) {
									echo number_format($product->get_regular_price(), 0, '', ' ') . ' р.';
								} else {
									echo '0 р.';
								}
								?>
							</div>
						</div>
					</div>
				</div>
					<div class="order_bottom">
						<img src="<?php echo esc_url(get_bloginfo('template_url') . '/assets/imgs/main/product_card/payment_methods.png'); ?>" alt="" class="payment_options">
						<div class="order_button scalable" id="order_open_button">Заказать</div>
					</div>
				</div>
            </div>
			<!-- Полировка (тип 2, индекс 1) -->
			<?php if (!empty($field_groups_2) && isset($field_groups_2[1])): 
				$fg = $field_groups_2[1];
				$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
				$group_fields = array();
				if (is_array($fg_fields)) {
					foreach ($fg_fields as $f) {
						if (isset($f['type']) && $f['type'] === 'group') {
							$group_fields[] = $f;
						}
					}
				}
				if (!empty($group_fields)):
			?>
				<div class="options_block second_type">
					<h3 class="options_title"><?php echo esc_html(str_replace('(тип 2)', '', $fg['title'])); ?></h3>
					<?php foreach ($group_fields as $gf):
						$sub_fields = isset($gf['sub_fields']) ? $gf['sub_fields'] : array();
						$gf_value = get_field($gf['name'], $product_id);
						if (is_array($gf_value) && !empty($gf_value[$sub_fields[0]['name']] ?? '')):
					?>
						<div class="option">
							<input type="radio" class="option_button" name="<?php echo esc_attr(preg_replace("/_\d+/", '', $gf['name'])); ?>">
							<div class="option_labels">
								<?php foreach ($sub_fields as $sf): ?>
									<div class="option_column_name <?php echo esc_attr($sf['name']); ?>">
										<?php 
										if ($sf['name'] == 'price') {
											$price_val = preg_replace('/[^\d]/', '', $gf_value[$sf['name']] ?? '0');
											echo number_format(floatval($price_val), 0, '', ' ') . ' р.';
										} else {
											echo esc_html($gf_value[$sf['name']] ?? '');
										}
										?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php 
						endif;
					endforeach; ?>
				</div>
			<?php 
				endif;
			endif; 
			?>

			<!-- Выбор цветника (тип 2, индекс 2) -->
			<?php if (!empty($field_groups_2) && isset($field_groups_2[2])): 
				$fg = $field_groups_2[2];
				$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
				$group_fields = array();
				if (is_array($fg_fields)) {
					foreach ($fg_fields as $f) {
						if (isset($f['type']) && $f['type'] === 'group') {
							$group_fields[] = $f;
						}
					}
				}
				if (!empty($group_fields)):
			?>
				<div class="options_block second_type">
					<h3 class="options_title"><?php echo esc_html(str_replace('(тип 2)', '', $fg['title'])); ?></h3>
					<?php foreach ($group_fields as $gf):
						$sub_fields = isset($gf['sub_fields']) ? $gf['sub_fields'] : array();
						$gf_value = get_field($gf['name'], $product_id);
						if (is_array($gf_value) && !empty($gf_value[$sub_fields[0]['name']] ?? '')):
					?>
						<div class="option">
							<input type="radio" class="option_button" name="<?php echo esc_attr(preg_replace("/_\d+/", '', $gf['name'])); ?>">
							<div class="option_labels">
								<?php foreach ($sub_fields as $sf): ?>
									<div class="option_column_name <?php echo esc_attr($sf['name']); ?>"
										 <?php if ($sf['name'] == 'price'): ?>data-first-price="<?php echo esc_attr(preg_replace('/[^\d]/', '', $gf_value[$sf['name']] ?? '0')); ?>"<?php endif; ?>>
										<?php 
										if ($sf['name'] == 'price') {
											$price_val = preg_replace('/[^\d]/', '', $gf_value[$sf['name']] ?? '0');
											echo number_format(floatval($price_val), 0, '', ' ') . ' р.';
										} else {
											echo esc_html($gf_value[$sf['name']] ?? '');
										}
										?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php 
						endif;
					endforeach; ?>
				</div>
			<?php 
				endif;
			endif; 
			?>

			<!-- Подставка (тип 2, индекс 3) -->
			<?php if (!empty($field_groups_2) && isset($field_groups_2[3])): 
				$fg = $field_groups_2[3];
				$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
				$group_fields = array();
				if (is_array($fg_fields)) {
					foreach ($fg_fields as $f) {
						if (isset($f['type']) && $f['type'] === 'group') {
							$group_fields[] = $f;
						}
					}
				}
				if (!empty($group_fields)):
			?>
				<div class="options_block second_type">
					<h3 class="options_title"><?php echo esc_html(str_replace('(тип 2)', '', $fg['title'])); ?></h3>
					<?php foreach ($group_fields as $gf):
						$sub_fields = isset($gf['sub_fields']) ? $gf['sub_fields'] : array();
						$gf_value = get_field($gf['name'], $product_id);
						if (is_array($gf_value) && !empty($gf_value[$sub_fields[0]['name']] ?? '')):
					?>
						<div class="option">
							<input type="radio" class="option_button" name="<?php echo esc_attr(preg_replace("/_\d+/", '', $gf['name'])); ?>">
							<div class="option_labels">
								<?php foreach ($sub_fields as $sf): ?>
									<div class="option_column_name <?php echo esc_attr($sf['name']); ?>"
										 <?php if ($sf['name'] == 'price'): ?>data-first-price="<?php echo esc_attr(preg_replace('/[^\d]/', '', $gf_value[$sf['name']] ?? '0')); ?>"<?php endif; ?>>
										<?php 
										if ($sf['name'] == 'price') {
											$price_val = preg_replace('/[^\d]/', '', $gf_value[$sf['name']] ?? '0');
											echo number_format(floatval($price_val), 0, '', ' ') . ' р.';
										} else {
											echo esc_html($gf_value[$sf['name']] ?? '');
										}
										?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php 
						endif;
					endforeach; ?>
				</div>
			<?php 
				endif;
			endif; 
			?>

			<!-- Гравировка/Декор (тип 3) -->
			<!-- Гравировка/Декор (тип 3) с data-атрибутами -->
			<!-- Гравировка/Декор (тип 3) - отдельно для каждой группы -->
			<?php 
			$decor_radio_counter = 0;
			foreach ($field_groups_3 as $fg):
				$decor_radio_counter++;
				$fg_fields = (function_exists('acf_get_fields')) ? acf_get_fields($fg['ID']) : array();
				$group_fields = array();
				if (is_array($fg_fields)) {
					foreach ($fg_fields as $f) {
						if (isset($f['type']) && $f['type'] === 'group') {
							$group_fields[] = $f;
						}
					}
				}
				if (!empty($group_fields)):
			?>
				<div class="product_decor_options options_block square_options third_type" data-group-id="<?php echo $decor_radio_counter; ?>">
					<h3 class="options_title"><?php echo esc_html(str_replace('(тип 3)', '', $fg['title'])); ?></h3>
					<?php foreach ($group_fields as $gf):
						$sub_fields = isset($gf['sub_fields']) ? $gf['sub_fields'] : array();
						$gf_value = get_field($gf['name'], $product_id);
						if (is_array($gf_value) && !empty($gf_value[$sub_fields[0]['name']] ?? '')):
							$option_title = $gf_value[$sub_fields[0]['name']] ?? '';
					?>
						<div class="option" data-parent-group="<?php echo $decor_radio_counter; ?>">
							<div class="option_labels">
								<?php foreach ($sub_fields as $sf):
									if (isset($sf['type']) && $sf['type'] === 'text'): ?>
										<div class="decor_option">
											<input type="radio" 
												   class="option_button" 
												   name="decor_group_<?php echo $decor_radio_counter; ?>" 
												   data-group-id="<?php echo $decor_radio_counter; ?>"
												   data-option-title="<?php echo esc_attr($option_title); ?>">
											<label class="option_label"><?php echo esc_html($gf_value[$sf['name']] ?? ''); ?></label>
										</div>
									<?php elseif (isset($sf['type']) && $sf['type'] === 'group' && isset($sf['sub_fields'])): ?>
										<div class="select_prices_options_container">
											<select name="decor_prices_<?php echo $decor_radio_counter; ?>" 
													class="prices_options" 
													data-group-id="<?php echo $decor_radio_counter; ?>">
												<?php foreach ($sf['sub_fields'] as $price_option):
													$price_val = isset($gf_value[$sf['name']][$price_option['name']]) ? $gf_value[$sf['name']][$price_option['name']] : '';
													if (!empty($price_val)):
														$opt_price = preg_replace('/[^\d]/', '', mb_substr($price_val, 5));
														$opt_quantity = mb_substr($price_val, 0, 4);
												?>
													<option value="<?php echo esc_attr($opt_price); ?>" 
															data-quantity="<?php echo esc_attr($opt_quantity); ?>"
															data-price="<?php echo esc_attr($opt_price); ?>">
														<span class="decor_quantity_option"><?php echo esc_html($opt_quantity); ?></span>
														<span class="decor_price_option"><?php echo esc_html(mb_substr($price_val, 5)); ?></span>
													</option>
												<?php 
													endif;
												endforeach; ?>
											</select>
											<img src="<?php echo esc_url(get_bloginfo('template_url') . '/assets/imgs/main/product_card/open_price_options_icon.png'); ?>" alt="" class="select_prices_icon open">
										</div>
									<?php endif;
								endforeach; ?>
							</div>
						</div>
					<?php 
						endif;
					endforeach; ?>
				</div>
			<?php 
				endif;
			endforeach; 
			?>

				<div class="product_price_window_descr">
					<button class="price_window_scroll_button" id="price_window_scroll_up">Рассчитать стоимость</button>
					<p>Итоговая стоимость и параметры отобразятся в синей плашке</p>
				</div>
			</div>
        </div>
    </section>
</div>

<script>
jQuery(document).ready(function($) {
    
    function updateTotalPrice() {
        var totalPrice = 0;
        
        // Собираем ВСЕ цены из синей плашки (кроме итоговой)
        $('.order_option .price').each(function() {
            var $this = $(this);
            var isFinalPrice = $this.closest('.order_option').find('.final_price_title').length > 0;
            
            if (!isFinalPrice) {
                var priceText = $this.text().replace(/[^\d]/g, '');
                var price = parseInt(priceText) || 0;
                if (price > 0) {
                    totalPrice += price;
                }
            }
        });
        
        $('#full_price').text(totalPrice.toLocaleString('ru-RU') + ' р.');
        $('#final_price_value').text(totalPrice.toLocaleString('ru-RU') + ' р.');
        
        return totalPrice;
    }
    
    // Функция обновления цены для конкретной группы декора
    function updateDecorPrice(groupId, price, quantity) {
        var $priceBlock = $('#decor_price_' + groupId);
        var $quantityBlock = $('#decor_quantity_' + groupId);
        
        if ($priceBlock.length) {
            $priceBlock.text(price.toLocaleString('ru-RU') + ' р.');
            $priceBlock.data('price', price);
        }
        
        if ($quantityBlock.length && quantity) {
            $quantityBlock.text(quantity);
        }
        
        updateTotalPrice();
    }
    
    // Обработчик для radio в блоках декора
    $(document).on('change', '.third_type .option_button', function() {
        var $this = $(this);
        var groupId = $this.data('group-id');
        var $option = $this.closest('.option');
        var $select = $option.find('.prices_options');
        
        if ($select.length) {
            // Если есть select, берем цену из выбранной опции select
            var selectedOption = $select.find('option:selected');
            var price = parseInt(selectedOption.data('price')) || 0;
            var quantity = selectedOption.data('quantity') || '';
            updateDecorPrice(groupId, price, quantity);
        } else {
            // Если нет select, ищем цену в option_labels
            var priceElement = $option.find('.option_column_name.price');
            var price = 0;
            var quantity = '';
            
            if (priceElement.length) {
                var priceText = priceElement.text().replace(/[^\d]/g, '');
                price = parseInt(priceText) || 0;
            }
            
            updateDecorPrice(groupId, price, quantity);
        }
    });
    
    // Обработчик для select в блоках декора
    $(document).on('change', '.prices_options', function() {
        var $select = $(this);
        var groupId = $select.data('group-id');
        var selectedOption = $select.find('option:selected');
        var price = parseInt(selectedOption.data('price')) || 0;
        var quantity = selectedOption.data('quantity') || '';
        
        updateDecorPrice(groupId, price, quantity);
        
        // Отмечаем соответствующий radio
        var $radio = $select.closest('.option').find('.option_button[data-group-id="' + groupId + '"]');
        if ($radio.length) {
            $radio.prop('checked', true);
        }
    });
    
    // Обработчик для обычных radio (не из декора)
    $(document).on('change', '.options_block:not(.third_type) .option_button', function() {
        var $this = $(this);
        var $option = $this.closest('.option');
        var $block = $option.closest('.options_block');
        var optionTitle = $block.find('.options_title').text();
        
        var price = 0;
        var priceElement = $option.find('.option_column_name.price');
        if (priceElement.length) {
            var priceText = priceElement.text().replace(/[^\d]/g, '');
            price = parseInt(priceText) || 0;
            if (priceElement.data('first-price')) {
                price = parseInt(priceElement.data('first-price')) || price;
            }
        }
        
        var $orderOption = $('.order_option .title:contains("' + optionTitle + '")').closest('.order_option');
        
        if ($orderOption.length) {
            $orderOption.find('.price').text(price.toLocaleString('ru-RU') + ' р.');
            $orderOption.find('.price').data('price', price);
        } else if (price > 0) {
            $('.order_options').prepend(
                '<div class="order_option">' +
                    '<div class="title">' + optionTitle + '</div>' +
                    '<div class="price" data-price="' + price + '">' + price.toLocaleString('ru-RU') + ' р.</div>' +
                '</div>'
            );
        }
        
        updateTotalPrice();
    });
    
    $('#price_window_scroll_up').on('click', function(e) {
        e.preventDefault();
        updateTotalPrice();
        $('html, body').animate({
            scrollTop: $('.product_order_window').offset().top - 100
        }, 500);
    });
    
    setTimeout(function() {
        // Инициализация обычных radio
        $('.options_block:not(.third_type)').each(function() {
            if ($(this).find('.option_button:checked').length === 0) {
                var firstRadio = $(this).find('.option_button').first();
                if (firstRadio.length) {
                    firstRadio.prop('checked', true);
                    firstRadio.trigger('change');
                }
            }
        });
        
        // Инициализация select в декоре
        $('.prices_options').each(function() {
            var $select = $(this);
            if (!$select.val()) {
                $select.find('option:first').prop('selected', true);
            }
            $select.trigger('change');
        });
        
        updateTotalPrice();
    }, 500);
});
</script>

<?php get_footer(); ?>