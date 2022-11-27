<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Partner;
use App\Startup;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    
    public function index() {
        $partners = Partner::withCount('startups')->paginate();
        return view('admin.partners.index', [
            'partners' => $partners
        ]);
    }

    public function refresh() {
        $sites = DB::connection('admin')->table('sites')->where('site_type', '!=', "")->get();
        $emrPartners = $sites->pluck('site_type')->unique()->toArray();
        $passed = [];
        if(!empty($emrPartners)) {
            foreach($emrPartners as $partner) {
                $getPartner = Partner::firstOrNew([
                    'name' => $partner
                ]);
                $getPartner->save();
                $passed[] = $getPartner->id;
            }
        }
        
        Startup::whereNotIn('partner_id', $passed)->delete();
        Partner::whereNotIn('id', $passed)->delete();
        
        return redirect(route('admin.partners.index'))->with('status', 'Successfully Refreshed!');

    }

    public function edit($id) {
        $partner = Partner::find($id);

        return view('admin.partners.edit', [
            'partner' => $partner
        ]);
    } 

    public function update(Request $request, $id) {
        $partner = Partner::find($id);
        $olderName = $partner;

        $validationArr = [
            'name' => 'required|max:255|unique:partners,name,'.$partner->id,
        ];
        $request->validate($validationArr);

        $partner->fill([
            'name' => $request->name,
            'show_on_app' => $request->show_on_app,
        ]);
        $partner->save();

        // DENIED FOR MAPI user, as it has no Update Permission.

        // DB::connection("admin")->table("sites")->where([
        //     'site_type'=>$olderName
        // ])->update([
        //     "name" => $request->name
        // ]);
       

        return redirect(route('admin.partners.index'))->with('status', 'Partner has been updated succesfully!');
    }
}
