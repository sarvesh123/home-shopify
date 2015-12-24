<?php

function getProductsArr($Reader) {
    foreach ($Reader as $key => $Row) {
        if ($key !== 0) {
            $products[] = createMapping($Row);
        }
    }
    return $products;
}

function createMapping($field) {
    $arr['title'] = $field[2] . ' ' . $field[0];
    $arr['body_html'] = $field[1];
    $arr['vendor'] = $field[2];
    $arr['product_type'] = $field[3];
    $arr['tags'] = getTags($field[4]);    
    $arr['images'] = array(getImageUrl(IMAGE_BASE_URL, $field[21]));  
    $arr['metafields_global_title_tag'] = 'Paramount BP ' . $arr['title'];
    $arr['metafields_global_description_tag'] = $field[28]; 
    $arr['variants'] = array(getbasicVariants($field));
//    //variants
//    $basicVariants = array(getbasicVariants($field));
//    $variants = getAllVariants($field, $pricePlans, $basicVariants);
//    $arr['variants'] = $variants;
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

function getInventoryQuantity($field) {
    return ($field[13] + $field[14] + $field[15] + $field[16]);
}

function getbasicVariants($values) {
    return array(
        'compare_at_price' => $values[20],
        'grams' => getWeightGrams($values[12]),
        'inventory_quantity' => getInventoryQuantity($values),
        'sku' => trim($values[6]),
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

function getAllVariants($product, $pricePlans, $basicVariants) {
    $sku = trim($product[6]);
    $planArr = array(
        'Default Title' => 'X4C2',
        'Default Title (X4C3)' => 'X4C3',
        'Default Title (X6C1)' => 'X6C1',
        'Default Title (X6C2)' => 'X6C2',
        'Default Title (X6C3)' => 'X6C3'
    );
    foreach($planArr as $key => $value) {
        $pricePlan = createPricePlanPostData($key, $pricePlans[ $sku ][$value], $sku);
        $pricePlanPostData[] = array_merge($pricePlan, $basicVariants[0]);
    }
    return $pricePlanPostData;
}

function createPricePlanPostData($option1, $price, $sku) {
    return array(
            'option1' => $option1,
            'price' => $price,
            'sku' => $sku
        );
}

function createVariantData($products, $pricePlans) {
//foreach product get variant
//    update variant with price plan
//    write put api
    return $products;
}