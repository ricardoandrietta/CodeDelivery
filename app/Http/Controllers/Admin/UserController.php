<?php

namespace CodeDelivery\Http\Controllers\Admin;

use CodeDelivery\Http\Controllers\Controller;
use CodeDelivery\Http\Requests;
use CodeDelivery\Http\Requests\Admin\UserCreateRequest;
use CodeDelivery\Http\Requests\Admin\UserUpdateRequest;
use CodeDelivery\Models\User;
use CodeDelivery\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Session;

class UserController extends Controller
{
    private $user;
    private $roles = ['admin' => 'Admin', 'client' => 'Client', 'deliveryman' => 'Delivery Man'];

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $userCollection = $this->user->paginate(10);
        $roles = $this->roles;
        return view('admin.user.index', compact('userCollection', 'roles'));
    }

    public function create()
    {
        $user = new User();
        $roles = $this->roles;
        return view('admin.user.create', compact('roles', 'user'));
    }

    public function store(UserCreateRequest $request, User $user)
    {
        $user->fill($request->all())->save();
        Session::flash('success', trans('crud.success.saved'));
        return redirect()->route('admin.user.index');
    }

    public function edit($id)
    {
        try {
            $roles = $this->roles;
            $user = $this->user->findOrFail($id);
            return view('admin.user.update', compact('user','roles'));
        } catch (ModelNotFoundException $e) {
            Session::flash('error', trans('crud.record_not_found', ['action' => 'edited']));
            return redirect()->route('admin.user.index');
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $except = collect(['password_confirmation']);
        $hasPassword = true;
        if ($request->has('password') === false)
        {
            $except->push('password');
            $hasPassword = false;
        }

        //Removing 'password_confirmation' and 'password' when applicable
        $arrRequest = $request->except($except->all());

        if ($hasPassword === true)
        {
            $arrRequest['password'] = bcrypt($request->password);
        }

        try {
            $this->user->findOrFail($id)->fill($arrRequest)->save();
            Session::flash('success', trans('crud.success.saved'));
        } catch (ModelNotFoundException $e) {
            Session::flash('error', trans('crud.record_not_found', ['action' => 'updated']));
        }

        return redirect()->route('admin.user.index');
    }

    public function delete($id)
    {
        try {
            $user = $this->user->findOrFail($id);

            if (is_null($user->client) || $user->client->orders->count() === 0)
            {
                $user->client()->delete();
                $user->delete();
                Session::flash('success', trans('crud.success.deleted'));
            }
            else
            {
                Session::flash('error', trans_choice('crud.client_has_orders', $user->client->orders->count(), ['qtdOrders' => $user->client->orders->count()]));   
            }
        } catch (ModelNotFoundException $e) {
            Session::flash('error', trans('crud.record_not_found', ['action' => 'deleted']));
        }

        return redirect()->route('admin.user.index');
    }
}
