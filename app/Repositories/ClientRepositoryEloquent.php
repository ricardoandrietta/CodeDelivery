<?php

namespace CodeDelivery\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use CodeDelivery\Repositories\ClientRepository;
use CodeDelivery\Models\Client;
use CodeDelivery\Validators\ClientValidator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ClientRepositoryEloquent
 * @package namespace CodeDelivery\Repositories;
 */
class ClientRepositoryEloquent extends BaseRepository implements ClientRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Client::class;
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
     * @param $userID
     * @return Client
     */
    public function getByUserID($userID)
    {
        $client = $this->model->where('user_id', '=', $userID)->first();
        return $client;
    }
}
