<?php

namespace CodeDelivery\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProductRepository
 * @package namespace CodeDelivery\Repositories;
 */
interface ProductRepository extends RepositoryInterface
{
    /**
     * Try to find the model. If it fails, throw a ModelNotFoundException
     *
     * @param $id
     * @return mixed
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail($id);

    /**
     * Search a product
     * 
     * @param $product
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search($product);
}
