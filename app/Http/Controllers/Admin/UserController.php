<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;
use App\User;

class UserController extends Controller
{
    public $admin, $project;


    public function __construct()
    {
        $this->admin = new Admin;
        $this->project = new Project;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate();

        $states = $this->admin->getStates(101);

        return view('admin.users.index', [
            'users' => $users,
            'states' => $states
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = $this->admin->getStates(101);
        $sites = $this->admin->getSites()->sortBy('siteid', SORT_NATURAL|SORT_FLAG_CASE)->pluck('site_title', 'siteid');
        return view('admin.users.create', [
            'states' => $states,
            'sites' => $sites
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|confirmed|min:6',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password)
        ]);

        if(!empty($request->role)) {
            $user->assignRole($request->role);
            $user->saveAssets($request);
        }

        return redirect(route('admin.users.index'))->with('status', 'User has been created succesfully!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $states = $this->admin->getStates(101);
        $sites = $this->admin->getSites()->sortBy('siteid', SORT_NATURAL|SORT_FLAG_CASE)->pluck('site_title', 'siteid');

        $user = User::find($id);

        return view('admin.users.edit', [
            'user' => $user,
            'states' => $states,
            'sites' => $sites
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $validationArr = [
            'name' => 'required|max:255',
            'email' => 'required|unique:users,email,'.$user->id,
            'username' => 'required|unique:users,username,'.$user->id,
            'role' => 'required',
        ];
        if(!empty(trim($request->password))) {
            $validationArr['password'] = 'min:6';
        }
        $request->validate($validationArr);

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username
        ]);
        $user->save();
        if(!empty(trim($request->password))) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        $user->syncRoles([$request->role]);
        $user->saveAssets($request);

        return redirect(route('admin.users.index'))->with('status', 'User has been updated succesfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->syncRoles([]);
        $user->delete();
        return redirect(route('admin.users.index'))->with('status', 'User has been deleted succesfully!');
    }
}
