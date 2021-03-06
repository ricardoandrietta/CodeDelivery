<?php

namespace CodeDelivery\Repositories;

use CodeDelivery\Models\Order;
use CodeDelivery\Presenters\OrderPresenter;
use CodeDelivery\Validators\OrderValidator;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class OrderRepositoryEloquent
 * @package namespace CodeDelivery\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    protected $skipPresenter = true;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Try to find the model. If it fails, throw a ModelNotFoundException
     *
     * @param $id
     * @return mixed
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @return Options to populate Status Combo for Orders
     */
    public function getOrderStatusOptions()
    {
        return [0 => 'Canceled', 1 => 'In Progress', 2 => 'Shipping', 3 => 'Finalized'];
    }

    /**
     * Get all User Orders by User ID
     *
     * @param $userID
     * @return array|static[]
     */
    public function getByUserID($userID)
    {
        $orderTable = DB::table('orders');
        $collection = $orderTable
            ->join('clients', 'clients.id', '=', 'orders.client_id')
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->where('users.id', '=', $userID)->get(['orders.*']);

        return $collection;
    }

    /**
     * Get a Order by ID and Deliveryman ID
     *
     * @param $orderID
     * @param $deliverymanID
     * @return Collection
     */
    public function getByOrderIDAndDeliverymanID($orderID, $deliverymanID)
    {
        try {
            $order = $this->with(['client', 'orderItems'])
                ->findWhere([
                    'id' => $orderID,
                    'user_deliveryman_id' => $deliverymanID,
                ]);

            if ($order instanceof Collection)
                return $order->first();

            //Via "Presenter" returns an Array
            if (isset($order['data']) && count($order['data'] == 1))
            {
                //Forcing first item to be an object
                $order['data'] = $order['data'][0];
                return $order;
            }

            return new Collection();

        } catch (ModelNotFoundException $e) {
            return new Collection();
        }
    }

    public function presenter()
    {
        return OrderPresenter::class;
    }
}
