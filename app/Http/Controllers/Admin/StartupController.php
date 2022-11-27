<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Partner;
use App\Startup;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class StartupController extends Controller
{

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $builder = Startup::with('partner');
        if($request->has('partner_id') && !empty($request->partner_id)) {
            $builder->where('partner_id', $request->partner_id);
        }
        $startups = $builder->paginate();
        return view('admin.startups.index',[
            'startups' => $startups
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $partner_id = "";
        if(!empty(Input::get('partner_id'))) {
           $partner_id = Input::get('partner_id');
        }
        
        $partners = Partner::pluck('name', 'id');
        return view('admin.startups.create', [
            'partners' => $partners,
            "partner_id" => $partner_id
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
            'name' => 'required|max:255|unique:startups',
        ];
        $request->validate($validationArr);
        Startup::create($request->only('name', 'partner_id'));

        return redirect(route('admin.startups.index'))->with('status', 'Startup has been added successfully');
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
        $startup = Startup::find($id);
        return view('admin.startups.edit', [
            'partners' => Partner::pluck('name', 'id'),
            'startup' => $startup
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
        $startup = Startup::find($id);
        $validationArr = [
            'name' => 'required|max:255|unique:startups,name,'.$startup->id,
        ];
        $request->validate($validationArr);
        $startup->fill([
            'name' => $request->name,
            'partner_id' => $request->partner_id
        ]);
        $startup->save();

        return redirect(route('admin.startups.index'))->with('status', 'Startup has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $startup = Startup::find($id);
        $startup->delete();
        return redirect(route('admin.startups.index'))->with('status', 'Startup has been deleted successfully');
    }
}
