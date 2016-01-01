<?php

function getProducts() {
    global $conn;

    $sql = "SELECT * FROM " . TABLE_PRODUCTS;
    $result = mysql_query( $sql, $conn );

    $products = array();
    if (mysql_num_rows($result)) {
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $products[] = createMapping($row);  
        }
        return $products;
    }
    else {
        return false;
    }    
}

function createMapping($field) {
    $arr['title'] = $field['vendor'] . ' ' . $field['title'];
    $arr['body_html'] = $field['description'];
    $arr['vendor'] = $field['vendor'];
    $arr['product_type'] = $field['type'];
    $arr['tags'] = getTags($field['tags']);    
    $arr['images'] = array(getImageUrl(IMAGE_BASE_URL, $field['image']));  
    $arr['metafields_global_title_tag'] = 'Paramount BP ' . $arr['title'];
    $arr['metafields_global_description_tag'] = $field['meta_description']; 
    $arr['variants'] = array(getbasicVariants($field));

    return $arr;
}

function getTags($tags) {
    $tagArr = explode(';', $tags);
    foreach ($tagArr as $value) {
        $tempArr[] = trim($value);
    }
    return implode(', ', $tempArr);
}

function getImageUrl($baseUrl, $imageName) {
    return array('src' => $baseUrl . $imageName);
}

function getbasicVariants($values) {
    return array(
        'compare_at_price' => $values['compare_price'],
        'grams' => getWeightGrams($values['weight']),
        'inventory_quantity' => $values['quantity'],
        'sku' => trim($values['sku']),
        'taxable' => true
    );
}

function getWeightGrams($weight) {
    return $weight * 100;
}

function getPricePlans($pricePlans) {
    $productPricePlan = array();
    foreach($pricePlans as $val) {
        $itemNo = $val[1];
        $productPricePlan[$itemNo]['X4C2'] = $val[23];
        $productPricePlan[$itemNo]['X4C3'] = $val[24];
        $productPricePlan[$itemNo]['X6C1'] = $val[28];
        $productPricePlan[$itemNo]['X6C2'] = $val[29];
        $productPricePlan[$itemNo]['X6C3'] = $val[30];
    }
    return $productPricePlan;
}

function getVariants($products, $pricePlans) {
    $variantData = array();
    foreach ($products as $key => $product) {
        $productVariants = $product['variants'];
        $variant = getVariantArr($productVariants, $pricePlans);
        $variantData = array_merge($variantData, $variant);
    }
    return $variantData;
}

function getVariantArr($productVariants, $pricePlans) {
    foreach ($productVariants as $key => $variant) {
        $sku = $variant['sku'];
        $pricePlan = $pricePlans[$sku];
        $variantData[] = getVariantData($variant, $pricePlan);
    }
    return $variantData;
}

function getVariantData($variant, $pricePlan) {

    switch ($variant['option1']) {
        case PRICE_X4C2:
            $data = makeVariantArr($variant['id'], $pricePlan['X4C2']);
        break;

        case PRICE_X4C3:
            $data = makeVariantArr($variant['id'], $pricePlan['X4C3']);
        break;

        case PRICE_X6C1:
            $data = makeVariantArr($variant['id'], $pricePlan['X6C1']);
        break;

        case PRICE_X6C2:
            $data = makeVariantArr($variant['id'], $pricePlan['X6C2']);
        break;

        case PRICE_X6C3:
            $data = makeVariantArr($variant['id'], $pricePlan['X6C3']);
        break;
    }

    return $data;
}

function makeVariantArr($id, $price) {
    return array(
            'id' => $id,
            'price' => $price
        );
}

function getLatestFile($path) {

    $latest_ctime = 0;
    $latest_filename = '';    

    $d = dir($path);
    while (false !== ($entry = $d->read())) {
      $filepath = "{$path}/{$entry}";
      // could do also other checks than just checking whether the entry is a file
      if (is_file($filepath) && filemtime($filepath) > $latest_ctime) {
        $latest_ctime = filemtime($filepath);
        $latest_filename = $entry;
      }
    }
    return $latest_filename;
}

function sumQuantity($values) {
    return ($values[13] + $values[14] + $values[15] + $values[16]);
}

function fileUnread($fileName) {
    global $conn;

    $sql = "SELECT id FROM " . TABLE_READ_FILES . " WHERE name = '" . $fileName . "'";
    $result = mysql_query( $sql, $conn );

    if (mysql_num_rows($result)) {
        return false;
    }
    else {
        return true;
    }    
}

function markFileRead($fileName, $path) {
    global $conn;

    $sql = "INSERT INTO " . TABLE_READ_FILES . " (name, path) VALUES ('" . $fileName . "', '" . mysql_real_escape_string($path) . "') ";

    mysql_query( $sql, $conn );
}
