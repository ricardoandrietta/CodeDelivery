<?php

namespace CodeDelivery\Http\Controllers\Admin;

use CodeDelivery\Http\Controllers\Controller;
use CodeDelivery\Http\Requests\Admin\ClientCreateRequest;
use CodeDelivery\Http\Requests\Admin\ClientUpdateRequest;
use CodeDelivery\Models\Client;
use CodeDelivery\Models\User;
use CodeDelivery\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Session;

class ClientController extends Controller
{
    private $client;

    public function __construct(ClientRepository $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $clientCollection = $this->client->paginate(10);
        return view('admin.client.index', compact('clientCollection'));
    }

    public function create()
    {
        $client = new Client();
        $client->user = new User();
        return view('admin.client.create', compact('client'));
    }

    public function store(ClientCreateRequest $request, User $user, Client $client)
    {
        $arrRrequest = collect($request->except(['password_confirmation']))
            ->merge(['role' => 'client']);

        $client->fill($arrRrequest->all());
        $user->fill($arrRrequest->all())->save();
        $user->client()->save($client);

        Session::flash('success', trans('crud.success.saved'));

        return redirect()->route('admin.client.index');
    }

    public function edit($id)
    {
        try {
            //$client = $this->client->findOrFail($id);
            $client = $this->client->find($id);
            return view('admin.client.update', compact('client'));
        } catch (ModelNotFoundException $e) {
            Session::flash('error', trans('crud.record_not_found', ['action' => 'edited']));
            return redirect()->route('admin.client.index');
        }
    }

    public function update(ClientUpdateRequest $request, $id)
    {
        $except = collect(['password_confirmation']);
        if ($request->has('password') === false) {
            $except->push('password');
        }

        //Removing 'password_confirmation' and 'password' when applicable
        $arrRequest = $request->except($except->all());

        try {
            $client = $this->client->findOrFail($id)->fill($arrRequest);
            $user = $client->user->fill($arrRequest);

            $client->save();
            $user->save();

            Session::flash('success', trans('crud.success.saved'));
        } catch (ModelNotFoundException $e) {
            Session::flash('error', trans('crud.record_not_found', ['action' => 'updated']));
        }

        return redirect()->route('admin.client.index');
    }

    public function delete($id)
    {
        try {

            $client = $this->client->findOrFail($id);
            if ($client->orders->count() === 0)
            {
                $client->delete();
                Session::flash('success', trans('crud.success.deleted'));
            }
            else
            {
                Session::flash('error', trans_choice('crud.client_has_orders', $client->orders->count(), ['qtdOrders' => $client->orders->count()]));   
            }

        } catch (ModelNotFoundException $e) {
            Session::flash('error', trans('crud.record_not_found', ['action' => 'deleted']));
        }

        return redirect()->route('admin.client.index');
    }


}
