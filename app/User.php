<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function meta(){
        return $this->hasOne(UserMeta::class);
    }

    public function galleries(){

        return $this->hasMany(Gallery::class);
    }

    public function getUpdatedAtAttribute($value){

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->diffForHumans();
    }

    public function scopeFilter($query, $filter = []){

        if(empty($filter))
            return $query;

            $caste = $marital_status = $manglik = $religion = [];

            $caste = $this->hasFilter($filter,'caste');
            $marital_status = $this->hasFilter($filter,'marital_status'); 
            $manglik = $this->hasFilter($filter,'manglik');
            $religion =  $this->hasFilter($filter,'religion');

            if(empty($caste) && empty($marital_status) && empty($manglik) && empty($religion))
                return $query;

            $query = $query->whereHas('meta', function ($query) use ($caste, $marital_status, $manglik, $religion){
                if(!empty($caste))
                    $query->whereIn('caste_id', $caste);

                if(!empty($marital_status))
                    $query->whereIn('marital_status_id', $marital_status);

                if(!empty($manglik))
                    $query->whereIn('manglik_id', $manglik);

                if(!empty($religion))
                    $query->whereIn('religion_id', $religion);
            });

            return $query;
    }

    private function hasFilter($data, $key, $items = []){

        if(isset($data[$key])):

            foreach($data[$key] as $itemKey => $item):

                if($item == false)
                continue;

                $items[] = $itemKey;
            endforeach;

        endif;

        return $items;
    }

    public function meta_item($item, $column = 'name'){

        if(isset($this->meta->{$item}->{$column}))
            return $this->meta->{$item}->{$column};
    }
}