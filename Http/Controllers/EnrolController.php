<?php

namespace Modules\Admission\Http\Controllers;

use App\Models\User;
use App\Models\State;
use App\Models\Branch;
use App\Models\Session;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Admission\Entities\Enrol;
use Illuminate\Contracts\Support\Renderable;

class EnrolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $branch=null)
    {
        if($request->branch){
            return redirect()->route('enroll.create', $branch);
        }
        $branches = Branch::get();
        $sessions = Session::get();
        $states = State::get();
        return view('enroll.create', compact(['branches', 'sessions', 'states']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // return $request;
        $request->validate([
            'section'=>'required|numeric',
            // 'regno'=>'required|string',
            // 'rollno'=>'required|numeric',
            'enrol_date'=>'required|string',
            'department'=>'required|numeric',
            'class'=>'required|numeric',
            'state'=>'required|numeric',
            'lga'=>'required|numeric',
            'firstname'=>'required|string',
            'lastname'=>'required|string',
            'middlename'=>'sometimes|nullable|string',
            'gender'=>'required|string',
            'dob'=>'required|string',
            'religion'=>'required|string',
            'bloodgroup'=>'required|string',
            'genotype'=>'required|string',
            'mothertongue'=>'sometimes|nullable|string',
            'email'=>'required|string',
            'mobile'=>'sometimes|nullable|string',
            'city'=>'required|string',
            'password'=>'required|confirmed',
            'address'=>'required|string',
            // 'guardian_id'=>'required_without_all:
            // guardian_firstname,guardian_lastname,guardian_email,guardian_state,
            // guardian_mobile, guardian_lga,guardian_gender, guardian_religion,
            // guardian_city, guardian_address, guardian_relationship, guardian_password',
            // 'guardian_avatar' => 'sometimes|nullable|image|mimes:png,jpg',
            // 'guardian_state' => 'required_without:guardian_id|nullable|numeric',
            // 'guardian_mobile' => 'required_without:guardian_id|nullable|numeric',
            // 'guardian_lga' => 'required_without:guardian_id|nullable|numeric',
            // 'guardian_firstname' => 'required_without:guardian_id|string',
            // 'guardian_lastname' => 'required_without:guardian_id|string',
            // 'guardian_gender' => 'required_without:guardian_id|string',
            // 'guardian_religion' => 'required_without:guardian_id|string',
            // 'guardian_email' => 'required_without:guardian_id|email',
            // 'guardian_city' => 'required_without:guardian_id|string',
            // 'guardian_address' => 'required_without:guardian_id|string',
            // 'guardian_password' => 'required_without:guardian_id|confirmed',
            // 'guardian_relationship' => 'required_without:guardian_id|string',


        ]);
        function get_reg_number ($short_name, $last_student) { 
            if (empty($last_student)) {
                return $short_name . ' - 1001';
            }else {
                $last_student_id = $last_student->id + 1001;
                return $short_name . ' - ' . $last_student_id;
            }
        }

        $short_name = Branch::where('id', $request->branch)->first()->short_name;
        $last_student = Student::latest()->first();
        $reg_number = get_reg_number ($short_name, $last_student);

        $user = new User();
        $user->email = $request->email;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->middlename = $request->middlename;
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->file("avatar")) {
            $fileName = str_replace(" ", "_", $request->firstname . " " . $request->lastname);
            $user->addMediaFromRequest('avatar')->usingFileName($fileName)->toMediaCollection("avatar");
        }


        if($request->gexist=="on"){
            $guardian = Guardian::with(['user', 'branch', 'students', 'state', 'lga'])->where('id', $request->guardian_id)->first();
        }else{

            $guardian_user = new User();
            $guardian_user->email = $request->guardian_email;
            $guardian_user->firstname = $request->guardian_firstname;
            $guardian_user->lastname = $request->guardian_lastname;
            $guardian_user->middlename = $request->guardian_middlename;
            $guardian_user->password = Hash::make($request->guardian_password);
            $guardian_user->save();

            if ($request->file("guardian_avatar")) {
                $fileName = str_replace(" ", "_", $request->guardian_firstname . " " . $request->guardian_firstname);
                $user->addMediaFromRequest('guardian_avatar')->usingFileName($fileName)->toMediaCollection("avatar");
            }
            $user->assignRole(6);
            $guardian = new Guardian();
            $guardian->user_id = $guardian_user->id;
            $guardian->mobile = $request->guardian_mobile;
            $guardian->city = $request->guardian_city;
            $guardian->state_id = $request->guardian_state;
            $guardian->local_government_id = $request->guardian_lga;
            $guardian->address = $request->guardian_address;
            $guardian->gender = $request->guardian_gender;
            $guardian->religion = $request->guardian_religion;
            $guardian->branch_id = $request->branch;
            $guardian->occupation = $request->occupation;
            $guardian->relationship = $request->guardian_relationship;
            $guardian->save();
        }
        $user->assignRole(7);
        $enrol = new Enrol();
        $enrol->user_id = $user->id;
        // $enrol->enrol_date = $user->enrol_date;
        $enrol->guardian_id = $guardian->id;
        $enrol->mobile = $request->mobile;
        $enrol->blood_group = $request->bloodgroup;
        $enrol->session_id = $request->session;
        $enrol->genotype = $request->genotype;
        $enrol->mothertongue = $request->mothertongue;
        $enrol->mobile = $request->mobile;
        $enrol->city = $request->city;
        $enrol->dob = date('Y-m-d',strtotime($request->dob));
        $enrol->state_id = $request->state;
        $enrol->local_government_id = $request->lga;
        $enrol->address = $request->address;
        $enrol->gender = $request->gender;
        $enrol->religion = $request->religion;
        $enrol->branch_id = $request->branch;
        $enrol->department_id = $request->department;
        $enrol->regno =   $reg_number;//$request->regno;
        $enrol->rollno =$request->rollno;  //$request->section;
        $enrol->section_id = $request->section;
        $enrol->s_class_id = $request->class;
        $enrol->enrol_date = date('Y-m-d',strtotime($request->enrol_date));
        $enrol->save();




        $student = new Student();
        $student->user_id = $user->id;
        $student->enrol_id = $enrol->id;
        $student->enrol_date = $user->enrol_date;
        $student->guardian_id = $guardian->id;
        $student->mobile = $request->mobile;
        $student->blood_group = $request->bloodgroup;
        $student->session_id = $request->session;
        $student->genotype = $request->genotype;
        $student->mothertongue = $request->mothertongue;
        $student->mobile = $request->mobile;
        $student->city = $request->city;
        $student->dob = date('Y-m-d',strtotime($request->dob));
        $student->state_id = $request->state;
        $student->local_government_id = $request->lga;
        $student->address = $request->address;
        $student->gender = $request->gender;
        $student->religion = $request->religion;
        $student->branch_id = $request->branch;
        $student->department_id = $request->department;
        $student->regno =   $reg_number;//$request->regno;
        $student->rollno = $request->rollno;  //$request->section;
        $student->section_id = $request->section;
        $student->s_class_id = $request->class;
        $student->enrol_date = date('Y-m-d',strtotime($request->enrol_date));
        $student->save();


        return redirect()->route('student.profile', $student->id)->with('success', 'New Enrolment added successfully');
        // return $request;
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
