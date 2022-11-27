<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    protected $guarded = [];
    protected $table  = 'studio';

    public function assets() {
        return $this->hasMany('App\StudioAsset', 'studio_id');
    }

    public function saveAssets($data, $type) {
        $this->realAssetsByType($type)->delete();

        if(!empty($data)) {
            foreach($data as $d) {
                $assetM = StudioAsset::create([
                    'studio_id' => $this->id,
                    'object_id' => $d,
                    'object_type' => $type
                ]);
            }
        }

    }

    public function realAssetsByType($type) {
        return $this->assets()->where('object_type', $type);
    }
}
