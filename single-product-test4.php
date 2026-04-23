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

					<!-- Доставка -->
					<div class="order_option delivery_option">
						<div class="title">
							Доставка: <span class="delivery-value">Самовывоз</span>
						</div>
						<div class="price" id="delivery_price" data-price="0">0 р.</div>
					</div>	
						
						
					<!-- Дополнительное оформление (суммируется) -->
					<div class="order_option extra_options_block">
						<div class="title">
							Доп. оформление: <span class="extra-options-list">—</span>
						</div>
						<div class="price" id="extra_price" data-price="0">0 р.</div>
					</div>
						
						
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
			
			<!-- Выбор материала (тип 2, индекс 0) - radio кнопки -->
			<?php if (!empty($field_groups_2) && isset($field_groups_2[0])): 
				$fg = $field_groups_2[0];
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
							<input type="radio" class="option_button" name="material_radio" data-option-type="material">
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
															data-price="<?php echo esc_attr($opt_price); ?>"
															data-title="<?php echo esc_attr($opt_quantity . ' ' . $option_title); ?>">
														<?php echo esc_html(mb_substr($price_val, 5)); ?>
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
				
				<!-- Выбор доставки -->
				<div class="options_block second_type delivery_block">
					<h3 class="options_title">Выбор доставки</h3>

					<div class="option">
						<input type="radio" class="option_button delivery_radio" name="delivery" value="0" data-delivery-name="Самовывоз" checked>
						<div class="option_labels">
							<div class="option_column_name">Самовывоз</div>
							<div class="option_column_name price">Бесплатно</div>
						</div>
					</div>

					<div class="option">
						<input type="radio" class="option_button delivery_radio" name="delivery" value="2000" data-delivery-name="По Москве (от МКАД до 5км)">
						<div class="option_labels">
							<div class="option_column_name">По Москве (от МКАД до 5км)</div>
							<div class="option_column_name price">2 000 р.</div>
						</div>
					</div>

					<div class="option">
						<input type="radio" class="option_button delivery_radio" name="delivery" value="3000" data-delivery-name="По МО (от МКАД до 50км)">
						<div class="option_labels">
							<div class="option_column_name">По МО (от МКАД до 50км)</div>
							<div class="option_column_name price">3 000 р.</div>
						</div>
					</div>

					<div class="option">
						<input type="radio" class="option_button delivery_radio" name="delivery" value="4000" data-delivery-name="По МО (от МКАД до 100км)">
						<div class="option_labels">
							<div class="option_column_name">По МО (от МКАД до 100км)</div>
							<div class="option_column_name price">4 000 р.</div>
						</div>
					</div>

					<div class="option">
						<input type="radio" class="option_button delivery_radio" name="delivery" value="6000" data-delivery-name="По МО (от МКАД до 150км)">
						<div class="option_labels">
							<div class="option_column_name">По МО (от МКАД до 150км)</div>
							<div class="option_column_name price">6 000 р.</div>
						</div>
					</div>

					<div class="option">
						<input type="radio" class="option_button delivery_radio" name="delivery" value="5000" data-delivery-name="По России (любой регион)">
						<div class="option_labels">
							<div class="option_column_name">По России (любой регион)</div>
							<div class="option_column_name price">от 5 000 р.</div>
						</div>
					</div>
				</div>
				
			<?php 
				endif;
			endforeach; 
			?>
			
			<!-- Дополнительное оформление (checkbox) -->
			<div class="options_block extra_options">
				<h3 class="options_title">Дополнительное оформление</h3>
				<p class="extra_note">(можно выбрать несколько вариантов)</p>

				<div class="option">
					<input type="checkbox" class="extra_checkbox" data-extra-name="Эпитафия" data-extra-price="1000">
					<div class="option_labels">
						<div class="option_column_name">Эпитафия</div>
						<div class="option_column_name price">1 000 р.</div>
					</div>
				</div>

				<div class="option">
					<input type="checkbox" class="extra_checkbox" data-extra-name="Крестик" data-extra-price="3000">
					<div class="option_labels">
						<div class="option_column_name">Крестик</div>
						<div class="option_column_name price">3 000 р.</div>
					</div>
				</div>

				<div class="option">
					<input type="checkbox" class="extra_checkbox" data-extra-name="Цветы" data-extra-price="2500">
					<div class="option_labels">
						<div class="option_column_name">Цветы</div>
						<div class="option_column_name price">2 500 р.</div>
					</div>
				</div>

				<div class="option">
					<input type="checkbox" class="extra_checkbox" data-extra-name="Виньетка" data-extra-price="1900">
					<div class="option_labels">
						<div class="option_column_name">Виньетка</div>
						<div class="option_column_name price">1 900 р.</div>
					</div>
				</div>

				<div class="option">
					<input type="checkbox" class="extra_checkbox" data-extra-name="Свеча" data-extra-price="1500">
					<div class="option_labels">
						<div class="option_column_name">Свеча</div>
						<div class="option_column_name price">1 500 р.</div>
					</div>
				</div>

				<div class="option">
					<input type="checkbox" class="extra_checkbox" data-extra-name="Икона" data-extra-price="4000">
					<div class="option_labels">
						<div class="option_column_name">Икона</div>
						<div class="option_column_name price">4 000 р.</div>
					</div>
				</div>

				<div class="option">
					<input type="checkbox" class="extra_checkbox" data-extra-name="Картинка" data-extra-price="4000">
					<div class="option_labels">
						<div class="option_column_name">Картинка</div>
						<div class="option_column_name price">4 000 р.</div>
					</div>
				</div>
			</div>
			

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
    
	// Функция обновления дополнительного оформления
	function updateExtraOptions() {
		var totalExtraPrice = 0;
		var selectedExtras = [];

		$('.extra_checkbox:checked').each(function() {
			var $this = $(this);
			var price = parseInt($this.data('extra-price')) || 0;
			var name = $this.data('extra-name');

			totalExtraPrice += price;
			selectedExtras.push(name);
		});

		// Обновляем блок в синей плашке
		var $extraBlock = $('.extra_options_block');
		if ($extraBlock.length) {
			$extraBlock.find('.price').text(totalExtraPrice.toLocaleString('ru-RU') + ' р.');
			$extraBlock.find('.price').data('price', totalExtraPrice);

			var extrasText = selectedExtras.length > 0 ? selectedExtras.join(', ') : '—';
			$extraBlock.find('.extra-options-list').text(extrasText);
		}

		updateTotalPrice();
	}

	// Обработчик для чекбоксов дополнительного оформления
	$(document).on('change', '.extra_checkbox', function() {
		updateExtraOptions();
	});
	
	
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
    function updateDecorPrice(groupId, price, quantity, optionTitle) {
        var $priceBlock = $('#decor_price_' + groupId);
        var $quantityBlock = $('#decor_quantity_' + groupId);
        var $titleBlock = $('#decor_block_' + groupId).find('.decor_parameter');
        
        if ($priceBlock.length) {
            $priceBlock.text(price.toLocaleString('ru-RU') + ' р.');
            $priceBlock.data('price', price);
        }
        
        if ($quantityBlock.length && quantity) {
            $quantityBlock.text(quantity);
        }
        
        if ($titleBlock.length && optionTitle && optionTitle !== '') {
            $titleBlock.text(optionTitle);
        }
        
        updateTotalPrice();
    }
    
    // Обработчик для radio в блоках декора
    $(document).on('change', '.third_type .option_button', function() {
        var $this = $(this);
        var groupId = $this.data('group-id');
        var optionTitle = $this.data('option-title');
        var $option = $this.closest('.option');
        var $select = $option.find('.prices_options');
        
        if ($select.length) {
            var selectedOption = $select.find('option:selected');
            var price = parseInt(selectedOption.data('price')) || 0;
            var quantity = selectedOption.data('quantity') || '';
            updateDecorPrice(groupId, price, quantity, optionTitle);
        } else {
            var priceElement = $option.find('.option_column_name.price');
            var price = 0;
            if (priceElement.length) {
                var priceText = priceElement.text().replace(/[^\d]/g, '');
                price = parseInt(priceText) || 0;
            }
            updateDecorPrice(groupId, price, '', optionTitle);
        }
    });
    
    // Обработчик для select в блоках декора
    $(document).on('change', '.prices_options', function() {
        var $select = $(this);
        var groupId = $select.data('group-id');
        var selectedOption = $select.find('option:selected');
        var price = parseInt(selectedOption.data('price')) || 0;
        var quantity = selectedOption.data('quantity') || '';
        var optionTitle = selectedOption.data('title') || '';
        
        updateDecorPrice(groupId, price, quantity, optionTitle);
        
        var $radio = $select.closest('.option').find('.option_button[data-group-id="' + groupId + '"]');
        if ($radio.length) {
            $radio.prop('checked', true);
        }
    });
    
    // Обработчик для выбора доставки
    $(document).on('change', '.delivery_radio', function() {
        var $this = $(this);
        var price = parseInt($this.val()) || 0;
        var deliveryName = $this.data('delivery-name');
        
        var $deliveryBlock = $('.delivery_option');
        if ($deliveryBlock.length) {
            $deliveryBlock.find('.price').text(price.toLocaleString('ru-RU') + ' р.');
            $deliveryBlock.find('.price').data('price', price);
            $deliveryBlock.find('.delivery-value').text(deliveryName);
        }
        
        updateTotalPrice();
    });
    
    // Обработчик для обычных radio (материал, размер, полировка, цветник, подставка)
    $(document).on('change', '.options_block:not(.third_type):not(.delivery_block) .option_button', function() {
        var $this = $(this);
        var $option = $this.closest('.option');
        var $block = $option.closest('.options_block');
        var optionTitle = $block.find('.options_title').text();
        
        var selectedValue = '';
        var firstTextElement = $option.find('.option_column_name').first();
        if (firstTextElement.length && !firstTextElement.hasClass('price')) {
            selectedValue = firstTextElement.text().trim();
        }
        
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
            
            var $title = $orderOption.find('.title');
            if (selectedValue) {
                var cleanTitle = optionTitle;
                $title.html(cleanTitle + ': <span class="option-value">' + selectedValue + '</span>');
            }
        } else if (price > 0) {
            var titleHtml = optionTitle;
            if (selectedValue) {
                titleHtml = optionTitle + ': <span class="option-value">' + selectedValue + '</span>';
            }
            $('.order_options').prepend(
                '<div class="order_option">' +
                    '<div class="title">' + titleHtml + '</div>' +
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
	
	// Обработчик для чекбоксов дополнительного оформления
	$(document).on('change', '.extra_checkbox', function() {
		updateExtraOptions();
	});
    
    setTimeout(function() {
        // Инициализация обычных radio
        $('.options_block:not(.third_type):not(.delivery_block)').each(function() {
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
        
        // Инициализация доставки (по умолчанию Самовывоз)
        $('.delivery_radio[value="0"]').prop('checked', true).trigger('change');
        
		// Инициализация дополнительного оформления
    	updateExtraOptions();
		
        updateTotalPrice();
    }, 500);
});
</script>

<?php get_footer(); ?>
