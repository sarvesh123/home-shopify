<?php

function getProducts() {
    global $mysqli;

    $query = "SELECT * FROM " . TABLE_PRODUCTS . " WHERE tags != 'Keywords'";
    $result = $mysqli->query($query);

    $products = array();
    if ($mysqli->affected_rows) {
        while($row = $result->fetch_array()) {
            $products[] = createMapping($row);  
        }
        return $products;
    }
    else {
        return false;
    }    
}

function createMapping($field) {
    $arr['title'] = $field['vendor'] . ' ' . $field['name'];
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
    $variantData = array();
    foreach ($productVariants as $key => $variant) {
        $sku = $variant['sku'];
        if ( array_key_exists($sku, $pricePlans) ) {
            $pricePlan = $pricePlans[$sku];
            $variantData[] = getVariantData($variant, $pricePlan);
        }
    }
    return $variantData;
}

function getVariantData($variant, $pricePlan) {

    switch ($variant['option1']) {
        case PRICE_X4C2:
            $data = makeVariantArr($variant, $pricePlan['X4C2']);
        break;

        case PRICE_X4C3:
            $data = makeVariantArr($variant, $pricePlan['X4C3']);
        break;

        case PRICE_X6C1:
            $data = makeVariantArr($variant, $pricePlan['X6C1']);
        break;

        case PRICE_X6C2:
            $data = makeVariantArr($variant, $pricePlan['X6C2']);
        break;

        case PRICE_X6C3:
            $data = makeVariantArr($variant, $pricePlan['X6C3']);
        break;
    }

    return $data;
}

function makeVariantArr($variant, $price) {
    return array(
            'id' => $variant['id'],
            'sku' => $variant['sku'],
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
    return array('mtime' => $latest_ctime, 'name' => $latest_filename);
}

function sumQuantity($values) {
    return ($values[14] + $values[15] + $values[16] + $values[17]);
}

function fileUnread($fileName) {
    global $mysqli;

    $query = "SELECT id FROM " . TABLE_READ_FILES . " WHERE name = '" . $fileName . "'";

    $mysqli->query($query);

    if ($mysqli->affected_rows) {
        return false;
    }
    else {
        return true;
    }    
}

function saveFileParsedData($fileData, $path, $records_count, $read_count) {
    global $mysqli;

    $query = "INSERT INTO " . TABLE_READ_FILES . " 
        (
            name, 
            path, 
            records_count, 
            download_time, 
            read_date, 
            read_count
        ) 
        VALUES 
        (   '" . $fileData['name'] . "', 
            '" . $mysqli->real_escape_string($path) . "', 
            '" . $records_count . "',
            '" . date('Y-m-d H:i:s', $fileData['mtime']) . "',
            '" . date('Y-m-d H:i:s') . "',
            '" . $read_count . "'
        )";

    $mysqli->query($query);
}

function truncateTable($tableName) {
    global $mysqli;

    $query = "TRUNCATE TABLE " . $tableName;

    $mysqli->query($query);
}

function updateShopifyPosted() {
    global $mysqli;

    $query = "UPDATE " . TABLE_READ_FILES . " SET shopify_posted = '" . date('Y-m-d H:i:s') . "' WHERE shopify_posted IS NULL";

    $mysqli->query($query);
}

function getShopifyProducts($shopify) {
    try
    {
        # Making an API request can throw an exception
        $products = $shopify('GET /admin/products.json', array('published_status'=>'published'));
        return $products;
    }
    catch (shopify\ApiException $e)
    {
        # HTTP status code was >= 400 or response contained the key 'errors'
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }
    catch (shopify\CurlException $e)
    {
        # cURL error
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }
}

function createShopifyCompareArr($products) {
    $shopifyArr = array();
    foreach ($products as $key => $product) {
        $shopifyArr[$key]['id'] = $product['id'];
        $shopifyArr[$key]['sku'] = $product['variants'][0]['sku'];
    }
    return $shopifyArr;
}

function skuMatch($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && skuMatch($needle, $item, $strict))) {
            return (isset($item['id']) ? $item['id']:$item);
        }
    }

    return false;
}

function createShopifyProduct($shopify, $data) {
    try
    {
        # Making an API request can throw an exception
        $response = $shopify('POST /admin/products.json', array(), $data);
        print_r($response);
    }
    catch (shopify\ApiException $e)
    {
        # HTTP status code was >= 400 or response contained the key 'errors'
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }
    catch (shopify\CurlException $e)
    {
        # cURL error
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }
}

function updateShopifyProduct($shopify, $data, $productId) {
    try
    {
        # Making an API request can throw an exception
        $response = $shopify('PUT /admin/products/' . $productId . '.json', array(), $data);
        print_r($response);
    }
    catch (shopify\ApiException $e)
    {
        # HTTP status code was >= 400 or response contained the key 'errors'
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }
    catch (shopify\CurlException $e)
    {
        # cURL error
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }
}

function updateVariants($shopify, $data, $variantId) {
    try
    {
        # Making an API request can throw an exception
        $response = $shopify('PUT /admin/variants/' . $variantId . '.json', array(), $data);
        print_r($response);
    }
    catch (shopify\ApiException $e)
    {
        # HTTP status code was >= 400 or response contained the key 'errors'
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }
    catch (shopify\CurlException $e)
    {
        # cURL error
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
    }    
}

function getProductBySku($sku) {
    global $mysqli;

    $query = "SELECT compare_price, weight, quantity, sku FROM " . TABLE_PRODUCTS . " WHERE sku = '" . $sku . "'";
    $result = $mysqli->query($query);

    $products = array();
    if ($mysqli->affected_rows) {
        return $result->fetch_assoc();
    }
    else {
        return false;
    }    
}