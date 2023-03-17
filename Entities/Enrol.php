<?php

namespace Modules\Admission\Entities;

use App\Models\User;
use App\Models\State;
use App\Models\Branch;
use App\Models\SClass;
use App\Models\Section;
use App\Models\Session;
use App\Models\Guardian;
use App\Models\Department;
use App\Models\LocalGovernment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrol extends Model
{
    use HasFactory;

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function guardian(){
        return $this->belongsTo(Guardian::class);
    }

    public function dept(){
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function class(){
        return $this->belongsTo(SClass::class, 's_class_id');
    }


    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function lga(){
        return $this->belongsTo(LocalGovernment::class, 'local_government_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function state(){
        return $this->belongsTo(State::class);
    }

    public function session(){
        return $this->belongsTo(Session::class);
    }



    public function getEnrolDateAttribute($value){
        return date('M, d Y', strtotime($value));
    }

    public function getCreatedAtAttribute($value){
        return date('M, d Y', strtotime($value));
    }



}

