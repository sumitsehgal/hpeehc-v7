<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utils\Admin;
use App\Http\Utils\Project;
use App\Studio;

class StudioController extends Controller
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
        $studios = Studio::paginate();
        return view('admin.studio.index',[
            'studios' => $studios
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
        $partners = $this->admin->getPartners();
        return view('admin.studio.create', [
            'states' => $states,
            'partners' => $partners
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
        $validationArr = [
            'name' => 'required|max:255|unique:studio',
            'stats' => 'required|numeric'
        ];
        $request->validate($validationArr);
        $studio = Studio::create($request->only('name', 'stats'));
        if($request->has('state')) {
            $studio->saveAssets($request->state, 'state');
        }

        if($request->has('partner')) {
            $studio->saveAssets($request->partner, 'partner');
        }


        return redirect(route('admin.studio.index'))->with('status', 'Studio has been added successfully');
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
        $studio = Studio::find($id);

        $states = $this->admin->getStates(101);
        $partners = $this->admin->getPartners();

        return view('admin.studio.edit', [
            'studio' => $studio,
            'states' => $states,
            'partners' => $partners
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
        $studio = Studio::find($id);
        $validationArr = [
            'name' => 'required|max:255|unique:studio,name,'.$studio->id,
            'stats' => 'required|numeric',
        ];
        $request->validate($validationArr);
        $studio->fill([
            'name' => $request->name,
            'stats' => $request->stats
        ]);
        $studio->save();

        if($request->has('state')) {
            $studio->saveAssets($request->state, 'state');
        }

        if($request->has('partner')) {
            $studio->saveAssets($request->partner, 'partner');
        }

        return redirect(route('admin.studio.index'))->with('status', 'Studio has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $studio = Studio::find($id);
        $studio->assets()->delete();
        $studio->delete();
        return redirect(route('admin.studio.index'))->with('status', 'Studio has been deleted successfully');
    }
}
