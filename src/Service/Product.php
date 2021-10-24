<?php

declare(strict_types=1);

namespace Gandung\Tokopedia\Service;

use InvalidArgumentException;

use function http_build_query;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Product extends Resource
{
    /**
     * @var int
     */
    const SORT_DEFAULT = 1;

    /**
     * @var int
     */
    const SORT_LAST_UPDATE_PRODUCT = 2;

    /**
     * @var int
     */
    const SORT_HIGHEST_SOLD = 3;

    /**
     * @var int
     */
    const SORT_LOWEST_SOLD = 4;

    /**
     * @var int
     */
    const SORT_HIGHEST_PRICE = 5;

    /**
     * @var int
     */
    const SORT_LOWEST_PRICE = 6;

    /**
     * @var int
     */
    const SORT_PRODUCT_NAME_ASCENDING = 7;

    /**
     * @var int
     */
    const SORT_PRODUCT_NAME_DESCENDING = 8;

    /**
     * @var int
     */
    const SORT_FEWEST_STOCK = 9;

    /**
     * @var int
     */
    const SORT_HIGHEST_STOCK = 10;

    /**
     * Get product info (can filtered by product ID and product URL optionally).
     *
     * @param int $productID Product ID.
     * @param string $productUrl Product URL.
     * @return string
     */
    public function getProductInfo(int $productID = 0, string $productUrl = '')
    {
        $endpoint = sprintf(
            '/inventory/v1/fs/%d/product/info',
            $this->getFulfillmentServiceID()
        );

        $queryParams = [];

        if (!empty($productID)) {
            $queryParams['product_id'] = $productID;
        }

        if (!empty($productUrl)) {
            $queryParams['product_url'] = $productUrl;
        }

        $serialized = http_build_query($queryParams);
        $response   = $this->call(
            'GET',
            sprintf(
                '%s%s',
                $endpoint,
                empty($serialized) ? '' : ('?' . $serialized)
            )
        );

        return $this->getContents($response);
    }

    /**
     * Get product info by SKU.
     *
     * @param string $sku Product SKU.
     * @return string
     */
    public function getProductInfoBySKU(string $sku)
    {
        $endpoint = sprintf(
            "/inventory/v1/fs/%d/product/info",
            $this->getFulfillmentServiceID()
        );

        $queryParams        = [];
        $queryParams['sku'] = $sku;

        $response = $this->call(
            'GET',
            sprintf("%s?%s", $endpoint, http_build_query($queryParams))
        );

        return $this->getContents($response);
    }

    /**
     * Get product info from related shop ID.
     *
     * @param int $shopID Shop ID.
     * @param int $page Current page.
     * @param int $perPage How much item showed per page.
     * @param int $sort Sort type.
     * @return string
     * @throws InvalidArgumentException When given an invalid sort type.
     */
    public function getProductInfoFromRelatedShopID(
        int $shopID,
        int $page,
        int $perPage,
        int $sort
    ) {
        if ($page <= 0) {
            throw new InvalidArgumentException(
                "Page number cannot be less or equal to zero."
            );
        }

        if ($perPage <= 0) {
            throw new InvalidArgumentException(
                "Per page number cannot be less or equal to zero."
            );
        }

        $this->validateSortOptions($sort);

        $endpoint = sprintf(
            '/inventory/v1/fs/%d/product/info',
            $this->getFulfillmentServiceID()
        );

        $queryParams             = [];
        $queryParams['shop_id']  = $shopID;
        $queryParams['page']     = $page;
        $queryParams['per_page'] = $perPage;
        $queryParams['sort']     = $sort;

        $response = $this->call(
            'GET',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams))
        );

        return $this->getContents($response);
    }

    /**
     * Get all product variants by category ID.
     *
     * @param int $categoryID Category ID.
     * @return string
     */
    public function getAllVariantsByCategoryID(int $categoryID)
    {
        $endpoint = sprintf(
            '/inventory/v1/fs/%d/category/get_variant',
            $this->getFulfillmentServiceID()
        );

        $queryParams           = [];
        $queryParams['cat_id'] = $categoryID;

        $response = $this->call(
            'GET',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams))
        );

        return $this->getContents($response);
    }

    /**
     * Get all product variants by category ID (V2).
     *
     * @param  int $categoryID Category ID.
     * @return string
     */
    public function getAllVariantsByCategoryIDV2(int $categoryID)
    {
        $endpoint = sprintf(
            '/inventory/v2/fs/%d/category/get_variant',
            $this->getFulfillmentServiceID()
        );

        $queryParams           = [];
        $queryParams['cat_id'] = $categoryID;

        $response = $this->call(
            'GET',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams))
        );

        return $this->getContents($response);
    }

    /**
     * Get all product variants by product ID.
     *
     * @param int $productID Product ID.
     * @return string
     */
    public function getAllVariantsByProductID(int $productID)
    {
        $response = $this->call(
            'GET',
            sprintf(
                '/inventory/v1/fs/%d/product/variant/%d',
                $this->getFulfillmentServiceID(),
                $productID
            )
        );

        return $this->getContents($response);
    }

    /**
     * Get all etalase.
     *
     * @param int $shopID Shop ID.
     * @return string
     */
    public function getAllEtalase(int $shopID)
    {
        $endpoint = sprintf(
            '/inventory/v1/fs/%d/product/etalase',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'GET',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams))
        );

        return $this->getContents($response);
    }

    /**
     * Create products.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function createProducts(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/v2/products/fs/%d/create',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'POST',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Create products v3
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function createProductsV3(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/v3/products/fs/%d/create',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'POST',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Edit product.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function editProduct(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/v2/products/fs/%d/edit',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'PATCH',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Edit product v3.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function editProductV3(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/v3/products/fs/%d/edit',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'PATCH',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Check product upload status.
     *
     * @param int $shopID Shop ID.
     * @param int $uploadID Product upload ID.
     * @return string
     */
    public function checkUploadStatus(int $shopID, int $uploadID)
    {
        $endpoint = sprintf(
            '/v2/products/fs/%d/status/%d',
            $this->getFulfillmentServiceID(),
            $uploadID
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'GET',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams))
        );

        return $this->getContents($response);
    }

    /**
     * Set product status to active.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function setActiveProduct(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/v1/products/fs/%d/active',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'POST',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Set product status to inactive.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function setInactiveProduct(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/v1/products/fs/%d/inactive',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'POST',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Update price for defined product.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function updatePriceOnly(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/inventory/v1/fs/%d/price/update',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'POST',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Update stock for defined product.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function updateStockOnly(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/inventory/v1/fs/%d/stock/update',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'POST',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * Delete a product or list of products.
     *
     * @param int $shopID Shop ID.
     * @param array $data
     * @return string
     */
    public function deleteProduct(int $shopID, array $data)
    {
        $endpoint = sprintf(
            '/v3/products/fs/%d/delete',
            $this->getFulfillmentServiceID()
        );

        $queryParams            = [];
        $queryParams['shop_id'] = $shopID;

        $response = $this->call(
            'POST',
            sprintf('%s?%s', $endpoint, http_build_query($queryParams)),
            $data
        );

        return $this->getContents($response);
    }

    /**
     * @return void
     * @throws InvalidArgumentException When sort options is invalid.
     */
    private function validateSortOptions(int $sort)
    {
        switch ($sort) {
            case self::SORT_DEFAULT:
            case self::SORT_LAST_UPDATE_PRODUCT:
            case self::SORT_HIGHEST_SOLD:
            case self::SORT_LOWEST_SOLD:
            case self::SORT_HIGHEST_PRICE:
            case self::SORT_LOWEST_PRICE:
            case self::SORT_PRODUCT_NAME_ASCENDING:
            case self::SORT_PRODUCT_NAME_DESCENDING:
            case self::SORT_FEWEST_STOCK:
            case self::SORT_HIGHEST_STOCK:
                break;
            default:
                throw new InvalidArgumentException("Invalid sort options.");
        }

        return;
    }
}
