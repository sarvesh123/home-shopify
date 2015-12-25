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